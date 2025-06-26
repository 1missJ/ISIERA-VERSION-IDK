<?php
session_start();
include 'db_connection.php';

$error = "";
$success = "";

$target_dir = "uploads/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $lrn = trim($_POST['lrn']);
    $date_of_birth = trim($_POST['date_of_birth']);
    $gender = trim($_POST['gender']);
    $citizenship = trim($_POST['citizenship']);
    $address = trim($_POST['address']);
    $contact_number = trim($_POST['contact_number']);
    $email = trim($_POST['email']);
    $section = trim($_POST['section']);
    $school_year = trim($_POST['school_year']);
    $grade_level = trim($_POST['grade_level']);
    $guardian_name = trim($_POST['guardian_name']);
    $guardian_contact = trim($_POST['guardian_contact']);
    $guardian_address = trim($_POST['guardian_address']);
    $guardian_relationship = trim($_POST['guardian_relationship']);
    $elementary_school = trim($_POST['elementary_school']);
    $year_graduated = trim($_POST['year_graduated']);
    $created_at = date('Y-m-d H:i:s');

    if (in_array($grade_level, ["Grade 7", "Grade 8", "Grade 9", "Grade 10"])) {
    $student_type = "JHS";
} elseif (in_array($grade_level, ["Grade 11", "Grade 12"])) {
    $student_type = "SHS";
}

    if (!preg_match('/^\d{4}-\d{4}$/', $school_year)) {
        $error = "Invalid school year format. Please use 'YYYY-YYYY'.";
    } else {
        list($start_year, $end_year) = explode("-", $school_year);
        $start_year = (int)$start_year;
        $end_year = (int)$end_year;

        $expected_gap = 0;
        if ($grade_level === "Grade 7") $expected_gap = 4;
        elseif ($grade_level === "Grade 8") $expected_gap = 3;
        elseif ($grade_level === "Grade 9") $expected_gap = 2;
        elseif ($grade_level === "Grade 10") $expected_gap = 1;
        elseif ($grade_level === "Grade 11") $expected_gap = 2;
        elseif ($grade_level === "Grade 12") $expected_gap = 1;

        if ($end_year != ($start_year + $expected_gap)) {
            $error = "Invalid school year range for $grade_level. It must be $expected_gap years after the start year.";
        }
    }

    if (empty($error)) {
        $check_stmt = $conn->prepare("SELECT lrn, email FROM pending_students WHERE lrn = ? OR email = ? UNION SELECT lrn, email FROM students WHERE lrn = ? OR email = ?");
        $check_stmt->bind_param("ssss", $lrn, $email, $lrn, $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $check_stmt->bind_result($existing_lrn, $existing_email);
            while ($check_stmt->fetch()) {
                if ($existing_lrn === $lrn) {
                    $error = "The LRN '$lrn' already exists in the system. Please use a different LRN.";
                }
                if ($existing_email === $email) {
                    $error = "The email '$email' is already registered. Please use a different email.";
                }
            }
        } else {
            $valid_extensions = ["jpg", "jpeg", "png"];
            $uploaded_files = [];

            foreach (["birth_certificate", "id_photo", "good_moral", "student_signature"] as $file_key) {
                if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] == 0) {
                    $file_ext = strtolower(pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION));
                    if (in_array($file_ext, $valid_extensions)) {
                        $new_file_name = uniqid() . "_" . $file_key . "." . $file_ext;
                        $target_file = $target_dir . $new_file_name;
                        if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $target_file)) {
                            $uploaded_files[$file_key] = $target_file;
                        } else {
                            $error = "Error uploading $file_key.";
                        }
                    } else {
                        $error = "Invalid file type for $file_key. Only JPG, JPEG, and PNG are allowed.";
                    }
                } else {
                    $uploaded_files[$file_key] = null;
                }
            }

            if (empty($error)) {
                $stmt = $conn->prepare("INSERT INTO pending_students 
                    (first_name, middle_name, last_name, lrn, date_of_birth, gender, citizenship, address, contact_number, email, section, school_year, student_type, guardian_name, guardian_contact, guardian_address, guardian_relationship, elementary_school, year_graduated, birth_certificate, id_photo, good_moral, student_signature, created_at, grade_level) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                if (!$stmt) {
                    die("SQL Prepare Error: " . $conn->error);
                }

                $stmt->bind_param("sssssssssssssssssssssssss",
                    $first_name, $middle_name, $last_name, $lrn, $date_of_birth, $gender,
                    $citizenship, $address, $contact_number, $email, $section, $school_year, $student_type,
                    $guardian_name, $guardian_contact, $guardian_address, $guardian_relationship, $elementary_school,
                    $year_graduated,
                    $uploaded_files['birth_certificate'], $uploaded_files['id_photo'],
                    $uploaded_files['good_moral'], $uploaded_files['student_signature'],
                    $created_at, $grade_level
                );

                if ($stmt->execute()) {
                    $success = "Registration successful!";
                } else {
                    $error = "Error: " . $stmt->error;
                }
                $stmt->close();
            }
        }
        $check_stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Student Registration</h2>
         <?php if ($success): ?>
            <div class="alert alert-success text-center"> <?php echo $success; ?> </div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger text-center"> <?php echo $error; ?> </div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            
            <h4>Personal Information</h4>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                </div>
                <div class="col-md-4 mb-3">
                    <input type="text" name="middle_name" class="form-control" placeholder="Middle Name">
                </div>
                <div class="col-md-4 mb-3">
                    <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                </div>

                <div class="col-md-4 mb-3">
                    <input type="text" name="lrn" class="form-control" placeholder="LRN (12 digits)" required>
                </div>
                <div class="col-md-4 mb-3">
                    <input type="date" name="date_of_birth" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <select name="gender" class="form-control" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <input type="text" name="citizenship" class="form-control" placeholder="Citizenship" required>
                </div>
                <div class="col-md-6 mb-3">
                    <input type="text" name="address" class="form-control" placeholder="Address" required>
                </div>

                <div class="col-md-6 mb-3">
                    <input type="text" name="contact_number" class="form-control" placeholder="Contact Number" required>
                </div>
                <div class="col-md-6 mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <input type="text" name="section" class="form-control" placeholder="Section" required>
                </div>

                <div class="col-md-4 mb-3">
                    <select name="grade_level" class="form-control" required>
                        <option value="">Grade Level</option>
                        <option value="Grade 7">Grade 7</option>
                        <option value="Grade 8">Grade 8</option>
                        <option value="Grade 9">Grade 9</option>
                        <option value="Grade 10">Grade 10</option>
                        <option value="Grade 11">Grade 11</option>
                        <option value="Grade 12">Grade 12</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <input type="text" name="school_year" id="school_year" class="form-control" placeholder="School Year" required oninput="updateSchoolYear()">
                </div>
            </div>

            <h4>Parent/Guardian Information</h4>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <input type="text" name="guardian_name" class="form-control" placeholder="Full Name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <input type="text" name="guardian_contact" class="form-control" placeholder="Contact Number" required>
                </div>
                <div class="col-md-6 mb-3">
                    <input type="text" name="guardian_address" class="form-control" placeholder="Guardian Address" required>
                </div>
                <div class="col-md-6 mb-3">
                    <input type="text" name="guardian_relationship" class="form-control" placeholder="Relationship to Student" required>
                </div>
            </div>

            <h4>Academic Information</h4>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <input type="text" name="elementary_school" class="form-control" placeholder="Elementary School" required>
                </div>
                <div class="col-md-6 mb-3">
                    <input type="text" name="year_graduated" class="form-control" placeholder="Year Graduated" required>
                </div>
            </div>

            <h4>Required Documents</h4>
            <div class="mb-3">
                <label>Birth Certificate</label>
                <input type="file" name="birth_certificate" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Form 137</label>
                <input type="file" name="good_moral" class="form-control">
            </div>
            <div class="mb-3">
                <label>Student Signature</label>
                <input type="file" name="student_signature" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Student ID Picture</label>
                 <input type="file" name="id_photo" class="form-control" required>
            </div>

           <button type="submit" name="submit" class="btn btn-primary w-100">Register</button>

        </form>

        <script>
            function autoUpdateSchoolYear() {
                const grade = document.querySelector('[name="grade_level"]').value;
                const yearInput = document.getElementById('school_year');

                if (yearInput.value.length === 4 && !isNaN(yearInput.value)) {
                    const start = parseInt(yearInput.value);
                        let gap = 0;

                        if (grade === "Grade 7") gap = 4;
                        else if (grade === "Grade 8") gap = 3;
                        else if (grade === "Grade 9") gap = 2;
                        else if (grade === "Grade 10") gap = 1;
                        else if (grade === "Grade 11") gap = 2;
                        else if (grade === "Grade 12") gap = 1;

                        if (gap > 0) {
                            yearInput.value = `${start}-${start + gap}`;
                        }
                    }
                }

            document.addEventListener('DOMContentLoaded', () => {
                document.querySelector('[name="grade_level"]').addEventListener('change', autoUpdateSchoolYear);
                document.getElementById('school_year').addEventListener('input', autoUpdateSchoolYear);
                });
        </script>


    </div>
</body>
</html>