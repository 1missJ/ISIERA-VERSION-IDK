<?php
require 'db_connection.php';

$student_id = $_POST['student_id'] ?? '';
$approved = $_POST['approved'] == '1' ? 1 : 0;

$sql = "UPDATE students SET mobile_verified = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $approved, $student_id);
$stmt->execute();

echo json_encode(['success' => true]);
