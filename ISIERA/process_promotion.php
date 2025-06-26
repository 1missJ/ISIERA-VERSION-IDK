<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_students = $_POST['selected_students'] ?? [];
    $current_grade = trim($_POST['current_grade'] ?? '');
    $current_section = trim($_POST['current_section'] ?? '');
    $new_section = trim($_POST['new_section'] ?? '');

    if (empty($selected_students) || empty($current_grade) || empty($current_section)) {
        die("Missing required data.");
    }

    // Extract grade number
    preg_match('/\d+/', $current_grade, $matches);
    $current_grade_num = $matches[0] ?? null;

    if (!$current_grade_num) {
        die("Invalid current grade level.");
    }

    $is_grade_12 = $current_grade_num == 12;

    $new_section_safe = mysqli_real_escape_string($conn, $new_section);
    $current_section_safe = mysqli_real_escape_string($conn, $current_section);
    $current_grade_safe = mysqli_real_escape_string($conn, $current_grade);
    $errors = 0;

    if ($is_grade_12) {
        // Archive Grade 12 students as SHS Graduates with all fields
        foreach ($selected_students as $lrn) {
            $lrn_safe = mysqli_real_escape_string($conn, trim($lrn));

            // Get student details
            $query = "SELECT * FROM students WHERE lrn = '$lrn_safe' AND grade_level = '$current_grade_safe' AND section = '$current_section_safe'";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                $student = mysqli_fetch_assoc($result);

                $insert = "INSERT INTO archived_students (
                    lrn, first_name, middle_name, last_name, email, section, school_year, grade_level,
                    student_type, date_of_birth, gender, citizenship, address, contact_number,
                    guardian_name, guardian_contact, guardian_relationship, guardian_address,
                    elementary_school, year_graduated, birth_certificate, id_photo, good_moral, student_signature
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt_insert = $conn->prepare($insert);
                $student['grade_level'] = 'SHS Graduate';

                $stmt_insert->bind_param(
                    'ssssssssssssssssssssssss',
                    $student['lrn'],
                    $student['first_name'],
                    $student['middle_name'],
                    $student['last_name'],
                    $student['email'],
                    $new_section_safe,
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

                if ($stmt_insert->execute()) {
                    mysqli_query($conn, "DELETE FROM students WHERE lrn = '$lrn_safe'");
                } else {
                    $errors++;
                }

                $stmt_insert->close();
            } else {
                $errors++;
            }
        }
    } else {
        // Promote to next grade level
        $next_grade = 'Grade ' . ($current_grade_num + 1);
        $next_grade_safe = mysqli_real_escape_string($conn, $next_grade);

        $stmt = $conn->prepare("UPDATE students SET grade_level = ?, section = ? WHERE lrn = ? AND grade_level = ? AND section = ?");

        foreach ($selected_students as $lrn) {
            $lrn_safe = trim($lrn);
            $stmt->bind_param("sssss", $next_grade_safe, $new_section_safe, $lrn_safe, $current_grade_safe, $current_section_safe);
            if (!$stmt->execute()) {
                $errors++;
            }
        }

        $stmt->close();
    }

    $conn->close();

    if ($errors === 0) {
        header("Location: student_promotion.php?grade=" . urlencode($current_grade));
        exit;
    } else {
        echo "$errors promotion(s) failed. Please try again.";
    }
} else {
    echo "Invalid request.";
}
?>