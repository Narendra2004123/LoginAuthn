<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure PHPMailer is autoloaded
include_once 'classes/db1.php';

if (isset($_GET['usn'])) {
    $usn = mysqli_real_escape_string($conn, $_GET['usn']);
    
    // Fetch the student data
    $result = mysqli_query($conn, "SELECT * FROM participent WHERE usn='$usn'");
    $student = mysqli_fetch_assoc($result);

    if (!$student) {
        echo "<script>alert('Student not found!'); window.location.href='adminPage.php';</script>";
        exit();
    }
}

if (isset($_POST['acceptUpdate'])) {
    // Update logic when "Accept" is clicked
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $branch = mysqli_real_escape_string($conn, $_POST['branch']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $college = mysqli_real_escape_string($conn, $_POST['college']);

    // Update the student details in the database
    $updateQuery = "UPDATE participent SET name='$name', branch='$branch', year='$year', email='$email', phone='$phone', college='$college' WHERE usn='$usn'";
    
    if (mysqli_query($conn, $updateQuery)) {
        // Send confirmation email to the updated email address
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

            // Recipients
            $mail->setFrom('narendrabaratam43@gmail.com', 'Stepcone 2K25'); // Your email and display name
            $mail->addAddress($email, $name); // Send email to the new email address

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Update Confirmation';
            $mail->Body = "Dear $name,<br>Your details have been updated successfully with the following information:<br>
                           Name: $name<br>
                           Branch: $branch<br>
                           Semester: $year<br>
                           Email: $email<br>
                           Phone: $phone<br>
                           College: $college<br>";

            $mail->send();
            echo "<script>alert('Details updated and email sent to the updated email address: $email!'); window.location.href='adminPage.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Email could not be sent. Mailer Error: {$mail->ErrorInfo}'); window.location.href='adminPage.php';</script>";
        }
    } else {
        echo "<script>alert('Error updating details: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student</title>
    <?php require 'utils/styles.php'; ?>

    <script>
        function showConfirmationModal() {
            // Show the modal
            document.getElementById("confirmationModal").style.display = "block";
        }

        function acceptUpdate() {
            // Hide the modal
            document.getElementById("confirmationModal").style.display = "none";
            // Submit the form with action to accept update
            document.getElementById("updateForm").action = "";
            document.getElementById("updateForm").submit();
        }

        function rejectUpdate() {
            // Hide the modal
            document.getElementById("confirmationModal").style.display = "none";
            alert("Update rejected.");
        }
    </script>

    <style>
        /* Styles for modal */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            padding-top: 60px; /* Location of the box */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; /* Centered */
            padding: 20px;
            border: 1px solid #888;
            width: 50%; /* Could be more or less, depending on screen size */
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php require 'utils/adminHeader.php'; ?>

    <div class="container">
        <h1 class="text-center">Update Student Details</h1>
        <div class="col-md-6 col-md-offset-3">
            <form method="POST" id="updateForm">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="branch">Branch:</label>
                    <input type="text" name="branch" value="<?php echo htmlspecialchars($student['branch']); ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="year">Year:</label>
                    <input type="number" name="year" value="<?php echo htmlspecialchars($student['year']); ?>"   class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="college">College:</label>
                    <input type="text" name="college" value="<?php echo htmlspecialchars($student['college']); ?>" class="form-control" required>
                </div>
                <button type="button" onclick="showConfirmationModal()" class="btn btn-primary">Update</button>
                <a href="adminPage.php" class="btn btn-secondary">Cancel</a>
                <input type="hidden" name="acceptUpdate" value="1">
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('confirmationModal').style.display='none'">&times;</span>
            <h3>Confirm Update</h3>
            <p>Are you sure you want to update the details?</p>
            <button onclick="acceptUpdate()" class="btn btn-success">Accept</button>
            <button onclick="rejectUpdate()" class="btn btn-danger">Reject</button>
        </div>
    </div>

    <?php require 'utils/footer.php'; ?>
</body>
</html>
