<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$staffName = $_SESSION['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        .btn-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .btn {
            background-color: #87cefa;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px;
            font-size: 1em;
            transition: background-color 0.3s, transform 0.3s;
            width: 100%;
            max-width: 300px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .btn:hover {
            background-color: #00bfff;
            transform: scale(1.05);
        }
        .btn:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/profile.jpg" alt="Profile Image">
        <div class="welcome">Welcome, <?php echo htmlspecialchars($staffName); ?></div>
        <button class="logout" onclick="window.location.href='logout.php'">Logout</button>
    </div>
    <div class="content">
        <button class="btn" onclick="window.location.href='admin_view_users.php'">View Users</button>
        <button class="btn" onclick="window.location.href='admin_add_staff.php'">Add Staff</button>
        <button class="btn" onclick="window.location.href='change_roles.php'">Change Staff Roles</button>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
