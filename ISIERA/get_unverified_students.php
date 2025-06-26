<?php
header('Content-Type: application/json');
require 'db_connection.php';

$raw = file_get_contents("php://input");
$input = json_decode($raw, true);

$teacherCode = $input['teacher_id'] ?? null;

if (!$teacherCode) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing teacher_id from mobile (varchar)',
    ]);
    exit;
}

// 🔍 STEP 1: Get internal faculty.id using teacher_id (varchar)
$facultyQuery = $conn->prepare("SELECT id FROM faculty WHERE teacher_id = ?");
$facultyQuery->bind_param("s", $teacherCode);
$facultyQuery->execute();
$facultyResult = $facultyQuery->get_result();

if ($facultyResult->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => "No faculty found with teacher_id $teacherCode"
    ]);
    exit;
}

$faculty = $facultyResult->fetch_assoc();
$teacherInternalId = $faculty['id']; // ← this is INT that maps to assign.teacher_id

// 🔄 STEP 2: Fetch assigned students based on internal ID
$assignQuery = $conn->prepare("
    SELECT section, grade_level, student_type 
    FROM assign 
    WHERE teacher_id = ?
");
$assignQuery->bind_param("i", $teacherInternalId);
$assignQuery->execute();
$assignResult = $assignQuery->get_result();

$students = [];

while ($row = $assignResult->fetch_assoc()) {
    $section = $row['section'];
    $grade = "Grade " . strval($row['grade_level']);
    $type = trim($row['student_type']);

    $studentQuery = $conn->prepare("
    SELECT id, CONCAT(last_name, ', ', first_name) AS name, section, grade_level, student_type 
    FROM students 
    WHERE section = ? AND grade_level = ? AND student_type = ? 
    AND mobile_verified = 0 
    AND id NOT IN (SELECT student_id FROM approved_students_mobile)
");

    $studentQuery->bind_param("sss", $section, $grade, $type);
    $studentQuery->execute();
    $studentResult = $studentQuery->get_result();

    while ($student = $studentResult->fetch_assoc()) {
        $students[] = $student;
    }
}

echo json_encode(['success' => true, 'students' => $students]);
?>
