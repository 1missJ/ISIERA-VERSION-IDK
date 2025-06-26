<?php
include('db_connection.php');

$section = $_POST['section'] ?? '';
$type = $_POST['type'] ?? '';

if (!$section || !$type) {
    echo json_encode(['success' => false, 'error' => 'Missing section or type.']);
    exit;
}

try {
    $conn->begin_transaction();

    $fetchQuery = "SELECT * FROM archived_students WHERE section = ? AND student_type = ?";
    $stmt = $conn->prepare($fetchQuery);
    $stmt->bind_param('ss', $section, $type);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = $result->fetch_all(MYSQLI_ASSOC);

    if (empty($students)) {
        echo json_encode(['success' => false, 'error' => 'No students found to unarchive.']);
        exit;
    }

    // Update your insert query to include all columns you want to insert
    $insertQuery = "INSERT INTO students (
        lrn, first_name, middle_name, last_name, email, section, school_year, grade_level, student_type,
        date_of_birth, gender, citizenship, address, contact_number, guardian_name, guardian_contact,
        guardian_relationship, guardian_address, elementary_school, year_graduated, birth_certificate,
        id_photo, good_moral, student_signature
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $deleteQuery = "DELETE FROM archived_students WHERE lrn = ?";

    $insertStmt = $conn->prepare($insertQuery);
    $deleteStmt = $conn->prepare($deleteQuery);

    foreach ($students as $student) {
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

        $deleteStmt->bind_param('s', $student['lrn']);
        $deleteStmt->execute();
    }

    $conn->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
