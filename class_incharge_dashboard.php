<?php
session_start();
require 'db.php';
include 'responsive.php';

// Fetch staff username from session
$staffUsername = $_SESSION['username'];
$sname = $_SESSION['name'];


// Fetch staff ID using the username
$sql_staff_id = "SELECT id FROM users WHERE username = '$staffUsername'";
$result_staff_id = mysqli_query($conn, $sql_staff_id);
$staffId = mysqli_fetch_assoc($result_staff_id)['id'];

// Fetch statistics based on staff ID
// Number of mentees
$sql_mentees = "SELECT COUNT(*) AS mentee_count FROM users WHERE class_incharge_id = $staffId AND mentor_id=$staffId";
$result_mentees = mysqli_query($conn, $sql_mentees);
$mentees_count = mysqli_fetch_assoc($result_mentees)['mentee_count'];

// Number of OD requests
$sql_pending_od = "SELECT COUNT(*) AS pending_count FROM od_requests WHERE classincharge_uname = '$staffUsername' AND mentor_approval_status = 'approved' AND classincharge_approval_status = 'pending'";
$result_pending_od = mysqli_query($conn, $sql_pending_od);
$pending_od_count = mysqli_fetch_assoc($result_pending_od)['pending_count'];

$sql_approved_od = "SELECT COUNT(*) AS approved_count FROM od_requests WHERE classincharge_uname = '$staffUsername' AND classincharge_approval_status = 'approved'";
$result_approved_od = mysqli_query($conn, $sql_approved_od);
$approved_od_count = mysqli_fetch_assoc($result_approved_od)['approved_count'];

$sql_rejected_od = "SELECT COUNT(*) AS rejected_count FROM od_requests WHERE classincharge_uname = '$staffUsername' AND classincharge_approval_status = 'rejected'";
$result_rejected_od = mysqli_query($conn, $sql_rejected_od);
$rejected_od_count = mysqli_fetch_assoc($result_rejected_od)['rejected_count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class In-Charge Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .header {
            width: 100%;
            padding: 20px;
            background-color: #007acc;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        }

        .header .welcome {
            font-size: 1.2em;
            font-weight: bold;
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
            width: 250px;
            background-color: #007acc;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            height: calc(100vh - 80px); /* Full height minus header */
            position: fixed; /* Fixed position on the left */
            top: 80px; /* Adjusted to align below the header */
            left: 0;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar h2 {
            color: #ffffff;
            font-size: 1.5em;
            margin-top: 0;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            width: 100%;
        }

        .sidebar ul li {
            margin: 15px 0;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #ffffff;
            font-size: 1.2em;
            transition: background-color 0.3s, padding-left 0.3s;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
        }

        .sidebar ul li a:hover {
            background-color: #005f99;
            padding-left: 20px;
        }

        .sidebar ul li a::before {
            content: "📋"; /* Default icon, can be customized per menu item */
            margin-right: 10px;
        }

        .content {
            margin-left: 250px; /* Align content next to sidebar */
            padding: 20px;
            width: calc(100% - 250px); /* Full width minus sidebar width */
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); /* Responsive grid layout */
            gap: 20px;
            max-width: 1700px; /* Centered max width */
            width: 100%;
        }

        .card {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .card h3 {
            margin: 10px 0;
            color: #007acc;
        }

        .card p {
            font-size: 1.5em;
            margin: 0;
        }

        .card span {
            font-size: 2em;
            margin-bottom: 10px;
        }

        /* Responsive design for smaller screens */
        @media (max-width: 768px) {
            .sidebar {
                position: static; /* Sidebar will be positioned statically on small screens */
                width: 100%; /* Full width on small screens */
                height: auto; /* Height auto to fit the content */
            }

            .content {
                margin-left: 0; /* Remove margin-left to fit content */
                width: 100%; /* Full width on small screens */
            }

            .cards-container {
                grid-template-columns: 1fr; /* Single column on small screens */
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/profile.jpg" alt="Profile Image">
        <div class="welcome">Welcome, <?php echo $sname; ?></div>
        <button class="logout" onclick="window.location.href='logout.php'">Logout</button>
    </div>
    <div class="sidebar">
        <h2>Menu</h2>
        <ul>
            <li><a href="classincharge_approval.php">OD Requests</a></li>
            <li><a href="check_od_updates.php">OD Updates</a></li>
            <li><a href="classincharge_check_defaulter.php">Defaulters List</a></li>
            <li><a href="classincharge_uploaded_details.php">Uploaded Details</a></li>
            <li><a href="reportt.php">View Report</a></li>
            <li><a href="view_m.php">View Mentees</a></li>
        </ul>
    </div>
    <div class="content">
        <div class="cards-container">
            <div class="card">
                <span>👨‍🎓</span>
                <h3>Total Mentees</h3>
                <p><?php echo $mentees_count; ?></p>
            </div>
            <div class="card">
                <span>⏳</span>
                <h3>Pending OD Requests</h3>
                <p><?php echo $pending_od_count; ?></p>
            </div>
            <div class="card">
                <span>✅</span>
                <h3>Approved OD Requests</h3>
                <p><?php echo $approved_od_count; ?></p>
            </div>
            <div class="card">
                <span>❌</span>
                <h3>Rejected OD Requests</h3>
                <p><?php echo $rejected_od_count; ?></p>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
