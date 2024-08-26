<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        // For students, check DOB as password
        if ($row['role'] == 'student') {
            if ($password == $row['dob']) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['name'] = $row['name'];
                header("Location: student_dashboard.php");
                exit();
            }
        } else {
            if ($password == $row['password']) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['name'] = $row['name'];
                if ($row['role'] == 'mentor') {
                    header("Location: mentor_dashboard.php");
                } elseif ($row['role'] == 'class_incharge') {
                    header("Location: class_incharge_dashboard.php");
                } elseif ($row['role'] == 'hod') {
                    header("Location: hod_dashboard.php");
                }
                elseif ($row['role'] == 'admin') {
                    header("Location: admin_dashboard.php");
                }
                exit();
            }
        }
        echo "<div class='error'>Invalid password.</div>";
    } else {
        echo "<div class='error'>Invalid username.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .header {
            width: 100%;
            padding: 0.5em;
            background-color: white;
            display: flex;
            align-items: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .header img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-left: 20px;
        }

        .header .title {
            flex: 1;
            text-align: center;
            font-size: 1.2em;
            font-weight: bold;
            margin: 0;
        }

        .login-container {
            background-color: white;
            padding: 2em;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
            margin-top: 100px;
        }

        .login-container h1 {
            margin-bottom: 1em;
            color: #333;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px 30px 10px 10px; /* Adjusted padding for space for image */
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box; /* Ensures padding doesn't affect width */
        }

        .login-container input[type="password"] {
            background-image: url('eye.png'); /* Image for password toggle */
            background-repeat: no-repeat;
            background-position: right 10px center; /* Position image on the right side */
            background-size: 20px; /* Size of the image */
        }

        .login-container .toggle-password {
            background-color: transparent;
            border: none;
            padding: 0;
            font-size: 0.8em;
            text-decoration: underline;
            cursor: pointer;
            margin-top: 5px;
            color: #007bff; /* Blue color for link */
        }

        .login-container .toggle-password:hover {
            color: #0056b3; /* Darker blue on hover */
        }

        .login-container button[type="submit"] {
            background-color: #87cefa;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }

        .login-container button[type="submit"]:hover {
            background-color: #00bfff;
        }

        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('toggleButton');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.textContent = 'Hide Password';
            } else {
                passwordInput.type = 'password';
                toggleButton.textContent = 'Show Password';
            }
        }

        function validateForm() {
            const username = document.forms["loginForm"]["username"].value.trim();
            const password = document.forms["loginForm"]["password"].value.trim();

            const rollNumberPattern = /^(22CSEB|23LCSEB|22CSEA|22CSEC|21CSE|23LCSEA|23LCSEC|23CSEA|23CSEB|23CSEC)\d{2}$/;
            const passwordPattern = /^\d{4}-\d{2}-\d{2}$/;
            const staffUsernamePattern = /^[a-z]+$/;
            const numericPasswordPattern = /^[a-zA-Z\d]+$/;

            if (!rollNumberPattern.test(username) && !staffUsernamePattern.test(username)) {
                alert("Invalid username. Roll number should be '22CSEBxx' or '23LCSEBxx' where xx ranges from 01 to 64, or a valid staff username (all lowercase letters).");
                return false;
            }

            if (rollNumberPattern.test(username) && !passwordPattern.test(password)) {
                alert("Invalid password format. For students, it should be in the format 'yyyy-mm-dd'.");
                return false;
            }

            if (staffUsernamePattern.test(username) && !numericPasswordPattern.test(password)) {
                alert("Invalid password");
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <div class="header">
        <img src="images/profile.jpg" alt="Profile Image"> 
        <div class="title">Velammal College Of Engineering, Madurai</div>
    </div>
    <div class="login-container">
        <h1>Login</h1>
        <form name="loginForm" method="post" action="index.php" onsubmit="return validateForm()">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <button type="button" id="toggleButton" class="toggle-password" onclick="togglePasswordVisibility()">Show Password</button>
            <button type="submit">Login</button>
        </form>
        <?php if(isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
