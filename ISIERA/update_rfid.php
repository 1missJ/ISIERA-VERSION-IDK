<?php
include('db_connection.php');

$lrn = $_POST['lrn'] ?? '';
$rfid = $_POST['rfid'] ?? '';

// Validate RFID is 10 to 12 digits
if (!preg_match('/^\d{10,12}$/', $rfid)) {
    echo "invalid";
    exit;
}

$lrn_safe = mysqli_real_escape_string($conn, $lrn);
$rfid_safe = mysqli_real_escape_string($conn, $rfid);

$sql = "UPDATE students SET rfid = '$rfid_safe' WHERE lrn = '$lrn_safe'";
if (mysqli_query($conn, $sql)) {
    echo "success";
} else {
    echo "error";
}
?>
