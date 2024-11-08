<?php
include_once 'classes/db1.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usn = mysqli_real_escape_string($conn, $_POST['usn']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $branch = mysqli_real_escape_string($conn, $_POST['branch']);
    $sem = mysqli_real_escape_string($conn, $_POST['sem']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $college = mysqli_real_escape_string($conn, $_POST['college']);

    // Update the student details in the database
    $updateQuery = "UPDATE participent 
                    SET name='$name', branch='$branch', sem='$sem', email='$email', phone='$phone', college='$college' 
                    WHERE usn='$usn'";

    if (mysqli_query($conn, $updateQuery)) {
        echo "Details updated successfully!";
    } else {
        echo "Error updating details: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
