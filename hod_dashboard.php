<?php
session_start();
require 'db.php';

if (!isset($_SESSION['name']) || $_SESSION['role'] != 'hod') {
    echo "Session variables not set or role is not HOD. Redirecting to index.php";
    header("Location: index.php");
    exit();
}

$hodName = $_SESSION['name'];

// Total number of students with hod_id 1
$sqlTotalStudents = "SELECT COUNT(*) AS count_students FROM users WHERE hod_id = 1";
$resultTotalStudents = mysqli_query($conn, $sqlTotalStudents);
$countTotalStudents = mysqli_fetch_assoc($resultTotalStudents)['count_students'];

// Total OD requests
$sqlTotalODRequests = "SELECT COUNT(*) AS count_total_requests FROM od_requests";
$resultTotalODRequests = mysqli_query($conn, $sqlTotalODRequests);
$countTotalODRequests = mysqli_fetch_assoc($resultTotalODRequests)['count_total_requests'];

// Total ODs approved
$sqlTotalApproved = "SELECT COUNT(*) AS count_total_approved FROM od_requests WHERE status = 'approved'";
$resultTotalApproved = mysqli_query($conn, $sqlTotalApproved);
$countTotalApproved = mysqli_fetch_assoc($resultTotalApproved)['count_total_approved'];

// Total ODs rejected
$sqlTotalRejected = "SELECT COUNT(*) AS count_total_rejected FROM od_requests WHERE status = 'rejected'";
$resultTotalRejected = mysqli_query($conn, $sqlTotalRejected);
$countTotalRejected = mysqli_fetch_assoc($resultTotalRejected)['count_total_rejected'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOD Dashboard</title>
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7f9;
        }

        .header {
            width: 100%;
            padding: 20px;
            background-color: #003d7a; /* Dark blue for header */
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .header .welcome {
            font-size: 1.5em;
            font-weight: bold;
        }

        .header .logout {
            background-color: #ffffff;
            color: #003d7a;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 1em;
            transition: background-color 0.3s, transform 0.3s;
        }

        .header .logout:hover {
            background-color: #e0e0e0;
            transform: scale(1.05);
        }

        .sidebar {
            width: 250px;
            background-color: #003d7a; /* Match header color */
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px;
            position: fixed;
            top: 80px; /* Adjusted to be below the header */
            bottom: 0;
            height: calc(100% - 80px);
            box-shadow: 2px 0 4px rgba(0, 0, 0, 0.1);
            z-index: 2;
        }

        .sidebar h2 {
            margin-top: 0;
            font-size: 1.5em;
        }

        .sidebar button {
            background-color: #ffffff;
            color: #003d7a; /* Match header color */
            border: none;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            text-align: left;
            width: 100%;
            transition: background-color 0.3s, transform 0.3s;
        }

        .sidebar button:hover {
            background-color: #002d62; /* Darker blue */
            color: white;
            transform: scale(1.05);
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            margin-top: 80px; /* Adjusted for fixed header */
        }

        .dashboard {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .box {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: calc(50% - 20px); /* Two boxes per row with gap */
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .box:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .box h3 {
            margin: 0;
            font-size: 1.2em;
            color: #333;
        }

        .box p {
            font-size: 1.5em;
            color: #555;
        }

        @media (max-width: 768px) {
            .box {
                width: calc(100% - 20px); /* Full width on smaller screens */
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/profile.jpg" alt="Profile Image">
        <div class="welcome">Welcome, <?php echo htmlspecialchars($hodName); ?></div>
        <button class="logout" onclick="window.location.href='logout.php'">Logout</button>
    </div>
    <div class="sidebar">
        <h2>Menu</h2>
        <button onclick="window.location.href='hod_approval.php'">OD Requests</button>
        <button onclick="window.location.href='hod_check_defaulter.php'">Check Defaulters</button>
        <button onclick="window.location.href='uploaded_details.php'">View Uploaded Details</button>
        <button onclick="window.location.href='report.php'">Report Generation</button>
    </div>
    <div class="content">
        <div class="dashboard">
            <div class="box">
                <h3>Total Number of Students </h3>
                <p><?php echo htmlspecialchars($countTotalStudents); ?></p>
            </div>
            <div class="box">
                <h3>Total OD Requests</h3>
                <p><?php echo htmlspecialchars($countTotalODRequests); ?></p>
            </div>
            <div class="box">
                <h3>Total ODs Approved</h3>
                <p><?php echo htmlspecialchars($countTotalApproved); ?></p>
            </div>
            <div class="box">
                <h3>Total ODs Rejected</h3>
                <p><?php echo htmlspecialchars($countTotalRejected); ?></p>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
