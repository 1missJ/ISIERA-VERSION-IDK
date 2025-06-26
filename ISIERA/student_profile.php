<?php
// Include database connection
include 'db_connection.php';

// Check if LRN is passed in URL
if (isset($_GET['lrn'])) {
    $lrn = $_GET['lrn'];
} else {
    echo "<script>alert('Student not found!'); window.location='student_details.php';</script>";
    exit();
}

// Determine mode and readonly status
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'edit';
$is_readonly = ($mode === 'view');

// Initialize variables
$first_name = $middle_name = $last_name = $date_of_birth = $gender = $citizenship = $contact_number = $address = $email = "";
$guardian_name = $guardian_contact = $guardian_address = $guardian_relationship = "";
$elementary_school = $year_graduated = "";
$id_photo = $birth_certificate = $good_moral = $student_signature = "";

// Handle POST (Update student)
if ($_SERVER["REQUEST_METHOD"] == "POST" && !$is_readonly) {
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $citizenship = $_POST['citizenship'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $guardian_name = $_POST['guardian_name'];
    $guardian_contact = $_POST['guardian_contact'];
    $guardian_address = $_POST['guardian_address'];
    $guardian_relationship = $_POST['guardian_relationship'];
    $elementary_school = $_POST['elementary_school'];
    $year_graduated = $_POST['year_graduated'];

    $sql = "UPDATE students SET first_name=?, middle_name=?, last_name=?, date_of_birth=?, gender=?, citizenship=?, contact_number=?, address=?, email=?, guardian_name=?, guardian_contact=?, guardian_address=?, guardian_relationship=?, elementary_school=?, year_graduated=? WHERE lrn=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssssssss", 
        $first_name, $middle_name, $last_name, $date_of_birth, $gender, $citizenship, $contact_number,
        $address, $email, $guardian_name, $guardian_contact, $guardian_address, $guardian_relationship,
        $elementary_school, $year_graduated, $lrn
    );

    if ($stmt->execute()) {
        echo "<script>alert('Student details updated successfully'); window.location='student_profile.php?lrn=$lrn&mode=view';</script>";
    } else {
        echo "<script>alert('Error updating student details.');</script>";
    }
    $stmt->close();
} else {
    // Fetch student data
    $sql = "SELECT * FROM students WHERE lrn=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $lrn);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $first_name = $row['first_name'];
        $middle_name = $row['middle_name'];
        $last_name = $row['last_name'];
        $date_of_birth = $row['date_of_birth'];
        $gender = $row['gender'];
        $citizenship = $row['citizenship'];
        $contact_number = $row['contact_number'];
        $address = $row['address'];
        $email = $row['email'];
        $guardian_name = $row['guardian_name'];
        $guardian_contact = $row['guardian_contact'];
        $guardian_address = $row['guardian_address'];
        $guardian_relationship = $row['guardian_relationship'];
        $elementary_school = $row['elementary_school'];
        $year_graduated = $row['year_graduated'];
        $id_photo = $row['id_photo'];
        $birth_certificate = $row['birth_certificate'];
        $good_moral = $row['good_moral'];
        $student_signature = $row['student_signature'];
    } else {
        echo "<script>alert('No student data found!');</script>";
    }
}

// Set file paths
$id_photo_path = !empty($id_photo) ? "uploads/$id_photo" : "assets/imgs/placeholder.png";
$birth_certificate_path = !empty($birth_certificate) ? "uploads/$birth_certificate" : "assets/imgs/placeholder.png";
$good_moral_path = !empty($good_moral) ? "uploads/$good_moral" : "assets/imgs/placeholder.png";
$student_signature_path = !empty($student_signature) ? "uploads/$student_signature" : "assets/imgs/placeholder.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<div class="row">
    <?php include('sidebar.php'); ?>
</div>

<div class="main-content">
    <form method="POST" action="">
<div class="card">
            <div class="card-header bg-primary text-white">
                Personal Information
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4  form-group">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="<?= $first_name ?>" <?= $is_readonly ? 'readonly' : '' ?>>
                    </div>
                    <div class="col-md-4  form-group">
                                <label for="middle_name">Middle Name:</label>
                                <input type="text" class="form-control" name="middle_name" value="<?= $middle_name ?>" <?= $is_readonly ? 'readonly' : '' ?>>
                    </div>
                    <div class="col-md-4  form-group">
                                <label for="last_name">Last Name:</label>
                                <input type="text" class="form-control" name="last_name" value="<?= $last_name ?>" <?= $is_readonly ? 'readonly' : '' ?>>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4  form-group">
                                <label for="lrn">LRN:</label>
                                <input type="text" class="form-control" name="lrn" value="<?= $lrn ?>" <?= $is_readonly ? 'readonly' : '' ?>>
                    </div>
                    <div class="col-md-4  form-group">
                                <label for="dob">Date of Birth:</label>
                                <input type="text" class="form-control" name="date_of_birth" value="<?= $date_of_birth ?>" <?= $is_readonly ? 'readonly' : '' ?>>
                    </div>
                    <div class="col-md-4  form-group">
                                <label for="gender">Gender:</label>
                                <input type="text" class="form-control" name="gender" value="<?= $gender ?>" <?= $is_readonly ? 'readonly' : '' ?>>
                    </div>
                    <div class="col-md-4  form-group">
                                <label for="lrn">Citizenship</label>
                                <input type="text" class="form-control" name="citizenship" value="<?= $citizenship ?>" <?= $is_readonly ? 'readonly' : '' ?>>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4  form-group">
                                <label for="dob">Contact Number:</label>
                                <input type="text" class="form-control" name="contact_number" value="<?= $contact_number ?>" <?= $is_readonly ? 'readonly' : '' ?>>
                    </div>
                    <div class="col-md-4  form-group">
                                <label for="lrn">Address</label>
                                <input type="text" class="form-control" name="address" value="<?= $address ?>" <?= $is_readonly ? 'readonly' : '' ?>>
                    </div>
                    <div class="col-md-4  form-group">
                                <label for="dob">Email Address:</label>
                                <input type="text" class="form-control" name="email" value="<?= $email ?>" <?= $is_readonly ? 'readonly' : '' ?>>
                    </div>
                </div>
</div>

        <!-- Guardian Info -->
<div class="card">
            <div class="card-header bg-primary text-white">
                Parent/Guardian Information
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4  form-group">
                                <label for="guardian_name">Guardian Name:</label>
                                <input type="text" class="form-control" name="guardian_name" value="<?= $guardian_name ?>" <?= $is_readonly ? 'readonly' : '' ?>>
                    </div>
                    <div class="col-md-4  form-group">
                                <label for="guardian_contact">Guardian Contact:</label>
                                <input type="text" class="form-control" name="guardian_contact" value="<?= $guardian_contact ?>" <?= $is_readonly ? 'readonly' : '' ?>>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4  form-group">
                                <label for="guardian_address">Guardian Address:</label>
                                <input type="text" class="form-control" name="guardian_address" value="<?= $guardian_address ?>" <?= $is_readonly ? 'readonly' : '' ?>>
                    </div>
                    <div class="col-md-4 form-group">
                                <label for="guardian_relationship">Relationship:</label>
                                <input type="text" class="form-control" name="guardian_relationship" value="<?= $guardian_relationship ?>" <?= $is_readonly ? 'readonly' : '' ?>>
                    </div>
                </div>
            </div>

                    <!-- Academic Information -->
<div class="card">
            <div class="card-header bg-primary text-white">
                Academic Information
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4  form-group">
                                <label for="elementary_school">Elementary School:</label>
                                <input type="text" class="form-control" name="elementary_school" value="<?= $elementary_school ?>" <?= $is_readonly ? 'readonly' : '' ?>>
                    </div>
                    <div class="col-md-4  form-group">
                                <label for="year_graduated">Year Graduated:</label>
                                <input type="text" class="form-control" name="year_graduated" value="<?= $year_graduated ?>" <?= $is_readonly ? 'readonly' : '' ?>>
                    </div>
            </div>
        </div>

                    <!-- Required Documents -->
<div class="card">
            <div class="card-header bg-primary text-white">
            Documents
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4  form-group">
                            <div class="rectangle-container">
                                <label for="id_photo">ID Photo:</label>
                                <img src="<?php echo !empty($id_photo) ? $id_photo : 'assets/imgs/placeholder.png'; ?>" class="document-box">
                            </div>
                        </div>
                    <div class="col-md-4  form-group">
                            <div class="rectangle-container">
                                <label for="birth_certificate">Birth Certificate:</label>
                                <img src="<?php echo !empty($birth_certificate) ? $birth_certificate : 'assets/imgs/placeholder.png'; ?>" class="document-box">
                            </div>
                        </div>
                        <div class="col-md-4  form-group">
                            <div class="rectangle-container">
                                <label for="good_moral">Good Moral Certificate:</label>
                                <img src="<?php echo !empty($good_moral) ? $good_moral : 'assets/imgs/placeholder.png'; ?>" class="document-box">
                            </div>
                        </div>
                        <div class="col-md-4  form-group">
                            <div class="rectangle-container">
                                <label for="student_signature">Student Signature:</label>
                                <img src="<?php echo !empty($student_signature) ? $student_signature : 'assets/imgs/placeholder.png'; ?>" class="document-box">
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="form-buttons">
                        <?php if (!$is_readonly): ?>
                            <button class="save-btn" type="submit">Save</button>
                        <?php endif; ?>
                            <button class="close-btn" id="closeBtn" type="button">Close</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.getElementById("closeBtn").addEventListener('click', function() {
            window.history.back();
        });
    </script>

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>      
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>