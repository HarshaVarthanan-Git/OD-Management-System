<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include 'db.php'; // Include your database connection file

// Check database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch filters from the form
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';
$section = isset($_POST['section']) ? $_POST['section'] : '';

// Define the query based on the filters
$query = "SELECT * FROM od_requests WHERE status = 'Approved'";
$conditions = [];

if (!empty($start_date) && !empty($end_date)) {
    $conditions[] = "request_date BETWEEN '$start_date' AND '$end_date'";
}

if (!empty($section)) {
    $section_conditions = [];
    switch ($section) {
        case 'II_CSE_A':
            $section_conditions[] = "roll_no LIKE '23CSEA%'";
            break;
        case 'II_CSE_B':
            $section_conditions[] = "roll_no LIKE '23CSEB%'";
            break;
        case 'II_CSE_C':
            $section_conditions[] = "roll_no LIKE '23CSEC%'";
            break;
        case 'III_CSE_A':
            $section_conditions[] = "roll_no LIKE '22CSEA%' OR roll_no LIKE '23LCSEA%'";
            break;
        case 'III_CSE_B':
            $section_conditions[] = "roll_no LIKE '22CSEB%' OR roll_no LIKE '23LCSEB%'";
            break;
        case 'III_CSE_C':
            $section_conditions[] = "roll_no LIKE '22CSEC%' OR roll_no LIKE '23LCSEC%'";
            break;
        default:
            // Handle any other case
            break;
    }

    if (!empty($section_conditions)) {
        $conditions[] = '(' . implode(' OR ', $section_conditions) . ')';
    }
}

if (count($conditions) > 0) {
    $query .= " AND " . implode(' AND ', $conditions);
}

// Execute the query
$result = mysqli_query($conn, $query);

// Check for query errors
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Map mentor IDs to mentor names
$mentor_names = [
    3 => 'Senthil Murugan',
    2 => 'Murali Shankar',
    4 => 'Padmadevi',
    5 => 'Ponmalar',
    6 => 'Shaheeb Jath',
    7 => 'Kavitha',
    8 => 'Lavanya',
    9 => 'Pavithra',
    10 => 'Swedheetha',
    220 => 'ShanthaSheela',
    221 => 'Selva Lakshmi',
    222 => 'Sarala',
    664 => 'Benazir Begum',
    665 => 'Rajeswary A N',
    666 => 'Balamurali Krishnan',
    667 => 'Niranjana',
    668 => 'Rajesway A M',
    669 => 'Ramsankar G'
];

?>

<!DOCTYPE html>
<html>
<head>
    <title>OD Request Report</title>
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
            width: 100%;
            padding: 0 20px;
            box-sizing: border-box;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #87cefa;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        .pagination a {
            margin: 0 5px;
            padding: 10px 20px;
            text-decoration: none;
            background-color: #87cefa;
            color: white;
            border-radius: 5px;
        }
        .pagination a:hover {
            background-color: #00bfff;
        }
        .download-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
        .download-btn:hover {
            background-color: #218838;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.14/jspdf.plugin.autotable.min.js"></script>
    <script>
        function downloadPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('landscape');

            doc.autoTable({
                html: '#od-table',
                theme: 'grid',
                headStyles: { fillColor: [135, 206, 250] },
                alternateRowStyles: { fillColor: [240, 248, 255] }
            });

            doc.save('od_request_report.pdf');
        }
    </script>
</head>
<body>
    <div class="header">
        <img src="images/profile.jpg" alt="Profile Image">
        <div class="welcome">Welcome, <?php echo isset($_SESSION['name']) ? $_SESSION['name'] : 'Guest'; ?></div>
        <button class="logout" onclick="window.location.href='mentor_dashboard.php'">Back to home</button>
    </div>
    <div class="content">
        <h2>OD Report</h2>
        <form method="POST" action="">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" required>
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" required>
            <label for="section">Section:</label>
            <select id="section" name="section">
                <option value="">All Sections</option>
                <option value="II_CSE_A" <?php echo ($section == 'II_CSE_A') ? 'selected' : ''; ?>>II CSE A</option>
                <option value="II_CSE_B" <?php echo ($section == 'II_CSE_B') ? 'selected' : ''; ?>>II CSE B</option>
                <option value="II_CSE_C" <?php echo ($section == 'II_CSE_C') ? 'selected' : ''; ?>>II CSE C</option>
                <option value="III_CSE_A" <?php echo ($section == 'III_CSE_A') ? 'selected' : ''; ?>>III CSE A</option>
                <option value="III_CSE_B" <?php echo ($section == 'III_CSE_B') ? 'selected' : ''; ?>>III CSE B</option>
                <option value="III_CSE_C" <?php echo ($section == 'III_CSE_C') ? 'selected' : ''; ?>>III CSE C</option>
            </select>
            <button type="submit">Filter</button>
        </form>

        <?php
        if (mysqli_num_rows($result) > 0) {
            echo '<table id="od-table">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Roll No</th>';
            echo '<th>Student Name</th>';
            echo '<th>Company Name</th>';
            echo '<th>Program</th>';
            echo '<th>Start Date</th>';
            echo '<th>End Date</th>';
            echo '<th>Events</th>';
            echo '<th>Mentor</th>';
            echo '<th>Request Date</th>';
            echo '<th>Status</th>';
            echo '<th>Paper Presentation Details</th>';
            echo '<th>Project Details</th>';
            echo '<th>Other Event Details</th>';
            echo '<th>Certificate Uploaded</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            
            while ($row = mysqli_fetch_assoc($result)) {
                $mentor_name = isset($mentor_names[$row['mentor_id']]) ? $mentor_names[$row['mentor_id']] : 'Unknown';
                $certificate_status = $row['certificate_uploaded'] ? 'Yes' : 'No';

                echo "<tr>";
                echo "<td>{$row['roll_no']}</td>";
                echo "<td>{$row['student_name']}</td>";
                echo "<td>{$row['company_name']}</td>";
                echo "<td>{$row['program_name']}</td>";
                echo "<td>{$row['start_date']}</td>";
                echo "<td>{$row['end_date']}</td>";
                echo "<td>{$row['events']}</td>";
                echo "<td>{$mentor_name}</td>";
                echo "<td>{$row['request_date']}</td>";
                echo "<td>{$row['status']}</td>";
                echo "<td>{$row['PaperPresentationDetails']}</td>";
                echo "<td>{$row['ProjectDetails']}</td>";
                echo "<td>{$row['OtherEventDetails']}</td>";
                echo "<td>{$certificate_status}</td>";
                echo "</tr>";
            }
            echo '</tbody>';
            echo '</table>';

            // Pagination logic
            $total_records = mysqli_num_rows($result);
            $records_per_page = 10;
            $total_pages = ceil($total_records / $records_per_page);

            echo '<div class="pagination">';
            for ($i = 1; $i <= $total_pages; $i++) {
                echo "<a href='?page=$i'>$i</a>";
            }
            echo '</div>';
        } else {
            echo '<p>No records found.</p>';
        }
        ?>

        <button class="download-btn" onclick="downloadPDF()">Download PDF</button>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>
