<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'class_incharge') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staffUsername = $_SESSION['username'];
    $requestId = $_POST['request_id'];
    $approvalStatus = $_POST['approval_status'];

    $updateSql = "UPDATE od_requests SET classincharge_approval_status = '$approvalStatus' WHERE id = '$requestId'";

    if (mysqli_query($conn, $updateSql)) {
        echo "<script>alert('OD request approval updated successfully.');</script>";
    } else {
        echo "Error: " . $updateSql . "<br>" . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Class Incharge Approval Process</title>
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .message {
            margin-top: 20px;
            font-size: 1.2em;
            font-weight: bold;
            color: #087e8b;
        }
        .back-link {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="message">Class Incharge approval process completed.</div>
    <a class="back-link" href="class_incharge_dashboard.php">Back to Dashboard</a>
    <?php include 'footer.php'; ?>
</body>
</html>
