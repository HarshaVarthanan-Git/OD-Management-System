<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit();
}

$rollNo = $_SESSION['username'];
$studentName = $_SESSION['name'];
$yearOfStudy = "3rd Year"; // Assuming all students are in their 3rd year
$batch = "2022-2026"; // Common batch for all students

// Fetch the approved OD requests for the student
$sql = "SELECT * FROM od_requests WHERE roll_no = '$rollNo' AND status = 'approved' AND certificate_uploaded = 1";
$result = mysqli_query($conn, $sql);

function getDetails($row) {
    $details = [];
    if (!empty($row['paper_presentation_details'])) {
        $details[] = 'Paper Presentation: ' . htmlspecialchars($row['paper_presentation_details']);
    }
    if (!empty($row['project_details'])) {
        $details[] = 'Project: ' . htmlspecialchars($row['project_details']);
    }
    if (!empty($row['other_event_details'])) {
        $details[] = 'Other Events: ' . htmlspecialchars($row['other_event_details']);
    }
    return implode(', ', $details);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Uploaded Details</title>
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .header {
            width: 100%;
            padding: 1em;
            background-color: #fff;
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
            background-color: #4a90e2;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .header .logout:hover {
            background-color: #357abd;
        }
        .content {
            text-align: center;
            margin-top: 20px;
            width: 90%;
            max-width: 800px;
        }
        .title {
            color: #4a90e2;
            font-size: 2em;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            text-align: left;
            padding: 12px;
        }
        th {
            background-color: #f7f7f7;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .message {
            color: green;
            margin-top: 10px;
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/profile.jpg" alt="Profile Image">
        <div class="welcome">Welcome, <?php echo htmlspecialchars($studentName); ?></div>
        <button class="logout" onclick="window.location.href='student_dashboard.php'">Back to Home</button>
    </div>
    <div class="content">
        <div class="title">OD History</div>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <table>
                <tr>
                    <th>S.No</th>
                    <th>Company/College Name</th>
                    <th>Programme/Event Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
                <?php 
                $sno = 1;
                while ($row = mysqli_fetch_assoc($result)): 
                    $details = getDetails($row);
                ?>
                    <tr>
                        <td><?php echo $sno++; ?></td>
                        <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['program_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No approved OD requests found.</p>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
