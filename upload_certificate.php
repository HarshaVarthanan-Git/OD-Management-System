<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit();
}

$rollNo = $_SESSION['username'];
$studentName = $_SESSION['name'];

$message = '';
$uploadError = '';

// Function to determine academic year based on roll number
function getAcademicYearFromRollNo($rollNo) {
    // Extract the numeric part from the roll number
    preg_match('/(\d{2})/', $rollNo, $matches);
    
    if (!empty($matches[1])) {
        $year = intval($matches[1]);

        if ($year == 22 || $year == 23) {
            // Determine if the student is in 3rd year
            if ($year == 22 || ($year == 23 && strpos($rollNo, 'LC') !== false)) {
                return '2022-2026';
            }
            // Determine if the student is in 2nd year
            elseif ($year == 23) {
                return '2023-2027';
            }
        }
    }
    return 'Unknown_Year';
}

$academicYear = getAcademicYearFromRollNo($rollNo);

// Check if the student has any approved OD requests with pending certificate upload
$sql = "SELECT * FROM od_requests WHERE roll_no = '$rollNo' AND status = 'approved' AND certificate_uploaded = 0";
$result = mysqli_query($conn, $sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['certificate']) && isset($_POST['od_id'])) {
    $odId = $_POST['od_id'];
    $certificate = $_FILES['certificate'];

    // Adjust upload directory based on the academic year
    $uploadDir = 'uploads/' . $academicYear . '/';
    // Create the directory if it does not exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Fetch end date of the specific OD request
    $odQuery = "SELECT end_date FROM od_requests WHERE id = '$odId' AND roll_no = '$rollNo'";
    $odResult = mysqli_query($conn, $odQuery);
    if ($odResult && mysqli_num_rows($odResult) > 0) {
        $odRow = mysqli_fetch_assoc($odResult);
        $endDate = $odRow['end_date'];
        $uploadFile = $uploadDir . $rollNo . '-' . $endDate . '.' . pathinfo($certificate['name'], PATHINFO_EXTENSION);

        // Check for upload errors
        if ($certificate['error'] !== UPLOAD_ERR_OK) {
            $uploadError = "Error uploading file.";
        } else {
            // Move uploaded file to the uploads directory
            if (move_uploaded_file($certificate['tmp_name'], $uploadFile)) {
                // Update database with the certificate path
                $sql = "UPDATE od_requests SET certificate_uploaded = 1, certificate_path = '$uploadFile' WHERE id = '$odId'";

                if (mysqli_query($conn, $sql)) {
                    $message = "Certificate uploaded successfully.";
                } else {
                    $uploadError = "Error updating database.";
                }
            } else {
                $uploadError = "Error moving uploaded file.";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Upload Certificate</title>
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
        .form-container input[type="file"] {
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
        <div class="welcome">Welcome, <?php echo $studentName; ?></div>
        <button class="logout" onclick="window.location.href='student_dashboard.php'">Back to Home</button>
    </div>
    <div class="content">
        <div class="title">Upload Certificate</div>
        <div class="form-container">
            <?php if ($message !== ''): ?>
                <p class="message"><?php echo $message; ?></p>
            <?php endif; ?>
            <?php if ($uploadError !== ''): ?>
                <p class="error-message"><?php echo $uploadError; ?></p>
            <?php endif; ?>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <form method="post" enctype="multipart/form-data">
                        <p><strong>Event:</strong> <?php echo $row['company_name']; ?> | <strong>End Date:</strong> <?php echo $row['end_date']; ?></p>
                        <input type="hidden" name="od_id" value="<?php echo $row['id']; ?>">
                        <label for="certificate">Upload Certificate:</label>
                        <input type="file" id="certificate" name="certificate" required>
                        <input type="submit" value="Upload">
                    </form>
                    <hr>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="message">No pending OD requests or certificates already uploaded.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>

<?php $conn->close(); ?>
