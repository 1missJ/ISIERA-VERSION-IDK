<?php
include('db_connection.php');

// Get filters and selected LRNs
$lrns = $_POST['selected_lrns'] ?? [];
$section = $_POST['section'] ?? '';
$grade_level = $_POST['grade_level'] ?? '';
$student_type = $_POST['student_type'] ?? '';

// Build redirect URL back to student_archive.php
$redirectUrl = "student_archive.php?section=" . urlencode($section) .
               "&grade_level=" . urlencode($grade_level) .
               "&student_type=" . urlencode($student_type);

// If no students selected, redirect back
if (empty($lrns)) {
    header("Location: $redirectUrl");
    exit;
}

try {
    $conn->begin_transaction();

    $fetchQuery = "SELECT * FROM archived_students WHERE lrn = ?";
    $insertQuery = "INSERT INTO students (
        lrn, first_name, middle_name, last_name, email, section, school_year, grade_level, student_type,
        date_of_birth, gender, citizenship, address, contact_number, guardian_name, guardian_contact,
        guardian_relationship, guardian_address, elementary_school, year_graduated, birth_certificate,
        id_photo, good_moral, student_signature
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $deleteQuery = "DELETE FROM archived_students WHERE lrn = ?";

    $fetchStmt = $conn->prepare($fetchQuery);
    $insertStmt = $conn->prepare($insertQuery);
    $deleteStmt = $conn->prepare($deleteQuery);

    foreach ($lrns as $lrn) {
        $fetchStmt->bind_param('s', $lrn);
        $fetchStmt->execute();
        $result = $fetchStmt->get_result();
        $student = $result->fetch_assoc();

        if (!$student) continue;

        $insertStmt->bind_param(
            'ssssssssssssssssssssssss',
            $student['lrn'],
            $student['first_name'],
            $student['middle_name'],
            $student['last_name'],
            $student['email'],
            $student['section'],
            $student['school_year'],
            $student['grade_level'],
            $student['student_type'],
            $student['date_of_birth'],
            $student['gender'],
            $student['citizenship'],
            $student['address'],
            $student['contact_number'],
            $student['guardian_name'],
            $student['guardian_contact'],
            $student['guardian_relationship'],
            $student['guardian_address'],
            $student['elementary_school'],
            $student['year_graduated'],
            $student['birth_certificate'],
            $student['id_photo'],
            $student['good_moral'],
            $student['student_signature']
        );
        $insertStmt->execute();

        $deleteStmt->bind_param('s', $lrn);
        $deleteStmt->execute();
    }

    $conn->commit();

    // Redirect silently back to archive page with same filters
    header("Location: $redirectUrl");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    header("Location: $redirectUrl");
    exit;
}
?>