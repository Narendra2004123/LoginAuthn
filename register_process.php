<?php
require 'vendor/autoload.php';
include 'classes/db1.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

\Stripe\Stripe::setApiKey('sk_test_51PsQ3zP9JX1hK4u8n3MDI3HVVlUoYrQcsYHXnmfVf9WXZUKHq7DsgjuxMDnxguoRwgjEiaD6XJBLO5HGvn4D2PIv002UlAp2kw'); // Replace with your Stripe Secret Key

if (isset($_POST['stripeToken']) && isset($_POST['usn']) && isset($_POST['event_id'])) {
    $usn = $_POST['usn'];
    $event_id = $_POST['event_id'];
    $token = $_POST['stripeToken'];

    try {
        // Create a charge using the token
        $charge = \Stripe\Charge::create([
            'amount' => 1000, // Amount in cents (10 USD)
            'currency' => 'usd',
            'description' => 'Event Registration Fee',
            'source' => $token,
        ]);

        if ($charge->status == 'succeeded') {
            // Payment successful, proceed with registration
            $check_participant_query = "SELECT * FROM participent WHERE usn = '$usn'";
            $participant_result = mysqli_query($conn, $check_participant_query);

            if (mysqli_num_rows($participant_result) > 0) {
                $participant = mysqli_fetch_assoc($participant_result);
                $email = $participant['email'];

                // Register the participant for the event
                $INSERT = "INSERT INTO registered (usn, event_id) VALUES ('$usn', '$event_id')";
                if ($conn->query($INSERT) === TRUE) {
                    // Send confirmation email using PHPMailer
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
                        $mail->Password = 'bulu nzzd oscw mxjv'; // Use your app password
                        $mail->SMTPSecure = 'tls';
                        $mail->Port = 587;

                        $mail->setFrom('narendrabaratam2004@gmail.com', 'Sanchalana 2K20');
                        $mail->addAddress($email);

                        $mail->isHTML(true);
                        $mail->Subject = 'Registration Completed';
                        $mail->Body = "
                            <h1>Registration Successful!</h1>
                            <p>Dear participant,</p>
                            <p>You have successfully registered for the event: <strong>$event_title</strong>.</p>
                            <p>Thank you for your participation in Sanchalana 2K20!</p>
                            <p>Best regards,<br>Sanchalana 2K20 Team</p>
                        ";

                        $mail->send();
                        echo "<script>alert('Payment successful! Registration completed, and a confirmation email has been sent.');window.location.href='confirmation.php';</script>";
                    } catch (Exception $e) {
                        echo "<script>alert('Registration successful but email could not be sent. Error: {$mail->ErrorInfo}');window.location.href='confirmation.php';</script>";
                    }
                } else {
                    echo "<script>alert('Error registering for the event.');window.location.href='register.php';</script>";
                }
            } else {
                echo "<script>alert('Student not found in participant table');window.location.href='register.php';</script>";
            }
        } else {
            echo "<script>alert('Payment failed or incomplete. Please try again.');window.location.href='register.php';</script>";
        }
    } catch (Exception $e) {
        echo "<script>alert('Error processing payment.');window.location.href='register.php';</script>";
    }
} else {
    echo "<script>alert('Payment details not provided.');window.location.href='register.php';</script>";
}
?>
