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

// Fetch all staff (mentors and class incharges)
$staffSql = "SELECT id, name FROM users WHERE role IN ('mentor', 'class_incharge')";
$staffResult = $conn->query($staffSql);

$staff = [];
if ($staffResult->num_rows > 0) {
    $staff = $staffResult->fetch_all(MYSQLI_ASSOC);
}

// Define class mappings
$classMappings = [
    'III CSE A' => ['22CSEA', '23LCSEA'],
    'III CSE B' => ['22CSEB', '23LCSEB'],
    'III CSE C' => ['22CSEC', '23LCSEC']
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['change_roles'])) {
        $selected_class = $_POST['selected_class'];
        $start_roll_number = $_POST['start_roll_number'];
        $end_roll_number = $_POST['end_roll_number'];
        $new_mentor_id = $_POST['new_mentor_id'];
        $new_class_incharge_id = $_POST['new_class_incharge_id'];

        // Update the mentor and class incharge for the selected students within the roll number range
        $prefixes = $classMappings[$selected_class];
        foreach ($prefixes as $prefix) {
            $sql = "UPDATE users SET mentor_id = '$new_mentor_id', class_incharge_id = '$new_class_incharge_id' 
                    WHERE roll_number LIKE '$prefix%' AND CAST(SUBSTRING(roll_number, LENGTH('$prefix') + 1) AS UNSIGNED) 
                    BETWEEN '$start_roll_number' AND '$end_roll_number'";
            if ($conn->query($sql) === TRUE) {
                echo "<div class='success'>Roles updated successfully for students from $start_roll_number to $end_roll_number in class $selected_class!</div>";
            } else {
                echo "<div class='error'>Error: " . $conn->error . "</div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Roles</title>
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
        .form-container {
            width: 100%;
            max-width: 600px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        .form-container input, .form-container select {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .form-container button {
            background-color: #87cefa;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        .form-container button:hover {
            background-color: #00bfff;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/profile.jpg" alt="Profile Image">
        <div class="welcome">Welcome, <?php echo htmlspecialchars($staffName); ?></div>
        <button class="logout" onclick="window.location.href='admin_dashboard.php'">Back to Home</button>
    </div>
    <div class="content">
        <div class="form-container">
            <form method="post">
                <label>Select Class: 
                    <select name="selected_class" required>
                        <option value="">Select Class</option>
                        <?php foreach ($classMappings as $class => $prefixes): ?>
                            <option value="<?php echo htmlspecialchars($class); ?>">
                                <?php echo htmlspecialchars($class); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Starting Roll Number: 
                    <input type="text" name="start_roll_number" placeholder="e.g., 01" required>
                </label>
                <label>Ending Roll Number: 
                    <input type="text" name="end_roll_number" placeholder="e.g., 50" required>
                </label>
                <label>New Mentor: 
                    <select name="new_mentor_id" required>
                        <option value="">Select Mentor</option>
                        <?php foreach ($staff as $mentor): ?>
                            <option value="<?php echo htmlspecialchars($mentor['id']); ?>">
                                <?php echo htmlspecialchars($mentor['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>New Class Incharge: 
                    <select name="new_class_incharge_id" required>
                        <option value="">Select Class Incharge</option>
                        <?php foreach ($staff as $classIncharge): ?>
                            <option value="<?php echo htmlspecialchars($classIncharge['id']); ?>">
                                <?php echo htmlspecialchars($classIncharge['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <button type="submit" name="change_roles">Update</button>
            </form>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
