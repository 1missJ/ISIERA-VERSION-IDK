<?php  
include 'db_connection.php';

if (isset($_GET['id'])) {
    $studentId = $_GET['id'];

    // Get student data from pending_students
    $query = "SELECT * FROM pending_students WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    if ($student) {
        // Validate required fields
        if (
            empty($student['section']) || 
            empty($student['school_year']) || 
            empty($student['guardian_address']) || 
            empty($student['grade_level'])
        ) {
            echo "<script>alert('Error: Some required fields are missing for this student.'); window.location.href='student_verification.php';</script>";
            exit();
        }

        // Determine student_type
        $gradeLevel = strtoupper(trim($student['grade_level']));
        $studentType = '';
        if (in_array($gradeLevel, ['GRADE 7', 'GRADE 8', 'GRADE 9', 'GRADE 10'])) {
            $studentType = 'JHS';
        } elseif (in_array($gradeLevel, ['GRADE 11', 'GRADE 12'])) {
            $studentType = 'SHS';
        }

        // 1. Insert school_year into school_years table (if not already exists)
        $schoolYear = trim($student['school_year']);

        $checkSY = $conn->prepare("SELECT id FROM school_years WHERE year = ?");
        $checkSY->bind_param("s", $schoolYear);
        $checkSY->execute();
        $checkSY->store_result();

        if ($checkSY->num_rows === 0) {
            $insertSY = $conn->prepare("INSERT INTO school_years (year) VALUES (?)");
            $insertSY->bind_param("s", $schoolYear);
            $insertSY->execute();
        }

        // 2. Insert student into students table
        $insertQuery = "INSERT INTO students (
            lrn, first_name, middle_name, last_name, email, section, school_year, grade_level, student_type,
            date_of_birth, gender, citizenship, address, contact_number,
            guardian_name, guardian_contact, guardian_relationship, guardian_address,
            elementary_school, year_graduated, birth_certificate, id_photo, good_moral, student_signature
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ssssssssssssssssssssssss", 
            $student['lrn'], 
            $student['first_name'], 
            $student['middle_name'], 
            $student['last_name'],
            $student['email'], 
            $student['section'],
            $schoolYear,
            $student['grade_level'],
            $studentType,
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

        if ($insertStmt->execute()) {
            // Delete student from pending_students
            $deleteQuery = "DELETE FROM pending_students WHERE id = ?";
            $deleteStmt = $conn->prepare($deleteQuery);
            $deleteStmt->bind_param("i", $studentId);
            $deleteStmt->execute();

            echo "<script>alert('Student approved successfully!'); window.location.href='student_verification.php';</script>";
        } else {
            echo "<script>alert('Error inserting into students: " . $insertStmt->error . "'); window.location.href='student_verification.php';</script>";
        }

    } else {
        echo "<script>alert('Student not found.'); window.location.href='student_verification.php';</script>";
    }
}
?>
