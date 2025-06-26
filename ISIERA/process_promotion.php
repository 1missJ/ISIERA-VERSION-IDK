<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $current_grade = trim($_POST['current_grade']);
    $current_section = trim($_POST['current_section']);
    $student_type = trim($_POST['student_type']);
    $new_section = trim($_POST['new_section']);
    $selected_students = $_POST['selected_students'] ?? [];

    if (empty($selected_students)) {
        die("No students selected.");
    }

    // Grade level mapping
    $grade_mapping = [
        'Grade 7' => 'Grade 8',
        'Grade 8' => 'Grade 9',
        'Grade 9' => 'Grade 10',
        'Grade 10' => 'Graduated'
    ];

    if (!isset($grade_mapping[$current_grade])) {
        die("Invalid current grade.");
    }

    $next_grade = $grade_mapping[$current_grade];

    // Prepare placeholders for selected_students for SQL IN clause
    $placeholders = implode(',', array_fill(0, count($selected_students), '?'));

    // Build SQL to update only selected students
    $sql = "UPDATE students 
            SET grade_level = ?, section = ? 
            WHERE grade_level = ? AND section = ? AND student_type = ? 
              AND lrn IN ($placeholders)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters dynamically
    $params = array_merge(
        [$next_grade, $new_section, $current_grade, $current_section, $student_type],
        $selected_students
    );

    $types = str_repeat('s', count($params));
    $bind_params = array_merge([$types], $params);

    // Use references for call_user_func_array
    $tmp = [];
    foreach ($bind_params as $key => $value) {
        $tmp[$key] = &$bind_params[$key];
    }

    call_user_func_array([$stmt, 'bind_param'], $tmp);

    // Execute and close
    $stmt->execute();
    $stmt->close();

    // ✅ Redirect to updated page with success message
header("Location: promote_students.php?promoted=1&grade_level=" . urlencode($next_grade) . "&section=" . urlencode($new_section) . "&student_type=" . urlencode($student_type));
    exit();
}
?>
