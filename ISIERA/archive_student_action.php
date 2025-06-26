<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');
error_reporting(E_ALL);

include('db_connection.php');

header('Content-Type: application/json');

if (!isset($_POST['lrn'])) {
    echo json_encode(['success' => false, 'error' => 'LRN not provided']);
    exit;
}

$lrn = mysqli_real_escape_string($conn, $_POST['lrn']);

mysqli_begin_transaction($conn);

try {
    $selectSql = "SELECT * FROM students WHERE lrn = '$lrn'";
    $result = mysqli_query($conn, $selectSql);

    if (!$result) {
        throw new Exception("DB Error: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) == 0) {
        throw new Exception("Student not found");
    }

    $student = mysqli_fetch_assoc($result);

    // Optional: log student for debugging
    // file_put_contents('debug_log.txt', print_r($student, true), FILE_APPEND);

    $insertSql = "INSERT INTO archived_students (
        rfid, lrn, first_name, middle_name, last_name, email,
        section, student_type, gender, date_of_birth,
        contact_number, address, citizenship,
        elementary_school, year_graduated,
        guardian_name, guardian_contact, guardian_address, guardian_relationship,
        birth_certificate, id_photo, good_moral, student_signature,
        grade_level, school_year
    ) VALUES (
        '" . mysqli_real_escape_string($conn, $student['rfid']) . "',
        '" . mysqli_real_escape_string($conn, $student['lrn']) . "',
        '" . mysqli_real_escape_string($conn, $student['first_name']) . "',
        '" . mysqli_real_escape_string($conn, $student['middle_name']) . "',
        '" . mysqli_real_escape_string($conn, $student['last_name']) . "',
        '" . mysqli_real_escape_string($conn, $student['email']) . "',
        '" . mysqli_real_escape_string($conn, $student['section']) . "',
        '" . mysqli_real_escape_string($conn, $student['student_type']) . "',
        '" . mysqli_real_escape_string($conn, $student['gender']) . "',
        '" . mysqli_real_escape_string($conn, $student['date_of_birth']) . "',
        '" . mysqli_real_escape_string($conn, $student['contact_number']) . "',
        '" . mysqli_real_escape_string($conn, $student['address']) . "',
        '" . mysqli_real_escape_string($conn, $student['citizenship']) . "',
        '" . mysqli_real_escape_string($conn, $student['elementary_school']) . "',
        '" . mysqli_real_escape_string($conn, $student['year_graduated']) . "',
        '" . mysqli_real_escape_string($conn, $student['guardian_name']) . "',
        '" . mysqli_real_escape_string($conn, $student['guardian_contact']) . "',
        '" . mysqli_real_escape_string($conn, $student['guardian_address']) . "',
        '" . mysqli_real_escape_string($conn, $student['guardian_relationship']) . "',
        '" . mysqli_real_escape_string($conn, $student['birth_certificate']) . "',
        '" . mysqli_real_escape_string($conn, $student['id_photo']) . "',
        '" . mysqli_real_escape_string($conn, $student['good_moral']) . "',
        '" . mysqli_real_escape_string($conn, $student['student_signature']) . "',
        '" . mysqli_real_escape_string($conn, $student['grade_level']) . "',
        '" . mysqli_real_escape_string($conn, $student['school_year']) . "'
    )";

    if (!mysqli_query($conn, $insertSql)) {
        throw new Exception("Failed to insert into archive: " . mysqli_error($conn));
    }

    $deleteSql = "DELETE FROM students WHERE lrn = '$lrn'";
    if (!mysqli_query($conn, $deleteSql)) {
        throw new Exception("Failed to delete student: " . mysqli_error($conn));
    }

    mysqli_commit($conn);
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    $content = ob_get_clean(); // get any output that happened before

if (!empty($content)) {
    // Output buffered content as error
    echo json_encode(['success' => false, 'error' => 'Unexpected output: ' . $content]);
    exit;
}

    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
