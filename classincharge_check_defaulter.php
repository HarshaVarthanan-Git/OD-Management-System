<?php
session_start();
require 'db.php';

// Ensure user is logged in and has class_incharge role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'class_incharge') {
    header('Location: login.php');
    exit();
}

$classincharge_uname = $_SESSION['username']; // Get the logged-in class in-charge's username

$sql = "SELECT * FROM od_requests WHERE status='approved' AND certificate_uploaded='FALSE' AND classincharge_uname='$classincharge_uname'";
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
            margin: 0;
            padding: 0;
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
            width: 90%;
            max-width: 1200px;
            margin-top: 20px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        table th, table td {
            padding: 15px;
            text-align: left;
        }
        table th {
            background-color: #87cefa;
            color: white;
            font-weight: bold;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:nth-child(odd) {
            background-color: #ffffff;
        }
        table tr:hover {
            background-color: #e0f7fa;
        }
        .no-results {
            font-size: 1.2em;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/profile.jpg" alt="Profile Image">
        <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></div>
        <button class="logout" onclick="window.location.href='class_incharge_dashboard.php'">Back to home</button>
    </div>
    <div class="content">
        <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Roll Number</th>
                    <th>Name</th>
                    <th>Company Attended</th>
                    <th>Program Attended</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Event</th>
                    <th>Paper Presentation Details</th>
                    <th>Project Details</th>
                    <th>Other Event Details</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo !empty($row['roll_no']) ? htmlspecialchars($row['roll_no']) : '-'; ?></td>
                    <td><?php echo !empty($row['student_name']) ? htmlspecialchars($row['student_name']) : '-'; ?></td>
                    <td><?php echo !empty($row['company_name']) ? htmlspecialchars($row['company_name']) : '-'; ?></td>
                    <td><?php echo !empty($row['program_name']) ? htmlspecialchars($row['program_name']) : '-'; ?></td>
                    <td><?php echo !empty($row['start_date']) ? htmlspecialchars($row['start_date']) : '-'; ?></td>
                    <td><?php echo !empty($row['end_date']) ? htmlspecialchars($row['end_date']) : '-'; ?></td>
                    <td><?php echo !empty($row['events']) ? htmlspecialchars($row['events']) : '-'; ?></td>
                    <td><?php echo !empty($row['PaperPresentationDetails']) ? htmlspecialchars($row['PaperPresentationDetails']) : '-'; ?></td>
                    <td><?php echo !empty($row['ProjectDetails']) ? htmlspecialchars($row['ProjectDetails']) : '-'; ?></td>
                    <td><?php echo !empty($row['OtherEventDetails']) ? htmlspecialchars($row['OtherEventDetails']) : '-'; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="no-results">No approved OD requests without certificates found.</p>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>

<?php $conn->close(); ?>
