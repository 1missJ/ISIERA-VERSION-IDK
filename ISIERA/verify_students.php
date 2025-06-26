<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "tapintime");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit;
}

$studentId = $_POST['student_id'] ?? '';
$isApproved = $_POST['approved'] ?? '';

if (empty($studentId) || !in_array($isApproved, ['0', '1'])) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

// Fetch original student
$getStudent = $conn->prepare("SELECT * FROM students WHERE id = ?");
$getStudent->bind_param("s", $studentId);
$getStudent->execute();
$studentResult = $getStudent->get_result();

if ($studentResult->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Student not found"]);
    exit;
}

$student = $studentResult->fetch_assoc();

// Choose table
$table = $isApproved == '1' ? 'approved_students_mobile' : 'rejected_students';

// Insert a copy
$insert = $conn->prepare("INSERT INTO $table (id, name, section, grade_level, student_type) VALUES (?, ?, ?, ?, ?)");
$insert->bind_param(
    "sssss",
    $student['id'],
    $student['name'],
    $student['section'],
    $student['grade_level'],
    $student['student_type']
);

if ($insert->execute()) {
    echo json_encode(["success" => true, "message" => $isApproved == '1' ? "Student approved" : "Student rejected"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to save decision"]);
}

$conn->close();
?>
