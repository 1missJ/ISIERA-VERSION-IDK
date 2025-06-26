<?php
header('Content-Type: application/json');
require 'db_connection.php';

$input = json_decode(file_get_contents("php://input"), true);
$teacherId = $input['teacher_id'] ?? '';
$subjectId = $input['subject'] ?? '';

$stmt = $conn->prepare("SELECT DISTINCT section FROM assign WHERE teacher_id = ? AND subject_id = ?");
$stmt->bind_param("ss", $teacherId, $subjectId);
$stmt->execute();
$result = $stmt->get_result();

$sections = [];
while ($row = $result->fetch_assoc()) {
    $sections[] = $row['section'];
}

echo json_encode(['success' => true, 'sections' => $sections]);
