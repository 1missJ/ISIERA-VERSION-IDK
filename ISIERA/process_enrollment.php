<?php
include('db_connection.php');
session_start();

// Check if teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

// Get JSON data from the request
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data || !isset($data['lrn']) || !isset($data['subjects']) || !isset($data['section_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request data']);
    exit();
}

$lrn = $data['lrn'];
$subjectIds = $data['subjects'];
$sectionId = $data['section_id'];
$teacherId = $_SESSION['teacher_id'];

try {
    // Begin transaction
    $conn->begin_transaction();

    // First, remove existing enrollments for this student in this section
    $deleteStmt = $conn->prepare("
        DELETE FROM student_enrollments 
        WHERE student_lrn = ? 
        AND section_id = ?
    ");
    $deleteStmt->bind_param("si", $lrn, $sectionId);
    $deleteStmt->execute();
    
    if ($deleteStmt->affected_rows === -1) {
        throw new Exception("Error deleting existing enrollments");
    }
    $deleteStmt->close();

    // Prepare statement for inserting new enrollments (without teacher_id)
    $insertStmt = $conn->prepare("
        INSERT INTO student_enrollments 
        (student_lrn, subject_id, section_id) 
        VALUES (?, ?, ?)
    ");

    foreach ($subjectIds as $subjectId) {
        // Insert the enrollment record without teacher_id
        $insertStmt->bind_param("sii", $lrn, $subjectId, $sectionId);
        if (!$insertStmt->execute()) {
            throw new Exception("Error enrolling student in subject: " . $subjectId);
        }
    }

    $insertStmt->close();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode(['status' => 'success', 'message' => 'Enrollment updated successfully']);
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
?>