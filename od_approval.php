<?php

session_start();
require 'db.php';

if (!isset($_SESSION['username']) || ($_SESSION['role'] != 'mentor' && $_SESSION['role'] != 'classincharge' && $_SESSION['role'] != 'hod')) {
    header("Location: index.php");
    exit();
}

$staffUsername = $_SESSION['username'];
$staffRole = $_SESSION['role'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestId = $_POST['request_id'];
    $approvalStatus = $_POST['approval_status'];

    // Fetch the request to determine the next approval step
    $sql = "SELECT * FROM od_requests WHERE id = '$requestId'";
    $result = mysqli_query($conn, $sql);
    $request = mysqli_fetch_assoc($result);

    if ($staffRole == 'mentor') {
        // Update mentor approval status
        $sql = "UPDATE od_requests SET mentor_approval_status = '$approvalStatus' WHERE id = '$requestId'";
    } elseif ($staffRole == 'classincharge') {
        // Update class in-charge approval status
        $sql = "UPDATE od_requests SET classincharge_approval_status = '$approvalStatus' WHERE id = '$requestId'";
    } elseif ($staffRole == 'hod') {
        // Update HOD approval status
        $sql = "UPDATE od_requests SET hod_approval_status = '$approvalStatus' WHERE id = '$requestId'";
        if ($approvalStatus == 'approved') {
            // Update OD request final status to approved
            $sql = "UPDATE od_requests SET final_approval_status = 'approved' WHERE id = '$requestId'";
        }
    }

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('OD request approval updated successfully.');</script>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>OD Approval</title>
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
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .title {
            color: #87cefa;
            font-size: 2em;
            margin-bottom: 20px;
        }
        .form-container {
            width: 90%;
            max-width: 600px;
            background-color: #f0f0f0;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .form-container label {
            display: block;
            margin-bottom: 8px;
        }
        .form-container input[type="text"],
        .form-container input[type="date"],
        .form-container input[type="checkbox"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 1em;
        }
        .form-container input[type="submit"] {
            background-color: #87cefa;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            font-size: 1em;
            transition: background-color 0.3s, transform 0.3s;
        }
        .form-container input[type="submit"]:hover {
            background-color: #00bfff;
            transform: scale(1.05);
        }
        .form-container input[type="submit"]:active {
            transform: scale(0.95);
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
        <div class="welcome">Welcome, <?php echo $staffUsername; ?></div>
        <button class="logout" onclick="window.location.href='logout.php'">Logout</button>
    </div>
    <div class="content">
        <div class="title">OD Approval</div>
        <div class="form-container">
            <?php if ($error !== ''): ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php endif; ?>
            <form method="post">
                <label for="request_id">Request ID:</label>
                <input type="text" id="request_id" name="request_id" placeholder="Enter Request ID" required>

                <label for="approval_status">Approval Status:</label>
                <input type="radio" id="approved" name="approval_status" value="approved" required>
                <label for="approved">Approved</label>
                <input type="radio" id="rejected" name="approval_status" value="rejected" required>
                <label for="rejected">Rejected</label>

                <input type="submit" value="Submit">
            </form>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
