<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require 'db.php';

if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['mentor', 'class_incharge', 'hod'])) {
    header("Location: index.php");
    exit();
}

$updateRejectedSql = "UPDATE od_requests 
                      SET mentor_approval_status = 'rejected', classincharge_approval_status = 'rejected', hod_approval_status = 'rejected'
                      WHERE mentor_approval_status = 'rejected' OR classincharge_approval_status = 'rejected'";
$conn->query($updateRejectedSql);

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$sql = "SELECT roll_no, student_name, company_name, program_name, start_date, end_date, events, mentor_id, classincharge_uname, request_date, mentor_approval_status, classincharge_approval_status, hod_approval_status 
        FROM od_requests";

if ($role == 'mentor') {
    $sql .= " WHERE mentor_id = $user_id";
} elseif ($role == 'class_incharge') {
    $sql .= " WHERE classincharge_uname = '$username'";
} elseif ($role == 'hod') {
    // No need to filter for HOD as they can see all records
}

$result = $conn->query($sql);

$odUpdates = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $odUpdates[] = $row;
    }
}

$mentorNames = [
    1 => 'Ponmalar',
    2 => 'Murali Shankar',
    4 => 'Shaheeb Jath'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check OD Updates</title>
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            background-color: #ffffff;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: #ffffff;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        td {
            border-bottom: 1px solid #ddd;
        }
        .status-approved {
            color: #28a745;
            font-weight: bold;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .status-rejected {
            color: #dc3545;
            font-weight: bold;
        }
        .btn-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }
        .btn-container a.button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 10px;
            transition: background-color 0.3s, transform 0.3s;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .btn-container a.button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="btn-container">
        <a class="button" href="class_incharge_dashboard.php">Back to home</a>
    </div>
    <h1>OD Updates</h1>
    <?php if (empty($odUpdates)): ?>
        <p style="text-align: center; color: #666;">Nothing to be displayed.</p>
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
                <?php foreach ($odUpdates as $update): ?>
                <tr>
                    <td><?php echo htmlspecialchars($update['roll_no']); ?></td>
                    <td><?php echo htmlspecialchars($update['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($update['company_name']); ?></td>
                    <td><?php echo htmlspecialchars($update['program_name']); ?></td>
                    <td><?php echo htmlspecialchars($update['start_date']); ?></td>
                    <td><?php echo htmlspecialchars($update['end_date']); ?></td>
                    <td><?php echo htmlspecialchars($update['events']); ?></td>
                    <td class="<?php echo getStatusClass($update['mentor_approval_status']); ?>">
                        <?php echo ucfirst(htmlspecialchars($update['mentor_approval_status'])); ?>
                    </td>
                    <td class="<?php echo getStatusClass($update['classincharge_approval_status']); ?>">
                        <?php echo ucfirst(htmlspecialchars($update['classincharge_approval_status'])); ?>
                    </td>
                    <td class="<?php echo getStatusClass($update['hod_approval_status']); ?>">
                        <?php echo ucfirst(htmlspecialchars($update['hod_approval_status'])); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <?php include 'footer.php'; ?>
</body>
</html>

<?php 
$conn->close();

function getStatusClass($status) {
    switch ($status) {
        case 'approved':
            return 'status-approved';
        case 'pending':
            return 'status-pending';
        case 'rejected':
            return 'status-rejected';
        default:
            return '';
    }
}
?>
