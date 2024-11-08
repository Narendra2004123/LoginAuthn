<?php
include_once 'classes/db1.php';
$result = mysqli_query($conn, "SELECT * FROM sponsors ORDER BY company_name");
?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Stepcone 2K25</title>
    <?php require 'utils/styles.php'; ?><!--css links. file found in utils folder-->
</head>

<body>
    <?php include 'utils/adminHeader.php' ?>
    
    <div class="content">
        <div class="container">
            <h1>Sponsor List</h1>
            <?php
            if (mysqli_num_rows($result) > 0) {
            ?>
            <table class="table table-hover">
                <tr>
                    <th>Company Name</th>
                    <th>Sponsor Name</th>
                    <th>Referred By</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
                <?php
                while ($row = mysqli_fetch_array($result)) {
                ?>
                <tr>
                    <td><?php echo $row["company_name"]; ?></td>
                    <td><?php echo $row["sponsor_name"]; ?></td>
                    <td><?php echo $row["referred_by"]; ?></td>
                    <td><?php echo $row["amount"]; ?></td>
                    <td><?php echo $row["date"]; ?></td>
                </tr>
                <?php
                }
                ?>
            </table>
            <?php
            } else {
                echo "<p>No sponsors found.</p>";
            }
            ?>
        </div>
    </div>
    <center><a href="download.php" class="btn btn-primary" style="width:250px">Download Excel</a></center>
    <?php include 'utils/footer.php'; ?>
</body>
</html>
