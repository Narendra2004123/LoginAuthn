<?php
include_once 'classes/db1.php'; // Include your database connection

// Fetch data from the database
$result = mysqli_query($conn, "SELECT * FROM sponsors ORDER BY company_name");

// Create a new Excel file
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="sponsor_list.xls"');

echo "<table border='1'>
        <tr>
            <th>Company Name</th>
            <th>Sponsor Name</th>
            <th>Referred By</th>
            <th>Amount</th>
        </tr>";

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_array($result)) {
        echo "<tr>
                <td>{$row['company_name']}</td>
                <td>{$row['sponsor_name']}</td>
                <td>{$row['referred_by']}</td>
                <td>{$row['amount']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No sponsors found.</td></tr>";
}

echo "</table>";
mysqli_close($conn);
exit();
?>
