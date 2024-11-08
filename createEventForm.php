<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>SStepcone 2K25</title>
    <?php require 'utils/styles.php'; ?> <!-- CSS links. file found in utils folder -->
</head>
<body>
    <?php require 'utils/adminHeader.php'; ?>
    <form method="POST">
        <div class="w3-container"> 
            <div class="content">
                <div class="container">
                    <div class="col-md-6 col-md-offset-3">
                        <label>Event ID:</label><br>
                        <input type="number" name="event_id" required class="form-control"><br><br>

                        <label>Event Name:</label><br>
                        <input type="text" name="event_title" required class="form-control"><br><br>

                        <label>Event Price:</label><br>
                        <input type="number" name="event_price" required class="form-control"><br><br>

                        <label>Upload Path to Image:</label><br>
                        <input type="text" name="img_link" required class="form-control"><br><br>

                        <label>Type_ID:</label><br>
                        <input type="number" name="type_id" required class="form-control"><br><br>

                        <label>Event Date:</label><br>
                        <input type="date" name="Date" required class="form-control"><br><br>

                        <label>Event Time:</label><br>
                        <input type="text" name="time" required class="form-control"><br><br>

                        <label>Event Location:</label><br>
                        <input type="text" name="location" required class="form-control"><br><br>

                        <label>Staff Coordinator Name:</label><br>
                        <input type="text" name="sname" required class="form-control"><br><br>

                        <label>Staff Coordinator Phone Number:</label><br>
                        <input type="text" name="sphone" required class="form-control"><br><br>

                        <label>Student Coordinator Name:</label><br>
                        <input type="text" name="st_name" required class="form-control"><br><br>

                        <label>Student Coordinator Phone Number:</label><br>
                        <input type="text" name="st_phone" required class="form-control"><br><br>

                        <button type="submit" name="update" class="btn btn-default pull-right">Create Event <span class="glyphicon glyphicon-send"></span></button>
                        <a class="btn btn-default navbar-btn" href="adminPage.php"><span class="glyphicon glyphicon-circle-arrow-left"></span> Back</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</body>
<?php require 'utils/footer.php'; ?>
</html>

<?php
// PHP code to handle form submission and email sending
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer autoload

if (isset($_POST["update"])) {
    $event_id = $_POST["event_id"];
    $event_title = $_POST["event_title"];
    $event_price = $_POST["event_price"];
    $img_link = $_POST["img_link"];
    $type_id = $_POST["type_id"];
    $name = $_POST["sname"];
    $st_name = $_POST["st_name"];
    $Date = $_POST["Date"];
    $time = $_POST["time"];
    $location = $_POST["location"];
    $sphone = $_POST["sphone"];
    $st_phone = $_POST["st_phone"];

    if (!empty($event_id) && !empty($event_title) && !empty($event_price) && !empty($img_link) && !empty($type_id)) {
        include 'classes/db1.php';

        // Prepare individual INSERT queries
        $INSERT_EVENTS = "INSERT INTO events(event_id, event_title, event_price, img_link, type_id) 
                          VALUES ($event_id, '$event_title', $event_price, '$img_link', $type_id);";

        $INSERT_EVENT_INFO = "INSERT INTO event_info(event_id, Date, time, location) 
                              VALUES ($event_id, '$Date', '$time', '$location');";

        $INSERT_STUDENT_COORDINATOR = "INSERT INTO student_coordinator(sid, st_name, phone, event_id) 
                                        VALUES ($event_id, '$st_name', '$st_phone', $event_id);";

        $INSERT_STAFF_COORDINATOR = "INSERT INTO staff_coordinator(stid, name, phone, event_id) 
                                      VALUES ($event_id, '$name', '$sphone', $event_id);";

        // Execute each query separately
        if ($conn->query($INSERT_EVENTS) === TRUE && 
            $conn->query($INSERT_EVENT_INFO) === TRUE &&
            $conn->query($INSERT_STUDENT_COORDINATOR) === TRUE &&
            $conn->query($INSERT_STAFF_COORDINATOR) === TRUE) {
            
            // Fetch all participant emails
            $query = "SELECT email FROM participent";
            $result = $conn->query($query);
            if ($result->num_rows > 0) {
                // Initialize PHPMailer
                $mail = new PHPMailer(true);
                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'narendrabaratam43@gmail.com';
                    $mail->Password = 'hges oneh rfsg azuv'; // Replace with your app password
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    // Sender information
                    $mail->setFrom('narendrabaratam43@gmail.com', 'Stepcone 2K25');

                    // Email content
                    $mail->isHTML(true);
                    $mail->Subject = 'New Event Created: ' . $event_title;
                    $mail->Body = "
                        Dear Participant,<br><br>
                        A new event has been created:<br>
                        <b>Event Name:</b> $event_title<br>
                        <b>Date:</b> $Date<br>
                        <b>Time:</b> $time<br>
                        <b>Location:</b> $location<br>
                        <b>Price:</b> $event_price<br><br>
                        We hope to see you there!<br>
                        Regards,<br>Stepcone 2K25 Team
                    ";

                    // Add recipient emails
                    while ($row = $result->fetch_assoc()) {
                        $mail->addAddress($row['email']);
                    }

                    $mail->send(); // Send email to all participants
                    echo "<script>
                            alert('Event created successfully! Emails have been sent to all participants.');
                            window.location.href='adminPage.php';
                          </script>";
                } catch (Exception $e) {
                    echo "<script>
                            alert('Event created successfully, but emails could not be sent. Error: {$mail->ErrorInfo}');
                            window.location.href='adminPage.php';
                          </script>";
                }
            }
        } else {
            echo "<script>
                    alert('Event already exists!');
                    window.location.href='createEventForm.php';
                  </script>";
        }

        $conn->close();
    } else {
        echo "<script>
                alert('All fields are required');
                window.location.href='createEventForm.php';
              </script>";
    }
}
?>
