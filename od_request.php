<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require 'db.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'student') {
    header("Location: index.php");
    exit();
}

$rollNo = $_SESSION['username'];
$studentName = $_SESSION['name'];

// Fetch the student's assigned mentor ID and class incharge ID
$mentorQuery = "SELECT mentor_id, class_incharge_id FROM users WHERE username = '$rollNo'";
$mentorResult = mysqli_query($conn, $mentorQuery);
$mentorRow = mysqli_fetch_assoc($mentorResult);
$assignedMentorId = $mentorRow['mentor_id'];
$classInchargeId = $mentorRow['class_incharge_id'];

// Fetch the username of the class incharge
$classInchargeUsername = '';
if (!empty($classInchargeId)) {
    $classInchargeUsernameQuery = "SELECT username FROM users WHERE id = '$classInchargeId'";
    $classInchargeUsernameResult = mysqli_query($conn, $classInchargeUsernameQuery);
    $classInchargeUsernameRow = mysqli_fetch_assoc($classInchargeUsernameResult);
    $classInchargeUsername = $classInchargeUsernameRow['username'];
}

$error = '';
$companyName = $collegeName = $programName = $startDate = $endDate = $mentorId = $proofLink = '';
$events = [];
$paperPresentationDetails = $projectDetails = $otherEventDetails = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $companyName = $_POST['company_name']; // Assuming this is provided in your HTML form
    $programName = $_POST['program_name'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $mentorId = $_POST['mentor_id'];
    $events = $_POST['events'] ?? [];
    $paperPresentationDetails = $_POST['paper_presentation_details'] ?? '';
    $projectDetails = $_POST['project_details'] ?? '';
    $otherEventDetails = $_POST['other_event_details'] ?? '';
    $proofLink = $_POST['proof_link'] ?? '';
    // Validate dates
    if (strtotime($startDate) < time()) {
        $error = "Start date must be after the current date.";
    } elseif (strtotime($startDate) > strtotime($endDate)) {
        $error = "End date must be after the start date.";
    } elseif (empty($proofLink)) {
        $error = "Link for Proof is required.";
    } else {
        // Prepare to insert into database
        $eventsStr = implode(', ', $events); // Convert array to comma-separated string
        $mentorApprovalStatus = ($mentorId == $classInchargeId) ? 'approved' : 'pending'; // Set mentor_approval_status based on condition
        
        $sql = "INSERT INTO od_requests (roll_no, student_name, company_name, college_name,  program_name, start_date, end_date, events, mentor_id, classincharge_uname, mentor_approval_status, PaperPresentationDetails, ProjectDetails, OtherEventDetails, proof_link)
                VALUES ('$rollNo', '$studentName', '$companyName', '$collegeName', '$programName', '$startDate', '$endDate', '$eventsStr', '$mentorId', '$classInchargeUsername', '$mentorApprovalStatus', '$paperPresentationDetails', '$projectDetails', '$otherEventDetails', '$proofLink')";

        if (empty($events)) {
            $error = "Please select at least one event.";
        } else {
            foreach ($events as $event) {
                if ($event === 'Paper Presentation' && empty($paperPresentationDetails)) {
                    $error = "Please provide Paper Presentation details.";
                    break;
                } elseif ($event === 'Project Presentation' && empty($projectDetails)) {
                    $error = "Please provide Project Presentation details.";
                    break;
                } elseif ($event === 'Other Events' && empty($otherEventDetails)) {
                    $error = "Please provide Other Event details.";
                    break;
                }
            }
        }

        if (empty($error) && mysqli_query($conn, $sql)) {
            echo "<script>alert('OD request sent successfully.');</script>";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
}
?>



<!DOCTYPE html>
<html>
<head>
    <title>OD Request</title>
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 60px;
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
            width: 100%;
            margin-bottom: 8px;
        }
        .form-container input[type="text"],
        .form-container input[type="date"],
        .form-container input[type="checkbox"],
        .form-container select {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 1em;
        }
        .form-container .event-details {
            display: none;
            margin-top: 10px;
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
        .form-container .checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        .form-container .checkbox-container input[type="checkbox"] {
            margin-right: 10px;
            transform: translateY(1px); /* Adjust vertical alignment */
        }
        .form-container .checkbox-container label {
            margin: 0;
            font-size: 1em; /* Adjust label font size */
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Existing script for checkbox display toggle
            const paperPresentationCheckbox = document.getElementById('paper_presentation');
            const paperPresentationDetails = document.getElementById('paper_presentation_details');
            const projectPresentationCheckbox = document.getElementById('project_presentation');
            const projectDetails = document.getElementById('project_details');
            const otherEventsCheckbox = document.getElementById('other_events');
            const otherEventDetails = document.getElementById('other_event_details');

            paperPresentationCheckbox.addEventListener('change', function() {
                paperPresentationDetails.style.display = paperPresentationCheckbox.checked ? 'block' : 'none';
            });

            projectPresentationCheckbox.addEventListener('change', function() {
                projectDetails.style.display = projectPresentationCheckbox.checked ? 'block' : 'none';
            });

            otherEventsCheckbox.addEventListener('change', function() {
                otherEventDetails.style.display = otherEventsCheckbox.checked ? 'block' : 'none';
            });

            // Additional script for form submission validation
            const form = document.querySelector('form');

            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent form submission initially

                // Validate checkboxes
                const checkboxes = document.querySelectorAll('input[type="checkbox"]');
                let isChecked = false;

                checkboxes.forEach(function(checkbox) {
                    if (checkbox.checked) {
                        const detailsInput = document.querySelector(`#${checkbox.id}_details_input`);
                        if (!detailsInput || detailsInput.value.trim() !== '') {
                            isChecked = true;
                        }
                    }
                });

                if (!isChecked) {
                    alert('Please select at least one event and fill in the details.');
                    return;
                }

                // If validation passed, submit the form
                form.submit();
            });
        });
    </script>
</head>
<body>
    <div class="header">
        <img src="images/profile.jpg" alt="Profile Image">
        <div class="welcome">Welcome, <?php echo $studentName; ?></div>
        <button class="logout" onclick="window.location.href='student_dashboard.php'">Back to Home</button>
    </div>
    <div class="content">
        <div class="title">OD REQUEST</div>
        <div class="form-container">
            <?php if ($error !== ''): ?>
                <p class="error-message"><?php echo $error; ?></p>
            <?php endif; ?>
            <form method="post">
                <label for="company_name">Name of College/Company (with full address):</label>
                <input type="text" id="company_name" name="company_name" placeholder="Enter Name of College/Company" value="<?php echo htmlspecialchars($companyName); ?>" required>

                <label for="program_name">Name of Programme/Symposium/Event:</label>
                <input type="text" id="program_name" name="program_name" placeholder="Enter Name of Programme/Symposium/Event" value="<?php echo htmlspecialchars($programName); ?>" required>

                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>" required>

                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>" required>
                <label for="company_name">Events: </label>
                <div class="checkbox-container">
    
                <label for="paper_presentation">Paper Presentation</label>
                    <input type="checkbox" id="paper_presentation" name="events[]" value="Paper Presentation">
                    
                </div>
                <div id="paper_presentation_details" class="event-details">
                    <label for="paper_presentation_details">Paper Presentation Details:</label>
                    <input type="text" id="paper_presentation_details_input" name="paper_presentation_details" placeholder="Enter Paper Presentation Details">
                </div>

                <div class="checkbox-container">
                <label for="project_presentation">Project Presentation</label>
                    <input type="checkbox" id="project_presentation" name="events[]" value="Project Presentation">
                  
                </div>
                <div id="project_details" class="event-details">
                    <label for="project_details">Project Presentation Details:</label>
                    <input type="text" id="project_details_input" name="project_details" placeholder="Enter Project Presentation Details">
                </div>

                <div class="checkbox-container">
                       <label for="other_events">Other Events</label>
                    <input type="checkbox" id="other_events" name="events[]" value="Other Events">
                 
                </div>
                <div id="other_event_details" class="event-details">
                    <label for="other_event_details">Other Event Details:</label>
                    <input type="text" id="other_event_details_input" name="other_event_details" placeholder="Enter Other Event Details">
                </div>
                <label for="proof_link">Link for Proof:</label>
<input type="text" id="proof_link" name="proof_link" placeholder="Enter Link for Proof" value="<?php echo htmlspecialchars($proofLink); ?>" required>

                

                <input type="hidden" name="mentor_id" value="<?php echo $assignedMentorId; ?>">

                <input type="submit" value="Request">
            </form>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
