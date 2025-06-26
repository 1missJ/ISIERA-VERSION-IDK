<?php
session_start();
include 'db_connection.php';

// Ensure the student is logged in
if (!isset($_SESSION['lrn'])) {
    header("Location: index.php");
    exit();
}

$lrn = $_SESSION['lrn'];

// Query to fetch student information
$sql = "SELECT 
    first_name, middle_name, last_name, lrn, date_of_birth, gender, 
    citizenship, address, contact_number, email,
    guardian_name, guardian_contact, guardian_address, guardian_relationship,
    elementary_school, school_year, grade_level, student_type, created_at
    FROM students
    WHERE lrn = '$lrn'";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/student_portal_nav.css">
</head>
<body>

<?php include('student_portal_navigation.php'); ?>

<div class="container my-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Student Information</h4>
        </div>
        <div class="card-body">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>First Name:</strong> <?= htmlspecialchars($row['first_name']) ?></div>
                        <div class="col-md-6"><strong>Middle Name:</strong> <?= htmlspecialchars($row['middle_name']) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Last Name:</strong> <?= htmlspecialchars($row['last_name']) ?></div>
                        <div class="col-md-6"><strong>LRN:</strong> <?= htmlspecialchars($row['lrn']) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Date of Birth:</strong> <?= htmlspecialchars(date("F j, Y", strtotime($row['date_of_birth']))) ?></div>
                        <div class="col-md-6"><strong>Gender:</strong> <?= htmlspecialchars($row['gender']) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Citizenship:</strong> <?= htmlspecialchars($row['citizenship']) ?></div>
                        <div class="col-md-6"><strong>Address:</strong> <?= htmlspecialchars($row['address']) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Contact Number:</strong> <?= htmlspecialchars($row['contact_number']) ?></div>
                        <div class="col-md-6"><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></div>
                    </div>
                    <hr>
                    <h5 class="mt-4 mb-3 text-primary">Guardian Information</h5>
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Name:</strong> <?= htmlspecialchars($row['guardian_name']) ?></div>
                        <div class="col-md-6"><strong>Contact:</strong> <?= htmlspecialchars($row['guardian_contact']) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Address:</strong> <?= htmlspecialchars($row['guardian_address']) ?></div>
                        <div class="col-md-6"><strong>Relationship:</strong> <?= htmlspecialchars($row['guardian_relationship']) ?></div>
                    </div>
                    <hr>
                    <h5 class="mt-4 mb-3 text-primary">Academic Information</h5>
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Elementary School:</strong> <?= htmlspecialchars($row['elementary_school']) ?></div>
                        <div class="col-md-3"><strong>School Year:</strong> <?= htmlspecialchars($row['school_year']) ?></div>
                        <div class="col-md-3"><strong>Grade Level:</strong> <?= htmlspecialchars($row['grade_level']) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Student Type:</strong> <?= htmlspecialchars($row['student_type']) ?></div>
                        <div class="col-md-6"><strong>Date Registered:</strong> <?= htmlspecialchars(date("M d, Y h:i A", strtotime($row['created_at']))) ?></div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center text-danger">No student records found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>

<?php $conn->close(); ?>
