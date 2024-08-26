<?php
session_start();
require 'db.php';
$sql = "SELECT * 
          FROM od_requests 
          WHERE status='approved'
          AND certificate_uploaded='FALSE' 
          AND roll_no IN (
              SELECT roll_number
              FROM users 
              WHERE mentor_id = {$_SESSION['user_id']}
          )";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved OD Requests without Certificate</title>
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
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
        table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #87cefa;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/profile.jpg" alt="Profile Image">
        <div class="welcome">Welcome, <?php echo $_SESSION['name']; ?></div>
        <button class="logout" onclick="window.location.href='mentor_dashboard.php'">Back to home</button>
    </div>
    <div class="content">
        <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Roll Number</th>
                <th>Name</th>
                <th>Company Attended</th>
                <th>Program Attended</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Event</th>
                <th>PaperPresentation Details</th>
                <th>Project Details</th>
                <th>Other Event Details</th>
                <th>Certificate Status</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['roll_no']; ?></td>
                <td><?php echo $row['student_name']; ?></td>
                <td><?php echo $row['company_name']; ?></td>
                <td><?php echo $row['program_name']; ?></td>
                <td><?php echo $row['start_date']; ?></td>
                <td><?php echo $row['end_date']; ?></td>
                <td><?php echo $row['events']; ?></td>
                <td><?php echo !empty($row['PaperPresentationDetails']) ? $row['PaperPresentationDetails'] : '-'; ?></td>
                <td><?php echo !empty($row['ProjectDetails']) ? $row['ProjectDetails'] : '-'; ?></td>
                <td><?php echo !empty($row['OtherEventDetails']) ? $row['OtherEventDetails'] : '-'; ?></td> 
                <td><?php echo $row['certificate_uploaded'] ? 'Certificate Uploaded' : 'Yet to be Uploaded'; ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
        <p>No approved OD requests without certificates found.</p>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
