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
$users = [];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 25;
$offset = ($page - 1) * $limit;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rollNumberPrefix = $_POST['roll_number_prefix'];
    header("Location: admin_view_users.php?page=1&roll_number_prefix=" . urlencode($rollNumberPrefix));
    exit();
} else {
    $rollNumberPrefix = isset($_GET['roll_number_prefix']) ? $_GET['roll_number_prefix'] : '';
}

// Map roll number prefix to class
$prefixMap = [
    '21CSEA' => 'a',
    '22CSEA' => 'a',
    '22CSEB' => 'b',
    '22CSEC' => 'c',
    '23LCSEA' => 'a',
    '23LCSEB' => 'b',
    '23LCSEC' => 'c'
];

// Function to get the roll number prefix from the roll number
function getRollNumberPrefix($rollNumber) {
    global $prefixMap;
    foreach ($prefixMap as $prefix => $char) {
        if (strpos($rollNumber, $prefix) === 0) {
            return $char;
        }
    }
    return 'all';
}

// Query for user data
$sql = "SELECT * FROM users WHERE role = 'student'";
if ($rollNumberPrefix && $rollNumberPrefix !== 'all') {
    $prefixes = array_keys($prefixMap, $rollNumberPrefix);
    if ($prefixes) {
        $likeConditions = [];
        foreach ($prefixes as $prefix) {
            $likeConditions[] = "roll_number LIKE '$prefix%'";
        }
        $sql .= " AND (" . implode(' OR ', $likeConditions) . ")";
    }
}
$sql .= " ORDER BY roll_number ASC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Fetch users
if ($result->num_rows > 0) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
}

// Count total records for pagination
$sqlCount = "SELECT COUNT(*) as total FROM users WHERE role = 'student'";
if ($rollNumberPrefix && $rollNumberPrefix !== 'all') {
    $prefixesCount = array_keys($prefixMap, $rollNumberPrefix);
    if ($prefixesCount) {
        $likeConditionsCount = [];
        foreach ($prefixesCount as $prefix) {
            $likeConditionsCount[] = "roll_number LIKE '$prefix%'";
        }
        $sqlCount .= " AND (" . implode(' OR ', $likeConditionsCount) . ")";
    }
}
$countResult = $conn->query($sqlCount);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Function to get staff name by ID
function getStaffName($id) {
    $names = [
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
    return isset($names[$id]) ? $names[$id] : 'Unknown';
}

// Safe output function
function safeOutput($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
    <link rel="stylesheet" href="css/responsive.css">
    <style> body { font-family: Arial, sans-serif; background-color: #f0f8ff; margin: 0; display: flex; flex-direction: column; align-items: center; } .header { width: 100%; padding: 1em; background-color: white; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); position: sticky; top: 0; } .header img { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; } .header .welcome { font-size: 1.2em; font-weight: bold; margin-left: 10px; } .header .logout { background-color: #87cefa; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; } .header .logout:hover { background-color: #00bfff; } .content { text-align: center; margin-top: 20px; display: flex; flex-direction: column; align-items: center; padding-bottom: 60px; } .btn { background-color: #87cefa; color: white; border: none; padding: 15px 30px; border-radius: 5px; cursor: pointer; margin: 10px; font-size: 1em; transition: background-color 0.3s, transform 0.3s; max-width: 300px; text-align: center; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); } .btn:hover { background-color: #00bfff; transform: scale(1.05); } .btn:active { transform: scale(0.95); } table { width: 100%; max-width: 1000px; border-collapse: collapse; margin: 20px 0; } th, td { padding: 10px; text-align: left; border: 1px solid #ddd; } th { background-color: #87cefa; color: white; } .filter-form { margin-bottom: 20px; } .filter-form select, .filter-form button { padding: 5px; font-size: 0.9em; } .pagination { margin-top: 20px; } .pagination a { margin: 0 5px; text-decoration: none; color: #87cefa; } .pagination a:hover { text-decoration: underline; } .footer { width: 100%; background-color: #87cefa; color: white; text-align: center; padding: 10px; position: fixed; bottom: 0; left: 0; font-size: 0.9em; } </style>
</head>
<body>
    <div class="header">
        <img src="images/profile.jpg" alt="Profile Image">
        <div class="welcome">Welcome, <?php echo safeOutput($staffName); ?></div>
        <button class="logout" onclick="window.location.href='admin_dashboard.php'">Back to Home</button>
    </div>
    <div class="content">
        <form id="filter-form" method="post" class="filter-form">
            <select name="roll_number_prefix" required>
                <option value="">Select Class</option>
                <option value="all" <?php echo $rollNumberPrefix === 'all' ? 'selected' : ''; ?>>All Students</option>
                <option value="a" <?php echo $rollNumberPrefix === 'a' ? 'selected' : ''; ?>>III CSE A</option>
                <option value="b" <?php echo $rollNumberPrefix === 'b' ? 'selected' : ''; ?>>III CSE B</option>
                <option value="c" <?php echo $rollNumberPrefix === 'c' ? 'selected' : ''; ?>>III CSE C</option>
            </select>
            <button type="submit" class="btn">Filter</button>
        </form>

        <?php if (count($users) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Name</th>
                        <th>Mentor</th>
                        <th>Class Incharge</th>
                        <th>HOD</th>
                        <th>Roll Number</th>
                        <th>DOB</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo safeOutput($user['username']); ?></td>
                            <td><?php echo safeOutput($user['password']); ?></td>
                            <td><?php echo safeOutput($user['name']); ?></td>
                            <td><?php echo safeOutput(getStaffName($user['mentor_id'])); ?></td>
                            <td><?php echo safeOutput(getStaffName($user['class_incharge_id'])); ?></td>
                            <td>Perumal Raja</td>
                            <td><?php echo safeOutput($user['roll_number']); ?></td>
                            <td><?php echo safeOutput($user['dob']); ?></td>
                            <td><a href="edit_user.php?id=<?php echo safeOutput($user['id']); ?>" class="btn">Edit</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No users found for the selected criteria.</p>
        <?php endif; ?>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&roll_number_prefix=<?php echo urlencode($rollNumberPrefix); ?>">&laquo; Previous</a>
            <?php endif; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>&roll_number_prefix=<?php echo urlencode($rollNumberPrefix); ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="footer">
        Designed and Developed by Department of CSE
    </div>
</body>
</html>
