<?php
header('Content-Type: application/json');
require 'db_connection.php';

$input = json_decode(file_get_contents("php://input"), true);
$teacherId = $input['teacher_id'] ?? '';
$subjectId = $input['subject'] ?? '';
$section = $input['section'] ?? '';

$stmt = $conn->prepare("
    SELECT s.student_id AS id, s.name, a.section, a.subject_id
    FROM approved_students_mobile s
    JOIN assign a ON s.section = a.section
    WHERE a.teacher_id = ? AND a.subject_id = ? AND a.section = ?
");
$stmt->bind_param("sss", $teacherId, $subjectId, $section);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode(['success' => true, 'students' => $students]);
