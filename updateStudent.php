<?php
include 'classes/db1.php';
$id = $_GET['id'];

// Make sure to include the Composer autoloader
require 'vendor/autoload.php'; // Ensure this path is correct

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Stepcone 2K25</title>
    <?php require 'utils/styles.php'; ?>
</head>
<body>
    <?php require 'utils/header.php'; ?>
    <div class="content">
        <div class="container">
            <div class="col-md-6 col-md-offset-3">
                <form method="POST">
                    <label>Student Coordinator Name</label><br>
                    <input type="text" name="st_name" required class="form-control"><br><br>
                    <label>Student Coordinator Phone</label><br>
                    <input type="text" name="phone" required class="form-control"><br><br>
                    <button type="submit" name="update" class="btn btn-default">Update</button>
                </form>
            </div>
        </div>
    </div>

    <?php require 'utils/footer.php'; ?>
</body>
</html>

<?php
if (isset($_POST["update"])) {
    $name = $_POST["st_name"];
    $phone = $_POST["phone"];

    // Update the student coordinator details
    $sql = "UPDATE student_coordinator SET phone='$phone', st_name='$name' WHERE sid='$id'";
    if ($conn->query($sql) === true) {
        // Fetch the event ID associated with this coordinator
        $eventIdQuery = "SELECT event_id FROM student_coordinator WHERE sid='$id'";
        $eventIdResult = $conn->query($eventIdQuery);
        $eventIdRow = $eventIdResult->fetch_assoc();
        $eventId = $eventIdRow['event_id'];

        // Fetch the event title (make sure the column name is correct, e.g., `event_id` instead of `id`)
        $eventTitleQuery = "SELECT event_title FROM events WHERE event_id='$eventId'"; // updated query
        $eventTitleResult = $conn->query($eventTitleQuery);
        $eventTitleRow = $eventTitleResult->fetch_assoc();
        $eventTitle = $eventTitleRow['event_title'];

        // Fetch students who are registered for the event
        $registeredStudentsQuery = "SELECT p.email FROM registered r JOIN participent p ON r.usn = p.usn WHERE r.event_id='$eventId'";
        $studentsResult = $conn->query($registeredStudentsQuery);

        // Prepare the email
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'narendrabaratam43@gmail.com'; // Your SMTP email
            $mail->Password = 'hges oneh rfsg azuv'; // Your SMTP email password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Email content
            $mail->setFrom('narendrabaratam43@gmail.com', 'Stepcone 2K25');
            $mail->Subject = 'Student Coordinator Details Updated';
            $mail->isHTML(true);
            $mail->Body = "Dear Student,<br>The details of your Student Coordinator for the event '$eventTitle' have been updated:<br>
                           Name: $name<br>
                           Phone: $phone<br>";

            // Send email to each registered student
            while ($student = $studentsResult->fetch_assoc()) {
                $mail->addAddress($student['email']); // Add each student's email address
                $mail->send();
                $mail->clearAddresses(); // Clear addresses for the next iteration
            }

            echo "<script>
            alert('Updated Successfully and emails sent to registered students!');
            window.location.href='stu_cordinator.php';
            </script>";
        } catch (Exception $e) {
            echo "<script>
            alert('Email could not be sent. Mailer Error: {$mail->ErrorInfo}');
            window.location.href='stu_cordinator.php';
            </script>";
        }
    } else {
        echo "<script>
        window.location.href='updateStudent.php';
        </script>";
    }
}
?>
