<?php
session_start();
include 'classes/db1.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Retrieve the stored user data from session
$usn = $_SESSION['usn'];
$event_id = $_SESSION['event_id'];

if ($usn && $event_id) {
    // Check if the participant exists in the database
    $check_participant_query = "SELECT * FROM participent WHERE usn = '$usn'";
    $participant_result = mysqli_query($conn, $check_participant_query);

    if (mysqli_num_rows($participant_result) > 0) {
        $participant = mysqli_fetch_assoc($participant_result);
        $email = $participant['email'];

        // Register the participant for the selected event
        $INSERT = "INSERT INTO registered (usn, event_id) VALUES ('$usn', '$event_id')";
        if ($conn->query($INSERT) === TRUE) {
            // Send a confirmation email to the participant
            $mail = new PHPMailer(true);
            $event_query = "SELECT event_title FROM events WHERE event_id = '$event_id'";
            $event_result = mysqli_query($conn, $event_query);
            $event = mysqli_fetch_assoc($event_result);
            $event_title = $event['event_title'];

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'narendrabaratam2004@gmail.com';
                $mail->Password = 'bulu nzzd oscw mxjv'; // Use your app-specific password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('your_email@gmail.com', 'Sanchalana 2K20');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Registration Confirmation';
                $mail->Body = "
                    <h1>Registration Successful!</h1>
                    <p>Dear participant,</p>
                    <p>You have successfully registered for the event: <strong>$event_title</strong>.</p>
                    <p>Thank you for participating in Sanchalana 2K20!</p>
                    <p>Best regards,<br>Sanchalana 2K20 Team</p>
                ";

                $mail->send();
                echo "<script>alert('Payment successful! Registration completed, and a confirmation email has been sent.');window.location.href='confirmation.php';</script>";
            } catch (Exception $e) {
                echo "<script>alert('Registration successful, but email could not be sent. Error: {$mail->ErrorInfo}');window.location.href='confirmation.php';</script>";
            }
        } else {
            echo "<script>alert('Error occurred while registering for the event. Please try again.');window.location.href='index.php';</script>";
        }
    } else {
        echo "<script>alert('Student not found in the participant table. Please contact support.');window.location.href='index.php';</script>";
    }
} else {
    echo "<script>alert('Invalid registration data. Please try again.');window.location.href='index.php';</script>";
}
?>
