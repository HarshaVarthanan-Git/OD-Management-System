<?php
session_start();
require 'db.php';

// Ensure the user is logged in as a mentor
if (!isset($_SESSION['username']) || ($_SESSION['role'] != 'mentor')) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$mentorName = $_SESSION['name'];

// Fetch mentor ID from the users table
$sqlFetchMentorId = "SELECT id FROM users WHERE username = '$username' AND role = 'mentor'";
$resultFetchMentorId = $conn->query($sqlFetchMentorId);

if ($resultFetchMentorId->num_rows > 0) {
    $mentorId = $resultFetchMentorId->fetch_assoc()['id'];
} else {
    echo "Mentor ID not found!";
    exit();
}

// Fetch relevant statistics for the mentor
// 1. Total OD Requests
$sqlTotalODRequests = "SELECT COUNT(*) as total FROM od_requests WHERE mentor_id = '$mentorId'";
$resultTotalODRequests = $conn->query($sqlTotalODRequests);
$totalODRequests = ($resultTotalODRequests->num_rows > 0) ? $resultTotalODRequests->fetch_assoc()['total'] : 0;

// 2. Total ODs Availed
$sqlTotalODAvailed = "SELECT COUNT(*) as total FROM od_requests WHERE mentor_id = '$mentorId' AND mentor_approval_status = 'approved'";
$resultTotalODAvailed = $conn->query($sqlTotalODAvailed);
$totalODAvailed = ($resultTotalODAvailed->num_rows > 0) ? $resultTotalODAvailed->fetch_assoc()['total'] : 0;

// 3. Total Mentees
$sqlTotalMentees = "SELECT COUNT(DISTINCT id) as total FROM users WHERE mentor_id = '$mentorId'";
$resultTotalMentees = $conn->query($sqlTotalMentees);
$totalMentees = ($resultTotalMentees->num_rows > 0) ? $resultTotalMentees->fetch_assoc()['total'] : 0;

// 4. Pending Approvals
$sqlPendingApprovals = "SELECT COUNT(*) as total FROM od_requests WHERE mentor_id = '$mentorId' AND mentor_approval_status = 'pending'";
$resultPendingApprovals = $conn->query($sqlPendingApprovals);
$totalPendingApprovals = ($resultPendingApprovals->num_rows > 0) ? $resultPendingApprovals->fetch_assoc()['total'] : 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentor Dashboard</title>
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
        }
        .header {
            padding: 20px;
            background-color: #007acc;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .header img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
        }
        .header .welcome {
            font-size: 1.5em;
            font-weight: bold;
            flex-grow: 1;
        }
        .header .logout {
            background-color: #ff6347;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .header .logout:hover {
            background-color: #ff4500;
        }
        .sidebar {
            width: 200px;
            background-color: #007acc;
            height: 100vh;
            position: fixed;
            top: 80px; /* Adjusted to be below the header */
            left: 0;
            padding-top: 20px;
        }
        .sidebar a {
            padding: 15px;
            text-align: center;
            display: block;
            color: white;
            text-decoration: none;
            font-size: 1.2em;
            margin-bottom: 10px;
            transition: background-color 0.3s;
        }
        .sidebar a:hover {
            background-color: #005f99;
        }
        .content {
            margin-left: 220px;
            padding: 20px;
            margin-top: 100px; /* Adjusted to ensure content starts below the header */
        }
        .container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            padding: 20px;
            max-width: 1200px;
            margin: 20px auto;
        }
        .box {
            background-color: white;
            width: 45%;
            padding: 20px;
            margin: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s;
        }
        .box:hover {
            transform: translateY(-5px);
        }
        .box h2 {
            margin: 0;
            font-size: 2em;
            color: #007acc;
        }
        .box p {
            font-size: 1.2em;
            color: #333;
        }
        .box-icon {
            font-size: 3em;
            color: #007acc;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/profile.jpg" alt="Profile Image">
        <div class="welcome">Welcome, <?php echo $mentorName; ?></div>
        <button class="logout" onclick="window.location.href='logout.php'">Logout</button>
    </div>

    <div class="sidebar">
    <a href="mentor_dashboard.php">Dashboard</a>
    <a href="mentor_approval.php">OD Requests</a>
    <a href="mentee_update.php">Check OD Updates</a>
    <a href="check_defaulters.php">Check Defaulters</a>
    <a href="mentee.php">View Uploaded Details</a>
    <a href="men_report.php">Report Generation</a>
    <a href="view_mentees.php">View Mentees</a> <!-- New Button -->
</div>


    <div class="content">
        <div class="container">
            <div class="box">
                <div class="box-icon">📄</div>
                <h2><?php echo $totalODRequests; ?></h2>
                <p>Total OD Requests</p>
            </div>
            <div class="box">
                <div class="box-icon">✅</div>
                <h2><?php echo $totalODAvailed; ?></h2>
                <p>Total ODs Granted</p>
            </div>
            <div class="box">
                <div class="box-icon">👥</div>
                <h2><?php echo $totalMentees; ?></h2>
                <p>Total Mentees</p>
            </div>
            <div class="box">
                <div class="box-icon">⏳</div>
                <h2><?php echo $totalPendingApprovals; ?></h2>
                <p>Pending Approvals</p>
            </div>
        </div>
    </div>
    <?php include 'footer.php' ?>
</body>
</html>
