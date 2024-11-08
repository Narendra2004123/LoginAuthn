<?php
session_start();
include 'classes/db1.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Set your Stripe Secret Key
\Stripe\Stripe::setApiKey('sk_test_51PsQ3zP9JX1hK4u8n3MDI3HVVlUoYrQcsYHXnmfVf9WXZUKHq7DsgjuxMDnxguoRwgjEiaD6XJBLO5HGvn4D2PIv002UlAp2kw'); 

// Initialize PHPMailer
$mail = new PHPMailer(true);

// Redirect to Stripe Checkout if the form is submitted
if (isset($_POST['usn']) && isset($_POST['event_id'])) {
    $usn = $_POST['usn'];
    $event_id = $_POST['event_id'];

    // Check if the USN exists in the participant table
    $check_usn_query = "SELECT * FROM participent WHERE usn = ?";
    $stmt = $conn->prepare($check_usn_query);
    $stmt->bind_param("s", $usn); // Assuming USN is a string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // USN exists, save user information in the session
        $_SESSION['usn'] = $usn;
        $_SESSION['event_id'] = $event_id;

        // Insert the registration details into the `registered` table immediately after form submission
        $insert_registered_query = "INSERT INTO registered (usn, event_id) VALUES ('$usn', '$event_id')";
        if (!mysqli_query($conn, $insert_registered_query)) {
            echo "Error inserting registration details into `registered` table: " . mysqli_error($conn);
        } else {
            // Send an initial email to notify the participant that their registration process has started
            try {
                // Get participant info for email (assuming the `participent` table contains name and email)
                $participant_query = "SELECT name, email FROM participent WHERE usn = ?";
                $stmt = $conn->prepare($participant_query);
                $stmt->bind_param("s", $usn);
                $stmt->execute();
                $participant_result = $stmt->get_result();
                $participant = $participant_result->fetch_assoc();
                $name = $participant['name'];
                $email = $participant['email'];

                // Set up PHPMailer
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'narendrabaratam43@gmail.com'; // Your email
                $mail->Password = 'hges oneh rfsg azuv'; // Use your app password here
                $mail->SMTPSecure = 'tls'; // Use encryption
                $mail->Port = 587;

                $mail->setFrom('narendrabaratam43@gmail.com', 'Sanchalana 2K20');
                $mail->addAddress($email); // Send email to the participant

                // Preliminary email content
                $mail->isHTML(true);
                $mail->Subject = 'Registration Process Started';
                $mail->Body = "
                    <h1>Registration Process Started!</h1>
                    <p>Dear $name,</p>
                    <p>Your registration process for Sanchalana 2K20 has started. Please proceed with the payment to complete your registration.</p>
                    <p>Best regards,<br>Sanchalana 2K20 Team</p>
                ";

                $mail->send(); // Send the email

            } catch (Exception $e) {
                echo "Mailer Error: " . $mail->ErrorInfo;
            }

            // Redirect to the Stripe Checkout page
            header('Location: https://buy.stripe.com/test_14k3cmgWp9ZN9WgdQQ');
            exit(); // Ensure no further code is executed after redirection
        }
    } else {
        // USN does not exist, show an alert
        echo "<script>
                alert('You are not done with your basic registration.'); 
                window.location.href = 'register.php'; // Replace with your registration page URL
              </script>";
    }

    $stmt->close();
}
?>
