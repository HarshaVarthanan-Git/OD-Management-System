<?php
session_start();
require 'db.php';
require_once('tcpdf/tcpdf.php');

// Fetch approved OD requests
$query = "SELECT * 
          FROM od_requests 
          WHERE status='approved' 
          AND roll_no IN (
              SELECT roll_number
              FROM users 
              WHERE mentor_id = {$_SESSION['user_id']}
          )";
$result = $conn->query($query);

// Count total approved OD requests
$totalApproved = $result->num_rows;

// Count certificates uploaded and not uploaded
$certificatesUploaded = 0;
$certificatesPending = 0;

while ($row = $result->fetch_assoc()) {
    if ($row['certificate_path']) {
        $certificatesUploaded++;
    } else {
        $certificatesPending++;
    }
}

// Reset result pointer
$result->data_seek(0);

// Check if export button is clicked
if (isset($_POST["export"])) {
    // Start output buffering
    ob_start();

    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle('OD Updates');
    $pdf->SetSubject('Approved OD Requests');
    $pdf->SetKeywords('TCPDF, PDF, OD, requests, approved');

    // Add a page
    $pdf->AddPage();

    // Set some content to print
    $html = '<h1>OD Updates</h1>';
    $html .= '<p>Total Approved OD Requests: ' . $totalApproved . '</p>';
    $html .= '<p>Certificates Uploaded: ' . $certificatesUploaded . '</p>';
    $html .= '<p>Certificates Pending: ' . $certificatesPending . '</p>';

    // Fetch data and create table
    if ($totalApproved > 0) {
        $html .= '<style>
                    table {
                        border-collapse: collapse;
                        width: 100%;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 8px;
                        text-align: left;
                    }
                    th {
                        background-color: #87cefa;
                        color: white;
                    }
                  </style>';
        $html .= '<table>';
        $html .= '<tr><th>Roll No</th><th>Name</th><th>Company Attended</th><th>Program Attended</th><th>Start Date</th><th>End Date</th><th>Event</th><th>Certificate</th></tr>';

        while ($row = $result->fetch_assoc()) {
            $html .= '<tr>';
            $html .= '<td>' . $row['roll_no'] . '</td>';
            $html .= '<td>' . $row['student_name'] . '</td>';
            $html .= '<td>' . $row['company_name'] . '</td>';
            $html .= '<td>' . $row['program_name'] . '</td>';
            $html .= '<td>' . $row['start_date'] . '</td>';
            $html .= '<td>' . $row['end_date'] . '</td>';
            $html .= '<td>' . $row['events'] . '</td>';
            if ($row['certificate_path']) {
                $html .= '<td>Uploaded</td>';
            } else {
                $html .= '<td>Yet to Upload</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</table>';
    } else {
        $html .= '<p>No records found.</p>';
    }

    // Output the HTML content to PDF
    $pdf->writeHTML($html, true, false, true, false, '');

    // Close and output PDF document
    ob_end_clean(); // Clean the output buffer
    $pdf->Output('OD_Updates.pdf', 'D');
    exit();
}

// Fetch data again for HTML display after PDF generation
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Check OD Updates</title>
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

        .summary {
            margin-bottom: 20px;
            font-size: 1.2em;
        }

        table {
            width: 90%;
            margin-top: 20px;
            border-collapse: collapse;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #87cefa;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .download-pdf {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 20px;
            cursor: pointer;
            border-radius: 5px;
        }

        .download-pdf:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/profile.jpg" alt="Profile Image">
        <div class="welcome">Welcome, <?php echo $_SESSION['name']; ?></div>
        <button class="logout" onclick="window.location.href='mentor_dashboard.php'">Back to home</button>
    </div>
    <div class="content">
        <div class="summary">
            <p>Total Approved OD Requests: <?php echo $totalApproved; ?></p>
            <p>Certificates Uploaded: <?php echo $certificatesUploaded; ?></p>
            <p>Certificates Pending: <?php echo $certificatesPending; ?></p>
        </div>
        <?php if ($result->num_rows > 0): ?>
        <table id="odTable">
            <tr>
                <th>Roll No</th>
                <th>Name</th>
                <th>Company Attended</th>
                <th>Program Attended</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Event</th>
                
                <th>Certificate</th>
            </tr>

            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['roll_no']; ?></td>
                <td><?php echo $row['student_name']; ?></td>
                <td><?php echo $row['company_name']; ?></td>
                <td><?php echo $row['program_name']; ?></td>
                <td><?php echo $row['start_date']; ?></td>
                <td><?php echo $row['end_date']; ?></td>
                <td><?php echo $row['events']; ?></td>
                <td>
                    <?php if ($row['certificate_path']): ?>
                        <a class="certificate-link" href="<?php echo $row['certificate_path']; ?>" target="_blank">View</a>
                    <?php else: ?>
                        Yet to Upload
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>   
        </table>
        <form method="post">
            <button type="submit" name="export" class="download-pdf">Download as PDF</button>
        </form>
        <?php else: ?>
        <p>No records found.</p>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>

<?php $conn->close(); ?>
