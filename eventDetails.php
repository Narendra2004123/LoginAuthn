<?php
include_once 'classes/db1.php';

$event_id = isset($_GET['id']) ? intval($_GET['id']) : null;
if ($event_id === null) {
    // Handle error, event_id not provided
    exit('Event ID is required.');
}

// Fetch event details based on event_id
$sql = "
    SELECT e.event_title, e.event_price, ef.Date, ef.time, ef.location, 
           s.name AS staff_name, s.phone AS staff_contact, 
           st.st_name AS student_name, st.phone AS student_contact,
           COUNT(CASE WHEN p.student_type = 'Internal' THEN 1 END) AS internal_count,
           COUNT(CASE WHEN p.student_type = 'External' THEN 1 END) AS external_count
    FROM events e
    JOIN event_info ef ON e.event_id = ef.event_id
    JOIN student_coordinator st ON e.event_id = st.event_id
    JOIN staff_coordinator s ON e.event_id = s.event_id
    LEFT JOIN registered r ON e.event_id = r.event_id
    LEFT JOIN participent p ON r.usn = p.usn
    WHERE e.event_id = $event_id
    GROUP BY e.event_id
";

$result = mysqli_query($conn, $sql);
$eventDetails = mysqli_fetch_array($result);

// Fetch internal participants
$internalSql = "
    SELECT p.usn, p.name, p.phone, p.email, p.college
    FROM registered r
    JOIN participent p ON r.usn = p.usn
    WHERE r.event_id = $event_id AND p.student_type = 'Internal'
";

$internalResult = mysqli_query($conn, $internalSql);
$internalParticipants = [];
while ($row = mysqli_fetch_assoc($internalResult)) {
    $internalParticipants[] = $row;
}

// Fetch external participants
$externalSql = "
    SELECT p.usn, p.name, p.phone, p.email, p.college
    FROM registered r
    JOIN participent p ON r.usn = p.usn
    WHERE r.event_id = $event_id AND p.student_type = 'External'
";

$externalResult = mysqli_query($conn, $externalSql);
$externalParticipants = [];
while ($row = mysqli_fetch_assoc($externalResult)) {
    $externalParticipants[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $eventDetails['event_title']; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f8f9fa;
        }
        h1 {
            color: #007bff;
        }
        p {
            font-size: 18px;
            line-height: 1.5;
        }
        .back-button {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
        .sop-container {
            background-color: #e9ecef;
            padding: 20px;
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .sop-container h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 15px;
        }
        .sop-container p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
        }
        .participant-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .participant-table th, .participant-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .participant-table th {
            background-color: #007bff;
            color: white;
        }
        .download-button {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .download-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <h1><?php echo $eventDetails['event_title']; ?></h1>
    <p><strong>Price:</strong> <?php echo $eventDetails['event_price']; ?></p>
    <p><strong>Date:</strong> <?php echo $eventDetails['Date']; ?></p>
    <p><strong>Time:</strong> <?php echo $eventDetails['time']; ?></p>
    <p><strong>Location:</strong> <?php echo $eventDetails['location']; ?></p>
    
    <div class="sop-container">
        <h2>Standard Operating Procedure (SOP)</h2>
        <p>This event is a thrilling and intellectually stimulating challenge that involves solving cryptographic puzzles and clues hidden in a series of digital and physical locations. Participants are expected to utilize their analytical, problem-solving, and code-breaking skills to decipher clues that lead to the next stage of the hunt.</p>
        <p><strong>Objective:</strong> The goal of this event is to find hidden messages or codes that unlock the next clue, ultimately leading to the final location or solution. Participants must solve a sequence of puzzles in the shortest time possible to win the challenge.</p>
        <p><strong>Rules and Guidelines:</strong>
            <ul>
                <li>Teams can consist of up to 1 members.</li>
                <li>All participants must bring their own devices (laptops or smartphones) with internet connectivity.</li>
                <li>Use of external help or collaboration with other teams is strictly prohibited.</li>
                <li>The event is time-bound, and the team with the fastest completion time wins.</li>
            </ul>
        </p>
        <p><strong>Judging Criteria:</strong>
            <ul>
                <li>Accuracy of answers.</li>
                <li>Time taken to solve all clues and reach the final solution.</li>
                <li>Penalties will be given for wrong answers or attempts.</li>
            </ul>
        </p>
    </div>
    
    <h2>Coordinators</h2>
    <p><strong>Student Coordinator:</strong> <?php echo $eventDetails['student_name']; ?> (Contact: <?php echo $eventDetails['student_contact']; ?>)</p>
    <p><strong>Staff Coordinator:</strong> <?php echo $eventDetails['staff_name']; ?> (Contact: <?php echo $eventDetails['staff_contact']; ?>)</p>
    
    <h2>Participant Counts</h2>
    <p><strong>Internal Participants:</strong> <?php echo $eventDetails['internal_count']; ?></p>
    <p><strong>External Participants:</strong> <?php echo $eventDetails['external_count']; ?></p>
    <p><strong>Total Participants:</strong> <?php echo $eventDetails['internal_count'] + $eventDetails['external_count']; ?></p>
    
    <h2>Internal Participants</h2>
    <table class="participant-table">
        <thead>
            <tr>
                <th>USN</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>College Name</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($internalParticipants) > 0): ?>
                <?php foreach ($internalParticipants as $participant): ?>
                    <tr>
                        <td><?php echo $participant['usn']; ?></td>
                        <td><?php echo $participant['name']; ?></td>
                        <td><?php echo $participant['phone']; ?></td>
                        <td><?php echo $participant['email']; ?></td>
                        <td><?php echo $participant['college']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No internal participants registered for this event.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Internal Participants Download Button -->
    <a class="download-button" href="download_excel.php?event_id=<?php echo $event_id; ?>&type=Internal">Download Internal Participants as Excel</a>

    <h2>External Participants</h2>
    <table class="participant-table">
        <thead>
            <tr>
                <th>USN</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>College Name</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($externalParticipants) > 0): ?>
                <?php foreach ($externalParticipants as $participant): ?>
                    <tr>
                        <td><?php echo $participant['usn']; ?></td>
                        <td><?php echo $participant['name']; ?></td>
                        <td><?php echo $participant['phone']; ?></td>
                        <td><?php echo $participant['email']; ?></td>
                        <td><?php echo $participant['college']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No external participants registered for this event.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- External Participants Download Button -->
    <a class="download-button" href="download_excel.php?event_id=<?php echo $event_id; ?>&type=External">Download External Participants as Excel</a>

    <a class="back-button" href="index.php">Go Back</a>
</body>
</html>
