<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require 'db.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['username']) || ($_SESSION['role'] != 'student')) {
    header("Location: index.php");
    exit();
}
$studentId = $_SESSION['username'];

// Fetch data from the od_requests table for the logged-in student
$sql = "SELECT student_name, roll_no, company_name, program_name, start_date, end_date, events, mentor_approval_status, classincharge_approval_status, hod_approval_status
        FROM od_requests
        WHERE roll_no = '$studentId'";
$result = $conn->query($sql);

// Initialize an array to store results
$odUpdates = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $odUpdates[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check OD Status</title>
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f9fc;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
            font-size: 2.2em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: #fff;
            border-bottom: 2px solid #0056b3;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #e0f7ff;
            cursor: pointer;
        }
        .btn-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .btn-container a.button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-size: 1em;
        }
        .btn-container a.button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        .status-approved {
            color: #28a745;
            font-weight: bold;
        }
        .status-rejected {
            color: #dc3545;
            font-weight: bold;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        @media (max-width: 768px) {
            table, th, td {
                display: block;
                width: 100%;
            }
            th, td {
                text-align: right;
            }
            td {
                padding-left: 50%;
                position: relative;
            }
            td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 45%;
                padding-left: 10px;
                font-weight: bold;
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="btn-container">
        <a class="button" href="student_dashboard.php">Go to Home Page</a>
    </div>
    <h1>OD Status</h1>
    <?php if (empty($odUpdates)): ?>
        <p style="text-align: center;">No OD Requests found.</p>
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
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($odUpdates as $update): 
                    // Determine the status
                    $status = 'Pending';
                    $statusClass = 'status-pending';
                    if ($update['mentor_approval_status'] == 'rejected' || 
                        $update['classincharge_approval_status'] == 'rejected' || 
                        $update['hod_approval_status'] == 'rejected') {
                        $status = 'Rejected';
                        $statusClass = 'status-rejected';
                    } elseif ($update['mentor_approval_status'] == 'approved' && 
                              $update['classincharge_approval_status'] == 'approved' && 
                              $update['hod_approval_status'] == 'approved') {
                        $status = 'Approved';
                        $statusClass = 'status-approved';
                    }
                ?>
                <tr>
                    <td data-label="Roll No."><?php echo $update['roll_no']; ?></td>
                    <td data-label="Student Name"><?php echo $update['student_name']; ?></td>
                    <td data-label="Company Name"><?php echo $update['company_name']; ?></td>
                    <td data-label="Program Name"><?php echo $update['program_name']; ?></td>
                    <td data-label="Start Date"><?php echo $update['start_date']; ?></td>
                    <td data-label="End Date"><?php echo $update['end_date']; ?></td>
                    <td data-label="Events"><?php echo $update['events']; ?></td>
                    <td data-label="Mentor Approval Status"><?php echo $update['mentor_approval_status']; ?></td>
                    <td data-label="Class Incharge Approval Status"><?php echo $update['classincharge_approval_status']; ?></td>
                    <td data-label="HOD Approval Status"><?php echo $update['hod_approval_status']; ?></td>
                    <td data-label="Status" class="<?php echo $statusClass; ?>"><?php echo $status; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <?php include 'footer.php'; ?>
</body>
</html>
