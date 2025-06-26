<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newSection = $_POST['new_section'] ?? '';
    $currentGrade = $_POST['current_grade'] ?? '';
    $currentSection = $_POST['current_section'] ?? '';
    $studentType = $_POST['student_type'] ?? '';
    $redirectUrl = $_POST['redirect_url'] ?? 'student_promotion.php';

    // Sanitize inputs
    $newSection = trim($newSection);
    $currentGrade = trim($currentGrade);
    $currentSection = trim($currentSection);
    $studentType = trim($studentType);

    if ($newSection && $currentGrade && $currentSection && $studentType) {
        // Promote: increase grade by 1
        $gradeParts = explode(' ', $currentGrade);
        $nextGradeNumber = intval($gradeParts[1]) + 1;
        $nextGrade = 'Grade ' . $nextGradeNumber;

        $stmt = $conn->prepare("UPDATE students SET grade_level = ?, section = ?, promotion_status = 'pending' WHERE grade_level = ? AND section = ? AND student_type = ?");
        $stmt->bind_param("sssss", $nextGrade, $newSection, $currentGrade, $currentSection, $studentType);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: $redirectUrl");
    exit;
}
?>

