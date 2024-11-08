<?php
include_once 'classes/db1.php'; // Include your database connection

// Fetch student details from the database
$result = mysqli_query($conn, "SELECT events.event_title, r.usn, p.name, p.branch, p.sem, p.email, p.phone, p.college 
                                FROM events 
                                JOIN registered r ON events.event_id = r.event_id 
                                JOIN participent p ON r.usn = p.usn 
                                ORDER BY event_title");

// Set headers to force the download of the file as an Excel file
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="student_details.xls"');

// Create the table structure for the Excel file
echo "<table border='1'>
        <tr>
            <th>USN</th>
            <th>Name</th>
            <th>Branch</th>
            <th>Semester</th>
            <th>Email</th>
            <th>Phone</th>
            <th>College</th>
            <th>Event</th>
        </tr>";

// Check if there are any student records in the result set
if (mysqli_num_rows($result) > 0) {
    // Loop through each student record and add it to the table
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['usn']}</td>
                <td>{$row['name']}</td>
                <td>{$row['branch']}</td>
                <td>{$row['sem']}</td>
                <td>{$row['email']}</td>
                <td>{$row['phone']}</td>
                <td>{$row['college']}</td>
                <td>{$row['event_title']}</td>
              </tr>";
    }
} else {
    // If no student data is found, display a message in the table
    echo "<tr><td colspan='8'>No student details found.</td></tr>";
}

// Close the table
echo "</table>";

// Close the database connection
mysqli_close($conn);

// Ensure that no additional output is sent to the browser
exit();
?>
