<?php
session_start();
include 'db_connection.php';

// Make sure the student is logged in
if (!isset($_SESSION['lrn'])) {
    header("Location: index.php");
    exit();
}

$lrn = $_SESSION['lrn']; // Assuming LRN is stored in the session

// Query to fetch the student's information based on their LRN
$sql = "SELECT 
    first_name, middle_name, last_name, lrn, date_of_birth, gender, 
    citizenship, address, contact_number, email,
    guardian_name, guardian_contact, guardian_address, guardian_relationship,
    elementary_school, school_year, grade_level, student_type, created_at
    FROM students
    WHERE lrn = '$lrn'"; // Fetch information for the logged-in student

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Student Information</title>
    <link rel="stylesheet" href="assets/css/student_portal_nav.css">
    <style>
 

        .container {
            margin-top: 50px;
        }

        .info-container {
            background: #fff;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 30px;
            text-align: center;
        }

        .row {
            margin-bottom: 20px;
        }

        .col-label {
            font-weight: bold;
            color: #4a4a4a;
            text-transform: capitalize;
            
        }

        .col-value {
            color: #5a5a5a;
        }

        .card {
            margin: 10px 0;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: #fafafa;
        }
    </style>
</head>
<body>

<?php include('student_portal_navigation.php'); ?>

<div class="container info-container">
    <h2>Student Information</h2>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card">
                <div class="row">
                    <div class="col-3 col-label">First Name</div>
                    <div class="col-9 col-value"><?= htmlspecialchars($row['first_name']) ?></div>
                </div>
                <div class="row">
                    <div class="col-3 col-label">Middle Name</div>
                    <div class="col-9 col-value"><?= htmlspecialchars($row['middle_name']) ?></div>
                </div>
                <div class="row">
                    <div class="col-3 col-label">Last Name</div>
                    <div class="col-9 col-value"><?= htmlspecialchars($row['last_name']) ?></div>
                </div>
                <div class="row">
                    <div class="col-3 col-label">LRN</div>
                    <div class="col-9 col-value"><?= htmlspecialchars($row['lrn']) ?></div>
                </div>
                <div class="row">
                    <div class="col-3 col-label">Date of Birth</div>
                    <div class="col-9 col-value"><?= htmlspecialchars(date("F j, Y", strtotime($row['date_of_birth']))) ?></div>
                </div>
                <div class="row">
                    <div class="col-3 col-label">Gender</div>
                    <div class="col-9 col-value"><?= htmlspecialchars($row['gender']) ?></div>
                </div>
                <div class="row">
                    <div class="col-3 col-label">Citizenship</div>
                    <div class="col-9 col-value"><?= htmlspecialchars($row['citizenship']) ?></div>
                </div>
                <div class="row">
                    <div class="col-3 col-label">Address</div>
                    <div class="col-9 col-value"><?= htmlspecialchars($row['address']) ?></div>
                </div>
                <div class="row">
                    <div class="col-3 col-label">Contact #</div>
                    <div class="col-9 col-value"><?= htmlspecialchars($row['contact_number']) ?></div>
                </div>
                <div class="row">
                    <div class="col-3 col-label">Email</div>
                    <div class="col-9 col-value"><?= htmlspecialchars($row['email']) ?></div>
                </div>
                <div class="row">
                    <div class="col-3 col-label">Guardian Name</div>
                    <div class="col-9 col-value"><?= htmlspecialchars($row['guardian_name']) ?></div>
                </div>
                <div class="row">
                    <div class="col-3 col-label">Guardian Contact</div>
                    <div class="col-9 col-value"><?= htmlspecialchars($row['guardian_contact']) ?></div>
                </div>
                <div class="row">
                    <div class="col-3 col-label">Guardian Address</div>
                    <div class="col-9 col-value"><?= htmlspecialchars($row['guardian_address']) ?></div>
                </div>
                <div class="row">
                    <div class="col-3 col-label">Guardian Relationship</div>
                    <div class="col-9 col-value"><?= htmlspecialchars($row['guardian_relationship']) ?></div>
                </div>
                <div class="row">
                    <div class="col-3 col-label">Elementary School</div>
                    <div class="col-9 col-value"><?= htmlspecialchars($row['elementary_school']) ?></div>
                </div>
                <div class="row">
                    <div class="col-3 col-label">School Year</div>
                    <div class="col-9 col-value"><?= htmlspecialchars($row['school_year']) ?></div>
                </div>
                <div class="row">
                    <div class="col-3 col-label">Grade Level</div>
                    <div class="col-9 col-value"><?= htmlspecialchars($row['grade_level']) ?></div>
                </div>
                <div class="row">
                    <div class="col-3 col-label">Student Type</div>
                    <div class="col-9 col-value"><?= htmlspecialchars($row['student_type']) ?></div>
                </div>
                <div class="row">
                    <div class="col-3 col-label">Created At</div>
                    <div class="col-9 col-value"><?= htmlspecialchars(date("M d, Y h:i A", strtotime($row['created_at']))) ?></div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-center">No student records found.</p>
    <?php endif; ?>
</div>

<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>

<?php $conn->close(); ?>
