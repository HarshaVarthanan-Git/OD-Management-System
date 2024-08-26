<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'hod') {
    header("Location: index.php");
    exit();
}

// Fetch OD requests where Class In-Charge has approved and HOD approval is pending
$sql = "SELECT * FROM od_requests WHERE classincharge_approval_status = 'approved' AND hod_approval_status = 'pending'";
$result = $conn->query($sql);
$requests = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>HOD Approval</title>
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        /* Add your styles here */
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
            width: 90%;
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
            color: #555;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/profile.jpg" alt="Profile Image">
        <div class="welcome">Welcome, <?php echo $_SESSION['name']; ?></div>
        <button class="logout" onclick="window.location.href='hod_dashboard.php'">Back Home</button>
    </div>
    <div class="content">
        <div class="title">HOD Approval</div>
        <div class="table-container">
            <?php if (empty($requests)): ?>
                <div class="no-requests">No pending OD requests found.</div>
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
                            <th>Class In-Charge Remarks</th>
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
                                <td><?php echo $request['classincharge_remarks']; ?></td>
                                <td class="action-buttons">
                                    <form method="post" action="hod_approval_process.php">
                                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
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
