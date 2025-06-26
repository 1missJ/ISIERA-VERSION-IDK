<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lrn = mysqli_real_escape_string($conn, $_POST['lrn']);
    $rfid = mysqli_real_escape_string($conn, $_POST['rfid']);

    // Check if RFID is already used
    $check = "SELECT * FROM students WHERE rfid = '$rfid'";
    $res = mysqli_query($conn, $check);

    if (mysqli_num_rows($res) > 0) {
        echo "RFID already assigned to another student.";
    } else {
        // Update student's RFID
        $sql = "UPDATE students SET rfid = '$rfid' WHERE lrn = '$lrn'";
        if (mysqli_query($conn, $sql)) {
            echo "RFID successfully assigned!";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>
