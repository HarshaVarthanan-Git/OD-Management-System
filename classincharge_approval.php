<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require 'db.php';

if (!isset($_SESSION['username']) || ($_SESSION['role'] != 'class_incharge' && $_SESSION['role'] != 'mentor')) {
    header("Location: index.php");
    exit();
}

$cnn = $_SESSION['username'];
$sql = "SELECT * FROM od_requests 
        WHERE mentor_approval_status = 'approved' 
        AND classincharge_approval_status = 'pending' 
        AND classincharge_uname = '$cnn'";
$result = mysqli_query($conn, $sql);
$requests = mysqli_fetch_all($result, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id']) && isset($_POST['approval_status']) && isset($_POST['remarks'])) {
    $requestId = $_POST['request_id'];
    $approvalStatus = $_POST['approval_status'];
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']); // Sanitize input

    // Update status based on the approval status
    $statusUpdate = $approvalStatus == 'rejected' ? 'rejected' : 'pending';

    $updateSql = "UPDATE od_requests 
                  SET classincharge_approval_status = '$approvalStatus', 
                      classincharge_remarks = '$remarks',
                      status = '$statusUpdate'
                  WHERE id = '$requestId'";

    if (mysqli_query($conn, $updateSql)) {
        echo "<script>alert('OD request approval updated successfully.'); window.location.href='classincharge_approval.php';</script>";
    } else {
        echo "Error: " . $updateSql . "<br>" . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Class Incharge Approval</title>
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        /* Your existing CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .header {
            width: 100%;
            padding: 1em;
            background-color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
        }
        .header img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        .header .welcome {
            font-size: 1.2em;
            font-weight: bold;
            margin-left: 10px;
        }
        .header .logout {
            background-color: #87cefa;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .header .logout:hover {
            background-color: #00bfff;
        }
        .content {
            text-align: center;
            margin-top: 20px;
        }
        .title {
            color: #87cefa;
            font-size: 2em;
            margin-bottom: 20px;
        }
        .table-container {
            width: 100%;
            max-width: 1200px; 
            background-color: #f0f0f0;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            overflow-x: auto; 
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #87cefa;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .action-buttons button {
            margin: 5px 0; 
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            font-size: 0.9em;
            background-color: #1e90ff; 
            color: white; 
        }

        .action-buttons button:hover {
            background-color: #0066cc; 
            transform: scale(1.05);
        }
        .no-requests {
            margin-top: 20px;
            color: #888;
        }
        .proof-link {
            color: #1e90ff;
            text-decoration: none;
        }
        .proof-link:hover {
            text-decoration: underline;
        }
        .remarks {
            margin-top: 10px;
            font-size: 0.9em;
        }
        .remarks textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1em;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/profile.jpg" alt="Profile Image">
        <div class="welcome">Welcome, <?php echo $_SESSION['name']; ?></div>
        <button class="logout" onclick="window.location.href='class_incharge_dashboard.php'">Back to home</button>
    </div>
    <div class="content">
        <div class="title">OD Approval</div>
        <div class="table-container">
            <?php if (empty($requests)): ?>
                <p class="no-requests">No OD requests pending for approval.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Student Name</th>
                            <th>Company/College</th>
                            <th>Program/Event</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Paper Presentation Details</th> 
                            <th>Project Details</th> 
                            <th>Other Event Details</th> 
                            <th>Link for Proof</th>
                            <th>Mentor Remarks</th> <!-- New column for mentor remarks -->
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $request): ?>
                            <tr>
                                <td><?php echo $request['id']; ?></td>
                                <td><?php echo $request['student_name']; ?></td>
                                <td><?php echo $request['company_name']; ?></td>
                                <td><?php echo $request['program_name']; ?></td>
                                <td><?php echo $request['start_date']; ?></td>
                                <td><?php echo $request['end_date']; ?></td>
                                <td><?php echo !empty($request['PaperPresentationDetails']) ? $request['PaperPresentationDetails'] : '-'; ?></td> 
                                <td><?php echo !empty($request['ProjectDetails']) ? $request['ProjectDetails'] : '-'; ?></td> 
                                <td><?php echo !empty($request['OtherEventDetails']) ? $request['OtherEventDetails'] : '-'; ?></td> 
                                <td><a href="<?php echo $request['proof_link']; ?>" class="proof-link" target="_blank">View Proof</a></td>
                                <td><?php echo !empty($request['mentor_remark']) ? $request['mentor_remark'] : '-'; ?></td> <!-- Display mentor remarks -->
                                <td class="action-buttons">
                                    <form method="post" action="classincharge_approval.php">
                                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                        <textarea name="remarks" placeholder="Enter remarks here..." rows="3"></textarea>
                                        <br>
                                        <button type="submit" name="approval_status" value="approved">Approve</button>
                                        <button type="submit" name="approval_status" value="rejected">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
