<?php
include('db_connection.php');
session_start();

if (!isset($_SESSION['teacher_id'])) {
    header("HTTP/1.1 401 Unauthorized");
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$sectionId = $data['section_id'] ?? null;
$subjectIds = $data['subject_ids'] ?? [];
$teacherId = $_SESSION['teacher_id'];
$teacherName = $_SESSION['teacher_name'];

if (!$sectionId || empty($subjectIds)) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

try {
    // Get section name
    $sectionStmt = $conn->prepare("SELECT section_name FROM sections WHERE id = ?");
    $sectionStmt->bind_param("i", $sectionId);
    $sectionStmt->execute();
    $section = $sectionStmt->get_result()->fetch_assoc();
    
    // Get all students in the section
    $studentStmt = $conn->prepare("
        SELECT lrn, CONCAT(first_name, ' ', last_name) as student_name, 
               rfid, grade_level 
        FROM students 
        WHERE section = ?
    ");
    $studentStmt->bind_param("i", $sectionId);
    $studentStmt->execute();
    $students = $studentStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    if (empty($students)) {
        echo json_encode(['success' => false, 'message' => 'No students found in this section']);
        exit();
    }
    
    // Begin transaction
    $conn->begin_transaction();
    
    // Prepare statements
    $deleteStmt = $conn->prepare("DELETE FROM enrolled_students WHERE student_lrn = ?");
    $insertStmt = $conn->prepare("
        INSERT INTO enrolled_students (
            student_lrn, student_name, rfid,
            section_id, section_name,
            subject_id, subject_name,
            teacher_id, teacher_name,
            grade_level
        )
        SELECT 
            ?, ?, ?,
            ?, ?,
            s.id, s.subject_name,
            ts.teacher_id, f.name,
            ?
        FROM subjects s
        JOIN teacher_subjects ts ON ts.subject_id = s.id AND ts.section_id = ?
        JOIN faculty f ON f.teacher_id = ts.teacher_id
        WHERE s.id = ?
    ");
    
    foreach ($students as $student) {
        // First delete all existing enrollments for this student
        $deleteStmt->bind_param("s", $student['lrn']);
        $deleteStmt->execute();
        
        foreach ($subjectIds as $subjectId) {
            $insertStmt->bind_param(
                "ssssisssi", 
                $student['lrn'],
                $student['student_name'],
                $student['rfid'],
                $sectionId,
                $section['section_name'],
                $student['grade_level'],
                $sectionId,
                $subjectId
            );
            $insertStmt->execute();
        }
    }
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Bulk enrollment completed for ' . count($students) . ' students',
        'students_count' => count($students),
        'subjects_count' => count($subjectIds)
    ]);
} catch (Exception $e) {
    $conn->rollback();
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>