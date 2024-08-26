<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit();
}

$rollNo = $_SESSION['username'];
$studentName = $_SESSION['name'];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $odId = $_GET['id'];

    $sql = "SELECT * FROM od_requests WHERE id = '$odId' AND roll_no = '$rollNo'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        $companyName = $row['company_name'];
        $collegeName = $row['college_name'];
        $programName = $row['program_name'];
        $startDate = $row['start_date'];
        $endDate = $row['end_date'];
        $mentorId = $row['mentor_id'];

    } else {
        header("Location: view_uploaded_details.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $companyName = $_POST['company_name'];
    $collegeName = $_POST['college_name'];
    $programName = $_POST['program_name'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $mentorId = $_POST['mentor_id'];

    $sql = "UPDATE od_requests SET 
            company_name = '$companyName', 
            college_name = '$collegeName', 
            program_name = '$programName', 
            start_date = '$startDate', 
            end_date = '$endDate', 
            mentor_id = '$mentorId'
            WHERE id = '$odId'";

    if (mysqli_query($conn, $sql)) {
        // Redirect to view details page
        header("Location: view_uploaded_details.php");
        exit();
    } else {
        $error = "Error updating OD request: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit OD Request</title>
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
            width: 90%;
            max-width: 800px;
        }
        .title {
            color: #87cefa;
            font-size: 2em;
            margin-bottom: 20px;
        }
        .form-container {
            background-color: #f0f0f0;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.5s ease-out;
            width: 100%;
            max-width: 500px;
            margin: 20px auto;
            box-sizing: border-box;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .input-group {
            margin-bottom: 15px;
            width: 100%;
            display: flex;
            flex-direction: column;
        }
        .input-group label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .input-group input,
        .input-group select {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }
        .input-group input[type="date"] {
            width: calc(100% - 22px); 
        }
        .input-group input[type="submit"] {
            background-color: #87cefa;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            width: 50%;
            align-self: center;
        }
        .input-group input[type="submit"]:hover {
            background-color: #00bfff;
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
        <div class="welcome">Welcome, <?php echo $studentName; ?></div>
        <button class="logout" onclick="window.location.href='logout.php'">Logout</button>
    </div>
    <div class="content">
        <div class="title">Edit OD Request</div>
        <div class="form-container">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $odId; ?>">
                <div class="input-group">
                    <label for="company_name">Company/College Name:</label>
                    <input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($companyName); ?>" required>
                </div>
               
                <div class="input-group">
                    <label for="program_name">Programme/Event Name:</label>
                    <input type="text" id="program_name" name="program_name" value="<?php echo htmlspecialchars($programName); ?>" required>
                </div>
                <div class="input-group">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo $startDate; ?>" required>
                </div>
                <div class="input-group">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo $endDate; ?>" required>
                </div>
                <div class="input-group">
                    <label for="mentor_id">Mentor ID:</label>
                    <input type="text" id="mentor_id" name="mentor_id" value="<?php echo htmlspecialchars($mentorId); ?>" required>
                </div>
                <div class="input-group">
                    <input type="submit" name="submit" value="Update">
                </div>
                <?php if (isset($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
