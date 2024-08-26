<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'student') {
    echo "Session variables not set or role is not student. Redirecting to index.php";
    header("Location: index.php");
    exit();
}

$studentName = $_SESSION['name'];
$rollNo = $_SESSION['username'];

// Count of approved and submitted OD requests
$sqlApprovedSubmitted = "SELECT COUNT(*) AS count_approved_submitted FROM od_requests WHERE roll_no = '$rollNo' AND status = 'approved' ";
$resultApprovedSubmitted = mysqli_query($conn, $sqlApprovedSubmitted);
$countApprovedSubmitted = mysqli_fetch_assoc($resultApprovedSubmitted)['count_approved_submitted'];

// Count of approved but pending OD requests
$sqlApprovedPending = "SELECT COUNT(*) AS count_approved_pending FROM od_requests WHERE roll_no = '$rollNo' AND status = 'approved' AND certificate_uploaded = 0";
$resultApprovedPending = mysqli_query($conn, $sqlApprovedPending);
$countApprovedPending = mysqli_fetch_assoc($resultApprovedPending)['count_approved_pending'];

// Count of pending OD requests
$sqlPending = "SELECT COUNT(*) AS count_pending FROM od_requests WHERE roll_no = '$rollNo' AND status = 'pending'";
$resultPending = mysqli_query($conn, $sqlPending);
$countPending = mysqli_fetch_assoc($resultPending)['count_pending'];

// Count of rejected OD requests
$sqlRejected = "SELECT COUNT(*) AS count_rejected FROM od_requests WHERE roll_no = '$rollNo' AND status = 'rejected'";
$resultRejected = mysqli_query($conn, $sqlRejected);
$countRejected = mysqli_fetch_assoc($resultRejected)['count_rejected'];

// Total OD requests
$sqlTotalODRequests = "SELECT COUNT(*) AS count_total_requests FROM od_requests WHERE roll_no = '$rollNo'";
$resultTotalODRequests = mysqli_query($conn, $sqlTotalODRequests);
$countTotalODRequests = mysqli_fetch_assoc($resultTotalODRequests)['count_total_requests'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        /* Include the CSS provided above here */
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f7f9;
}

.header {
    width: 100%;
    padding: 20px;
    background-color: #003d7a; /* Dark blue for header */
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.header img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.header .welcome {
    font-size: 1.5em;
    font-weight: bold;
}

.header .logout {
    background-color: #ffffff;
    color: #003d7a;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    font-size: 1em;
    transition: background-color 0.3s, transform 0.3s;
}

.header .logout:hover {
    background-color: #e0e0e0;
    transform: scale(1.05);
}

.sidebar {
    width: 250px;
    background-color: #003d7a; /* Match header color */
    color: white;
    display: flex;
    flex-direction: column;
    padding: 20px;
    position: fixed;
    top: 80px; /* Adjusted to be below the header */
    bottom: 0;
    height: calc(100% - 80px);
    box-shadow: 2px 0 4px rgba(0, 0, 0, 0.1);
    z-index: 2;
}

.sidebar h2 {
    margin-top: 0;
    font-size: 1.5em;
}

.sidebar button {
    background-color: #ffffff;
    color: #003d7a; /* Match header color */
    border: none;
    padding: 15px;
    margin: 10px 0;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1em;
    text-align: left;
    width: 100%;
    transition: background-color 0.3s, transform 0.3s;
}

.sidebar button:hover {
    background-color: #002d62; /* Darker blue */
    color: white;
    transform: scale(1.05);
}

.content {
    margin-left: 250px;
    padding: 20px;
    margin-top: 80px; /* Adjusted for fixed header */
}

.dashboard {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
}

.box {
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: calc(50% - 20px); /* Two boxes per row with gap */
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    transition: transform 0.3s, box-shadow 0.3s;
}

.box:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
}

.box h3 {
    margin: 0;
    font-size: 1.2em;
    color: #333;
}

.box p {
    font-size: 1.5em;
    color: #555;
}

@media (max-width: 768px) {
    .box {
        width: calc(100% - 20px); /* Full width on smaller screens */
    }
}

    </style>
</head>
<body>
    <div class="header">
        <img src="images/profile.jpg" alt="Profile Image">
        <div class="welcome">Welcome, <?php echo htmlspecialchars($studentName); ?></div>
        <button class="logout" onclick="window.location.href='logout.php'">Logout</button>
    </div>
    <div class="sidebar">
        <h2>Menu</h2>
        <button onclick="window.location.href='od_request.php'">Request OD</button>
        <button onclick="window.location.href='student_request.php'">OD Status</button>
        <button onclick="window.location.href='upload_certificate.php'">Upload Certificate</button>
        <button onclick="window.location.href='view_uploaded_details.php'">View OD History</button>
    </div>
    <div class="content">
        <div class="dashboard">
            <div class="box">
                <h3>Total ODs requested</h3>
                <p><?php echo htmlspecialchars($countTotalODRequests); ?></p>
            </div>
            <div class="box">
                <h3>Approved ODs</h3>
                <p><?php echo htmlspecialchars($countApprovedSubmitted); ?></p>
            </div>
            <div class="box">
                <h3>Pending ODs</h3>
                <p><?php echo htmlspecialchars($countPending); ?></p>
            </div>
            <div class="box">
                <h3>Rejected ODs</h3>
                <p><?php echo htmlspecialchars($countRejected); ?></p>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
