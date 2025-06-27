<?php
include 'db_connection.php';

if (isset($_GET['id'])) {
    $studentId = $_GET['id'];

    $deleteQuery = "DELETE FROM pending_students WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $studentId);

    if ($deleteStmt->execute()) {
        echo "<script>alert('Student rejected successfully!'); window.location.href='adviser_student_verification.php';</script>";
    } else {
        echo "<script>alert('Error rejecting student.'); window.location.href='adviser_student_verification.php';</script>";
    }
}
?>
