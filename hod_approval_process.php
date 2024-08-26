<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'hod') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    $approval_status = $_POST['approval_status'];

    // Update the HOD approval status in the database
    $stmt = $conn->prepare("UPDATE od_requests SET hod_approval_status = ? WHERE id = ?");
    $stmt->bind_param("si", $approval_status, $request_id);
    if ($stmt->execute()) {
        // If the request is approved, also update the status to 'approved'
        if ($approval_status == 'approved') {
            $stmt = $conn->prepare("UPDATE od_requests SET status = 'approved' WHERE id = ?");
            $stmt->bind_param("i", $request_id);
            $stmt->execute();

            // Fetch the email of the student for the request
            $stmt = $conn->prepare("SELECT student_name, email FROM od_requests WHERE id = ?");
            $stmt->bind_param("i", $request_id);
            $stmt->execute();
            $stmt->bind_result($student_name, $student_email);
            $stmt->fetch();
            $stmt->close();

            // Send an email to the student
            $subject = "OD Request Granted";
            $message = "Dear $student_name,\n\nYour OD request has been granted by the HOD.\n\nBest Regards,\nDepartment of CSE";
            $headers = "From: borushiki08@gmail.com"; // Replace with your actual email

            if (mail($student_email, $subject, $message, $headers)) {
                $_SESSION['message'] = "OD request approved and email sent successfully.";
            } else {
                $_SESSION['message'] = "OD request approved, but email could not be sent.";
            }
        } else {
            $_SESSION['message'] = "OD request has been rejected.";
        }
    } else {
        $_SESSION['message'] = "Failed to update the OD request status.";
    }

    header("Location: hod_dashboard.php");
    exit();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>HOD Approval Process</title>
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
    <div class="message">HOD approval process completed.</div>
    <a class="back-link" href="hod_dashboard.php">Back to Dashboard</a>
    <?php include 'footer.php'; ?>
</body>
</html>
