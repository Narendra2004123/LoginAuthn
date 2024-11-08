<?php
include_once 'classes/db1.php'; // Ensure this file connects to your database

// Check if a student_type is set in the URL
$student_type = isset($_GET['student_type']) ? $_GET['student_type'] : null;

// Prepare the SQL query based on the selected student_type
$sql = "SELECT * FROM participent";

if ($student_type) {
    $sql .= " WHERE student_type = '$student_type'"; // Add filter for student_type
}

$result = mysqli_query($conn, $sql);

// Fetch distinct student types for the dropdown
$studentTypesResult = mysqli_query($conn, "SELECT DISTINCT student_type FROM participent");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participants</title>
    <link rel="stylesheet" href="path/to/your/bootstrap.css"> <!-- Include your CSS -->
    <style>
        .form-group {
            margin: 20px 0;
        }
        select {
            padding: 10px 15px;
            border-radius: 5px;
            border: 1px solid #007bff;
            background-color: #f8f9fa;
            font-size: 16px;
            color: #333;
        }
    </style>
</head>
<body>
    <?php require 'utils/adminHeader.php'; ?> <!-- Adjust path to your header file -->

    <div class="content">
        <div class="container">
            <h1>Participants List</h1>

            <!-- Dropdown for selecting student type -->
            <div class="form-group">
                <label for="studentTypeDropdown">Select Student Type:</label>
                <select id="studentTypeDropdown" onchange="filterParticipants()">
                    <option value="">All Participants</option>
                    <?php while ($studentType = mysqli_fetch_array($studentTypesResult)): ?>
                        <option value="<?php echo $studentType['student_type']; ?>" <?php if ($studentType['student_type'] == $student_type) echo 'selected'; ?>>
                            <?php echo ucfirst($studentType['student_type']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>USN</th>
                            <th>Name</th>
                            <th>Branch</th>
                            <th>Year</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>College</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_array($result)): ?>
                            <tr>
                                <td><?php echo $row['usn']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['branch']; ?></td>
                                <td><?php echo $row['year']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['phone']; ?></td>
                                <td><?php echo $row['college']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No participants found.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php require 'utils/footer.php'; ?> <!-- Adjust path to your footer file -->

    <script>
        function filterParticipants() {
            const studentType = document.getElementById('studentTypeDropdown').value;
            // Redirect to the same page with the selected student_type as a query parameter
            window.location.href = `participants.php?student_type=${studentType}`;
        }
    </script>
</body>
</html>
