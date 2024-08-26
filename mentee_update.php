<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username']) || ($_SESSION['role'] != 'mentor')) {
    header("Location: index.php");
    exit();
}
$mentorId = $_SESSION['user_id'];

// Fetch data from the students table
$sql = "SELECT student_name, roll_no, company_name, program_name, start_date, end_date, events, mentor_approval_status, classincharge_approval_status, hod_approval_status
        FROM od_requests
        WHERE mentor_id = '$mentorId'";
$result = $conn->query($sql);

// Initialize an array to store results
$odUpdates = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['mentor_approval_status'] == 'rejected') {
            $row['classincharge_approval_status'] = 'rejected';
            $row['hod_approval_status'] = 'rejected';
        }
        $odUpdates[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/responsive.css">
    <title>Check OD Updates</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #4a90e2;
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
        .btn-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }
        .btn-container a.button {
            background-color: #4a90e2;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 10px;
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn-container a.button:hover {
            background-color: #357abd;
            transform: scale(1.05);
        }
        p {
            text-align: center;
            font-size: 1.2em;
            color: #666;
        }
        .approved {
            color: green;
            font-weight: bold;
        }
        .rejected {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="btn-container">
        <a class="button" href="mentor_dashboard.php">Back to home</a>
    </div>
    <h1>OD Updates</h1>
    <?php if (empty($odUpdates)): ?>
        <p>Nothing to be displayed.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Roll No.</th>
                    <th>Student Name</th>
                    <th>Company Name</th>
                    <th>Program Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Events</th>
                    <th>Mentor Approval Status</th>
                    <th>Class Incharge Approval Status</th>
                    <th>HOD Approval Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($odUpdates as $index => $update): ?>
                <tr>
                    <td><?php echo htmlspecialchars($update['roll_no']); ?></td>
                    <td><?php echo htmlspecialchars($update['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($update['company_name']); ?></td>
                    <td><?php echo htmlspecialchars($update['program_name']); ?></td>
                    <td><?php echo htmlspecialchars($update['start_date']); ?></td>
                    <td><?php echo htmlspecialchars($update['end_date']); ?></td>
                    <td><?php echo htmlspecialchars($update['events']); ?></td>
                    <td class="<?php echo $update['mentor_approval_status'] == 'approved' ? 'approved' : 'rejected'; ?>">
                        <?php echo htmlspecialchars($update['mentor_approval_status']); ?>
                    </td>
                    <td class="<?php echo $update['classincharge_approval_status'] == 'approved' ? 'approved' : 'rejected'; ?>">
                        <?php echo htmlspecialchars($update['classincharge_approval_status']); ?>
                    </td>
                    <td class="<?php echo $update['hod_approval_status'] == 'approved' ? 'approved' : 'rejected'; ?>">
                        <?php echo htmlspecialchars($update['hod_approval_status']); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <?php include 'footer.php'; ?>
</body>
</html>
