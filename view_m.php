<?php
session_start();
require 'db.php';

// Ensure the user is logged in as a mentor
if (!isset($_SESSION['username']) || ($_SESSION['role'] != 'class_incharge')) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$mentorName = $_SESSION['name'];

// Fetch mentor ID from the users table
$sqlFetchMentorId = "SELECT id FROM users WHERE username = '$username' AND role = 'class_incharge'";
$resultFetchMentorId = $conn->query($sqlFetchMentorId);

if ($resultFetchMentorId->num_rows > 0) {
    $mentorId = $resultFetchMentorId->fetch_assoc()['id'];
} else {
    echo "Mentor ID not found!";
    exit();
}

// Fetch mentees for the mentor
$sqlFetchMentees = "SELECT name FROM users WHERE mentor_id = '$mentorId'";
$resultFetchMentees = $conn->query($sqlFetchMentees);

$mentees = [];
if ($resultFetchMentees->num_rows > 0) {
    while ($row = $resultFetchMentees->fetch_assoc()) {
        $mentees[] = $row['name'];
    }
} else {
    echo "No mentees found!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Mentees</title>
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
            width: 100%;
            box-sizing: border-box;
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
            height: calc(100vh - 80px); /* Adjust height to fill remaining space */
            position: fixed;
            top: 80px; /* Align with the bottom of the header */
            left: 0;
            padding-top: 20px;
            box-sizing: border-box;
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
            flex-grow: 1;
            margin-top: 80px; /* Align with the bottom of the header */
            box-sizing: border-box;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px; /* Reduced space above the table */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007acc;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .footer {
            background-color: #007acc;
            color: white;
            text-align: center;
            padding: 10px 0;
            width: 100%;
            box-sizing: border-box;
            position: sticky;
            bottom: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/profile.jpg" alt="Profile Image">
        <div class="welcome">Welcome, <?php echo $mentorName; ?></div>
        <button class="logout" onclick="window.location.href='class_incharge_dashboard.php'">Back to Home</button>
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
        <h1>Mentees</h1>
        <table>
            <thead>
                <tr>
                    <th>S. No</th>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($mentees)) { ?>
                    <?php foreach ($mentees as $index => $mentee) { ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo $mentee; ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="2">No mentees found!</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <?php include 'footer.php'; ?>
    </div>
</body>
</html>



