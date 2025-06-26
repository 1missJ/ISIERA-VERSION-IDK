<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_grade_raw = trim($_POST['current_grade'] ?? '');
    $current_section = trim($_POST['current_section'] ?? '');
    $new_section = trim($_POST['new_section'] ?? '');
    $redirect_url = $_POST['redirect_url'] ?? 'student_promotion.php';

    // Extract numeric grade level
    preg_match('/\d+/', $current_grade_raw, $matches);
    $current_grade = $matches[0] ?? null;

    if (!$current_grade) {
        die("Invalid current grade.");
    }

    // Sanitize inputs
    $current_section_safe = mysqli_real_escape_string($conn, $current_section);
    $new_section_safe = mysqli_real_escape_string($conn, $new_section);
    $current_grade_raw_safe = mysqli_real_escape_string($conn, $current_grade_raw);

    if ((int)$current_grade === 12) {
        // Archive Grade 12 students
        $query = "SELECT * FROM students WHERE grade_level = '$current_grade_raw_safe' AND section = '$current_section_safe'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($student = mysqli_fetch_assoc($result)) {
                $insert = "INSERT INTO archived_students (
                    lrn, first_name, middle_name, last_name, email, section, school_year, grade_level,
                    student_type, date_of_birth, gender, citizenship, address, contact_number,
                    guardian_name, guardian_contact, guardian_relationship, guardian_address,
                    elementary_school, year_graduated, birth_certificate, id_photo, good_moral, student_signature
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt = $conn->prepare($insert);

                // Overwrite grade_level for archive
                $student['grade_level'] = 'SHS Graduate';

                $stmt->bind_param(
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

                if ($stmt->execute()) {
                    $lrn_safe = mysqli_real_escape_string($conn, $student['lrn']);
                    mysqli_query($conn, "DELETE FROM students WHERE lrn = '$lrn_safe'");
                }

                $stmt->close();
            }
        }

        mysqli_free_result($result);
        mysqli_close($conn);
        header("Location: $redirect_url");
        exit;

    } else {
        // Promote to next grade
        $next_grade = "Grade " . ($current_grade + 1);
        $next_grade_safe = mysqli_real_escape_string($conn, $next_grade);

        $sql = "UPDATE students 
                SET grade_level = '$next_grade_safe', section = '$new_section_safe'
                WHERE grade_level = '$current_grade_raw_safe' AND section = '$current_section_safe'";

        if (mysqli_query($conn, $sql)) {
            header("Location: $redirect_url");
            exit;
        } else {
            echo "Promotion failed: " . mysqli_error($conn);
        }
    }
} else {
    echo "Invalid request.";
}
?>
