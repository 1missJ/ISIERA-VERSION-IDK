<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);
$teacherId = $input['teacher_id'] ?? null;

// Log to PHP error log
error_log("🧪 Test Log - Received teacher_id: " . var_export($teacherId, true));

// Return for visual confirmation
echo json_encode([
    'success' => true,
    'received_teacher_id' => $teacherId
]);
?>
