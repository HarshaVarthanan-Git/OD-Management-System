<?php
session_start();
require 'db.php';

// Check if the user is logged in and has the 'mentor' role
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'mentor') {
    header("Location: index.php");
    exit();
}

// Get mentor's username from session
$mentor_username = $_SESSION['username'];

// Fetch mentor's ID from the database
$sql_mentor_id = "SELECT id FROM users WHERE username = '$mentor_username'";
$result_mentor_id = mysqli_query($conn, $sql_mentor_id);
$row = mysqli_fetch_assoc($result_mentor_id);
$mentor_id = $row['id'];

// Fetch pending OD requests for the logged-in mentor
$sql = "SELECT * FROM od_requests WHERE mentor_id = $mentor_id AND mentor_approval_status = 'pending'";
$result = mysqli_query($conn, $sql);
$requests = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Handle form submission for approving or rejecting requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $approval_status = $_POST['approval_status'];
    $mentor_remark = $_POST['mentor_remark'];

    // Validate the approval status
    if ($approval_status != 'approved' && $approval_status != 'rejected') {
        echo "Invalid approval status.";
        exit();
    }

    // Determine the status based on the approval status
    $status = ($approval_status == 'rejected') ? 'rejected' : 'pending';

    // Update the request with the approval status and mentor remark
    $sql_update = "UPDATE od_requests 
                   SET mentor_approval_status = '$approval_status', 
                       mentor_remark = '$mentor_remark', 
                       status = '$status' 
                   WHERE id = $request_id";

    if (mysqli_query($conn, $sql_update)) {
        // Redirect or show success message
        header("Location: mentor_approval.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>Mentor Approval</title>
    <link rel="stylesheet" href="css/responsive.css">
    <style>
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
            width: 100%;
            max-width: 1200px;
        }
        .title {
            color: #87cefa;
            font-size: 2em;
            margin-bottom: 20px;
        }
        .table-container {
            background-color: #f0f0f0;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            overflow-x: auto; /* Enable horizontal scrolling */
            display: flex;
            justify-content: center;
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
            margin: 5px 0; /* Adjust as needed for vertical space */
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            font-size: 0.9em;
            background-color: #1e90ff; /* Dark blue background */
            color: white; /* White text */
        }
        .action-buttons button:hover {
            background-color: #0066cc; /* Darker shade of blue on hover */
            transform: scale(1.05);
        }
        textarea {
            width: 100%;
            height: 60px;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }
        .no-requests {
            text-align: center;
            color: #555;
            font-size: 1.2em;
            margin-top: 20px;
        }
        .proof-link {
            color: #1e90ff;
            text-decoration: none;
            font-size: 0.9em;
        }
        .proof-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/profile.jpg" alt="Profile Image">
        <div class="welcome">Welcome, <?php echo $_SESSION['name']; ?></div>
        <button class="logout" onclick="window.location.href='mentor_dashboard.php'">Back to Home</button>
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
                            <th>Events</th>
                            <th>Link for Proof</th>
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
                                <td><?php echo $request['events']; ?></td>
                                <td><a href="<?php echo $request['proof_link']; ?>" class="proof-link" target="_blank">View Proof</a></td>
                                <td class="action-buttons">
                                    <form method="post" action="">
                                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                        <textarea name="mentor_remark" placeholder="Enter your remarks here" required></textarea><br>
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
