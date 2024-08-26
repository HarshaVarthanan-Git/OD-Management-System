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

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $role = $_POST['role'];
    $mentorId = $_POST['mentor_id'];
    $classInchargeId = $_POST['class_incharge_id'];
    $dob = $_POST['dob'];
    $rollNumber = $_POST['roll_number'];

    $sql = "UPDATE users SET username=?, password=?, name=?, role=?, mentor_id=?, class_incharge_id=?, dob=?, roll_number=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiissi", $username, $password, $name, $role, $mentorId, $classInchargeId, $dob, $rollNumber, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_view_users.php");
    exit();
}

$sql = "SELECT * FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

function getStaffOptions($selectedId) {
    $options = [
        2 => 'Murali Shankar',
        3 => 'Senthil Murugan',
        4 => 'Padmadevi',
        5 => 'Ponmalar',
        6 => 'Shaheeb Jath',
        7 => 'Kavitha',
        8 => 'Lavanya',
        9 => 'Pavithra',
        10 => 'Swedheetha'
    ];
    $html = '';
    foreach ($options as $id => $name) {
        $selected = $id == $selectedId ? 'selected' : '';
        $html .= "<option value=\"$id\" $selected>$name</option>";
    }
    return $html;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }
        .container h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #87cefa;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s, transform 0.3s;
        }
        button:hover {
            background-color: #00bfff;
            transform: scale(1.05);
        }
        button:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit User</h1>
        <form method="post">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label for="password">Password</label>
            <input type="text" id="password" name="password" value="<?php echo htmlspecialchars($user['password']); ?>" required>

            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

            <label for="role">Role</label>
            <input type="text" id="role" name="role" value="<?php echo htmlspecialchars($user['role']); ?>" required>

            <label for="mentor_id">Mentor</label>
            <select id="mentor_id" name="mentor_id" required>
                <option value="">Select Mentor</option>
                <?php echo getStaffOptions($user['mentor_id']); ?>
            </select>

            <label for="class_incharge_id">Class Incharge</label>
            <select id="class_incharge_id" name="class_incharge_id" required>
                <option value="">Select Class Incharge</option>
                <?php echo getStaffOptions($user['class_incharge_id']); ?>
            </select>

            <label for="dob">Date of Birth</label>
            <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>" required>

            <label for="roll_number">Roll Number</label>
            <input type="text" id="roll_number" name="roll_number" value="<?php echo htmlspecialchars($user['roll_number']); ?>" required>

            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>
</html>
