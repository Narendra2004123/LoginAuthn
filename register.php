<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer

if (isset($_POST["update"])) {
    $usn = $_POST["usn"];
    $student_type = $_POST["student_type"];
    $name = $_POST["name"];
    $branch = $_POST["branch"];
    $year = $_POST["year"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $college = $_POST["college"];

    // Validation for USN (must be 10 characters long and alphanumeric)
    if (strlen($usn) !== 10 || !preg_match("/^[a-zA-Z0-9]*$/", $usn)) {
        echo "<script>
        alert('USN must be exactly 10 alphanumeric characters.');
        window.location.href='register.php';
        </script>";
        exit();
    }

    // Validation for email (must be a valid email format)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
        alert('Invalid email format.');
        window.location.href='register.php';
        </script>";
        exit();
    }

    // Ensure all fields are filled
    if (!empty($usn) && !empty($student_type) && !empty($name) && !empty($branch) && !empty($year) && !empty($email) && !empty($phone) && !empty($college)) {
        include 'classes/db1.php';

        // Check if the USN already exists
        $checkUSN = "SELECT * FROM participent WHERE usn = ?";
        $stmt = $conn->prepare($checkUSN);
        $stmt->bind_param("s", $usn);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // Insert new participant
            $INSERT = "INSERT INTO participent (usn, student_type, name, branch, year, email, phone, college) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtInsert = $conn->prepare($INSERT);
            $stmtInsert->bind_param("ssssisss", $usn, $student_type, $name, $branch, $year, $email, $phone, $college);

            if ($stmtInsert->execute()) {
                // Create a new PHPMailer instance
                $mail = new PHPMailer(true);
                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'narendrabaratam43@gmail.com';
                    $mail->Password = 'hges oneh rfsg azuv'; 
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    // Sender and recipient
                    $mail->setFrom('stokes8143@gmail.com', 'Stepcone 2K25 Team');
                    $mail->addAddress($email, $name);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Registration Confirmation';
                    $mail->Body = "<p>Dear $name,</p><p>You have successfully completed your basic registration for Sanchalana 2K20.</p><p>Thank you,<br>Sanchalana 2K20 Team</p>";

                    // Send the email
                    $mail->send();
                    echo "<script>
                    alert('Registered Successfully! A confirmation email has been sent to $email');
                    window.location.href='index.php';
                    </script>";
                } catch (Exception $e) {
                    echo "<script>
                    alert('Registration successful, but email could not be sent. Error: {$mail->ErrorInfo}');
                    window.location.href='index.php';
                    </script>";
                }
            } else {
                echo "<script>
                alert('Error occurred during registration. Please try again.');
                window.location.href='register.php';
                </script>";
            }
            $stmtInsert->close();
        } else {
            echo "<script>
            alert('Already registered with this USN');
            window.location.href='usn.php';
            </script>";
        }
        $stmt->close();
        $conn->close();
    } else {
        echo "<script>
        alert('All fields are required');
        window.location.href='register.php';
        </script>";
    }
}
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
                <label>Student USN:</label><br>
                <input type="text" name="usn" class="form-control" maxlength="10" required><br><br>

                <label>Student Type:</label><br>
                <select name="student_type" class="form-control" required>
                    <option value="Internal">Internal</option>
                    <option value="External">External</option>
                </select><br><br>

                <label>Student Name:</label><br>
                <input type="text" name="name" class="form-control" required><br><br>

                <label>Branch:</label><br>
                <select name="branch" class="form-control" required>
                    <option value="CSE">CSE</option>
                    <option value="IT">IT</option>
                    <option value="EEE">EEE</option>
                    <option value="ECE">ECE</option>
                    <option value="Mechanical">Mechanical</option>
                    <option value="Civil">Civil</option>
                    <option value="Architecture">Architecture</option>
                </select><br><br>

                <label>Year:</label><br>
                <select name="year" class="form-control" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select><br><br>

                <label>Email:</label><br>
                <input type="email" name="email" class="form-control" required><br><br>

                <label>Phone:</label><br>
                <input type="text" name="phone" class="form-control" required><br><br>

                <label>College:</label><br>
                <input type="text" name="college" class="form-control" required><br><br>

                <button type="submit" name="update">Submit</button><br><br>
                <a href="usn.php"><u>Already registered?</u></a>
            </form>
        </div>
    </div>
</div>

<?php require 'utils/footer.php'; ?>
</body>
</html>
