<?php
header('Content-Type: application/json');
require 'db_connection.php';

$input = json_decode(file_get_contents("php://input"), true);
$teacherId = $input['teacher_id'] ?? '';

if (empty($teacherId)) {
    echo json_encode(['success' => false, 'message' => 'Missing teacher ID']);
    exit;
}

$stmt = $conn->prepare("
    SELECT DISTINCT s.subject_name 
    FROM assign a
    JOIN subjects s ON a.subject_id = s.id
    WHERE a.teacher_id = ?
");
$stmt->bind_param("s", $teacherId);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row['subject_name'];
}

echo json_encode([
    'success' => true,
    'subjects' => $subjects
]);
?>
