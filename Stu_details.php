<?php
include_once 'classes/db1.php';

// Fetch all events for the dropdown
$event_query = "SELECT event_id, event_title FROM events";
$event_result = mysqli_query($conn, $event_query);

// Check if the query was successful
if (!$event_result) {
    echo "Error executing query: " . mysqli_error($conn);
    exit; // Stop the script if there's an error
}

// Check for selected event
$selected_event_id = isset($_GET['event_id']) ? $_GET['event_id'] : '';

// Adjust the SQL query based on the selected event
$query = "SELECT p.usn, p.student_type, p.name, p.branch, p.year, p.email, p.phone, p.college, e.event_title, r.event_id 
          FROM events e 
          JOIN registered r ON e.event_id = r.event_id 
          JOIN participent p ON r.usn = p.usn";

// If an event is selected, add a WHERE clause to the query
if ($selected_event_id) {
    $query .= " WHERE r.event_id = ?";
}

$query .= " ORDER BY e.event_title";

$stmt = $conn->prepare($query);

// Bind the parameter if an event is selected
if ($selected_event_id) {
    $stmt->bind_param("i", $selected_event_id);
}

$stmt->execute();
$result = $stmt->get_result();

// Check if the query was successful
if (!$result) {
    echo "Error executing query: " . mysqli_error($conn);
    exit; // Stop the script if there's an error
}

// Handle deletion
if (isset($_GET['delete_usn']) && isset($_GET['event_id'])) {
    $delete_usn = $_GET['delete_usn'];
    $event_id = $_GET['event_id'];

    // Delete the participant from the registered table for the specific event
    $delete_query = "DELETE FROM registered WHERE usn = ? AND event_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("si", $delete_usn, $event_id);
    $stmt->execute();

    // Redirect to the same page to refresh the list
    header("Location: Stu_details.php?event_id=" . $event_id);
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Stepcone 2K25</title>

    <style>
    /* Centering the form and making the button wider */
    .download-form {
        display: flex;
        justify-content: center; /* Center the form horizontally */
        margin: 20px 0; /* Add some margin to separate the button from other content */
    }

    .wide-button {
        width: 200px; /* Adjust the width as needed to make the button wider */
        padding: 10px 0; /* Add padding to increase the button height */
        font-size: 16px; /* Increase the font size for better readability */
    }

    /* Styles for the select dropdown */
    select {
        width: 100%; /* Full width */
        padding: 10px; /* Padding for better spacing */
        font-size: 16px; /* Font size */
        border: 1px solid #ccc; /* Border styling */
        border-radius: 4px; /* Rounded corners */
        background-color: #f8f8f8; /* Light background */
        color: #333; /* Text color */
        transition: border-color 0.3s; /* Smooth transition for border color */
    }

    /* Change border color on focus */
    select:focus {
        border-color: #007bff; /* Change border color on focus */
        outline: none; /* Remove outline */
    }

    /* Optional: Add hover effect */
    select:hover {
        border-color: #007bff; /* Change border color on hover */
    }
</style>


    <?php require 'utils/styles.php'; ?><!-- CSS links. File found in utils folder -->
</head>

<body>
    <?php include 'utils/adminHeader.php' ?>
    <div class="content">
        <div class="container">
            <h1>Student Details</h1>
            
            <!-- Event Selection Dropdown -->
            <form method="GET" action="Stu_details.php">
                <div class="form-group">
                    <label for="event_id">Select Event:</label>
                    <select name="event_id" id="event_id" onchange="this.form.submit()">
                        <option value="">ALL EVENTS</option>
                        <?php while ($event_row = mysqli_fetch_assoc($event_result)) { ?>
                            <option value="<?php echo $event_row['event_id']; ?>" <?php echo $event_row['event_id'] == $selected_event_id ? 'selected' : ''; ?>>
                                <?php echo $event_row['event_title']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </form>

            <?php
            if (mysqli_num_rows($result) > 0) {
            ?>
                <table class="table table-hover">
                    <tr>
                        <th>USN</th>
                        <th>Student Type</th>
                        <th>Name</th>
                        <th>Branch</th>
                        <th>Year</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>College</th>
                        <th>Event</th>
                        <th>Action</th> <!-- New column for action -->
                    </tr>
                    <?php
                    while ($row = mysqli_fetch_array($result)) {
                    ?>
                        <tr>
                            <td><?php echo $row["usn"]; ?></td>
                            <td><?php echo $row["student_type"]; ?></td>
                            <td><?php echo $row["name"]; ?></td>
                            <td><?php echo $row["branch"]; ?></td>
                            <td><?php echo $row["year"]; ?></td>
                            <td><?php echo $row["email"]; ?></td>
                            <td><?php echo $row["phone"]; ?></td>
                            <td><?php echo $row["college"]; ?></td>
                            <td><?php echo $row["event_title"]; ?></td>
                            <td>
                                <a href="updateStudent1.php?usn=<?php echo $row['usn']; ?>" class="btn btn-primary">Update</a>
                                <a href="?delete_usn=<?php echo $row['usn']; ?>&event_id=<?php echo $row['event_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this participant from the event?');">Delete</a>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
            <?php
            } else {
                echo "No results found.";
            }
            ?>
            <!-- Download Excel button -->
            <div class="container">
                <form action="downloadExcel.php" method="post" class="download-form">
                    <button type="submit" class="btn btn-success wide-button">Download Excel</button>
                </form>
            </div>

        </div>
    </div>
    <?php include 'utils/footer.php'; ?>
</body>
</html>
