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

// Fetch all staff (mentors and class in-charges)
$staffSql = "SELECT id, name, role FROM users WHERE role IN ('mentor', 'class_incharge')";
$staffResult = $conn->query($staffSql);

$staff = [];
if ($staffResult->num_rows > 0) {
    $staff = $staffResult->fetch_all(MYSQLI_ASSOC);
}

// Function to process each row of the CSV file
function processRow($data, $conn) {
    $roll_number = $data[0];
    $dob = $data[1];
    $name = $data[2];

    // Convert date of birth to YYYY-MM-DD format if necessary
    $dob = DateTime::createFromFormat('m/d/Y', $dob); // Adjust format as needed
    if ($dob !== false) {
        $dob = $dob->format('Y-m-d');
    } else {
        // Handle invalid date format
        echo "<div class='error'>Invalid date format for roll number $roll_number</div>";
        return;
    }

    // Fetch mentor ID (if any)
    $mentor_id = null;

    // Fetch class in-charge ID (if any)
    $class_incharge_id = null;

    // Check if student already exists
    $check_sql = "SELECT id FROM users WHERE roll_number = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $roll_number);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Skip insertion if student already exists
        $stmt->close();
        return;
    }
    $stmt->close();

    // Prepare and execute the insert query
    $username = $roll_number;
    $password = $dob; // Use DOB as password directly
    $hod_id = 1;

    $sql = "INSERT INTO users (username, password, name, role, mentor_id, class_incharge_id, roll_number, dob, hod_id) 
            VALUES (?, ?, ?, 'student', ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiisis", $username, $password, $name, $mentor_id, $class_incharge_id, $roll_number, $dob, $hod_id);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_student'])) {
        $roll_number = $_POST['roll_number'];
        $name = $_POST['name'];
        $dob = $_POST['dob'];
        $mentor_id = $_POST['mentor_id'] ?? null;
        $class_incharge_id = $_POST['class_incharge_id'] ?? null;

        // Convert dob to YYYY-MM-DD format
        $dob = DateTime::createFromFormat('Y-m-d', $dob);
        if ($dob !== false) {
            $dob = $dob->format('Y-m-d');
        } else {
            echo "<div class='error'>Invalid date format</div>";
            return;
        }

        // Check if student already exists
        $check_sql = "SELECT id FROM users WHERE roll_number = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $roll_number);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo "<div class='error'>Student with roll number $roll_number already exists!</div>";
            $stmt->close();
        } else {
            $stmt->close();
            $username = $roll_number;
            $password = $dob; // Use DOB as password directly
            $hod_id = 1;

            $sql = "INSERT INTO users (username, password, name, role, mentor_id, class_incharge_id, roll_number, dob, hod_id) 
                    VALUES (?, ?, ?, 'student', ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssiisis", $username, $password, $name, $mentor_id, $class_incharge_id, $roll_number, $dob, $hod_id);
            if ($stmt->execute()) {
                echo "<div class='success'>Student added successfully!</div>";
            } else {
                echo "<div class='error'>Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Students</title>
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
            margin-bottom: 20px;
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
            <form action="admin_add_students.php" method="post">
                <label for="roll_number">Roll Number:</label>
                <input type="text" name="roll_number" id="roll_number" required>
                
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" required>
                
                <label for="dob">Date of Birth:</label>
                <input type="date" name="dob" id="dob" required>
                
                <label for="mentor_id">Mentor:</label>
                <select name="mentor_id" id="mentor_id">
                    <option value="">None</option>
                    <?php foreach ($staff as $member): ?>
                        <?php if ($member['role'] === 'mentor'): ?>
                            <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                
                <label for="class_incharge_id">Class In-Charge:</label>
                <select name="class_incharge_id" id="class_incharge_id">
                    <option value="">None</option>
                    <?php foreach ($staff as $member): ?>
                        <?php if ($member['role'] === 'class_incharge'): ?>
                            <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" name="add_student">Add Student</button>
            </form>
        </div>
    </div>
</body>
</html>
