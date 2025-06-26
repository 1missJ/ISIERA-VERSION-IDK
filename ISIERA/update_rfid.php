<?php
include 'db_connection.php';

$lrn = $_POST['lrn'] ?? '';
$rfid = $_POST['rfid'] ?? '';

if (!empty($lrn) && !empty($rfid)) {
    $stmt = $conn->prepare("UPDATE students SET rfid = ? WHERE lrn = ?");
    $stmt->bind_param("ss", $rfid, $lrn);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
} else {
    echo "invalid";
}

$conn->close();
?>
