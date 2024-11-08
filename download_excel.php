<?php
include_once 'classes/db1.php';

$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : null;
$type = isset($_GET['type']) ? $_GET['type'] : null;

if ($event_id === null || $type === null) {
    exit('Event ID and type are required.');
}

// Fetch the relevant participants based on the type
$sql = "
    SELECT p.usn, p.name, p.phone, p.email, p.college
    FROM registered r
    JOIN participent p ON r.usn = p.usn
    WHERE r.event_id = $event_id AND p.student_type = '$type'
";

$result = mysqli_query($conn, $sql);

// Set headers to force the download of the file as an Excel file
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="'.$type.'_participants.xls"');

// Create the table structure for the Excel file
echo "<table border='1'>
        <tr>
            <th>USN</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>College</th>
        </tr>";

// Check if there are any records
if (mysqli_num_rows($result) > 0) {
    // Loop through each record and add it to the table
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['usn']}</td>
                <td>{$row['name']}</td>
                <td>{$row['phone']}</td>
                <td>{$row['email']}</td>
                <td>{$row['college']}</td>
              </tr>";
    }
} else {
    // If no data is found, display a message
    echo "<tr><td colspan='5'>No participants found.</td></tr>";
}

// Close the table
echo "</table>";

// Close the database connection
mysqli_close($conn);

// Ensure that no additional output is sent to the browser
exit();
?>
