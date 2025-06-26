<?php
session_start();
include('db_connection.php'); // Adjust this if needed

$lrn = $_SESSION['lrn'];

// Get student info (student_type is VARCHAR)
$stmt = $conn->prepare("SELECT id, grade_level, student_type FROM students WHERE lrn = ?");
$stmt->bind_param("s", $lrn);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

$student_id = $student['id'];
$grade_level = $student['grade_level'];
$student_type = $student['student_type'];

// Fetch subjects that match student_type and grade_level
$stmt = $conn->prepare("SELECT 
                            s.id,
                            s.subject_name
                        FROM assigned_grade_subjects ags
                        JOIN subjects s ON ags.subject_id = s.id
                        WHERE ags.grade_level = ?
                          AND ags.student_type = ?");
$stmt->bind_param("is", $grade_level, $student_type); // student_type is VARCHAR
$stmt->execute();
$subjects = $stmt->get_result();

// Handle enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subject_id'])) {
    $subject_id = $_POST['subject_id'];

    // Check if already enrolled
    $check = $conn->prepare("SELECT * FROM enrollments WHERE student_id = ? AND subject_id = ?");
    $check->bind_param("ii", $student_id, $subject_id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows === 0) {
        $enroll = $conn->prepare("INSERT INTO enrollments (student_id, subject_id) VALUES (?, ?)");
        $enroll->bind_param("ii", $student_id, $subject_id);
        $enroll->execute();
    }
}
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

<p style="margin-top: 7px;">Grade Level: <?= $grade_level ?> | Student Type: <?= $student_type ?></p>
<hr>

<?php 
// Reformatted subjects as pairs of Subject => Instructor
$subjects = [
    ['subject' => 'Mathematics', 'instructor' => 'Jaylin Fernandez'],
    ['subject' => 'Science', 'instructor' => 'Marvita Yadan'],
    ['subject' => 'Filipino', 'instructor' => 'Elmarie Cataggatan'],
    ['subject' => 'PE and Health', 'instructor' => 'Joyce Baquiran']
];
?>

<style>
    .subjects-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 50vh;
        justify-content: center;
        text-align: center;
    }

    .subjects-list {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        width: 100%;
        max-width: 600px;
        margin-top: 20px;
    }

    .subject-block {
        text-align: left;
        padding: 10px;
    }

    .subject-name {
        font-size: 20px;
        font-weight: bold;
        margin-left:100px;
    }

    .instructor-label {
        font-size: 14px;
        margin-top: 5px;
        color: #555;
        margin-left:100px;
    }

    .instructor-name {
        font-size: 16px;
        margin-top: 2px;
        margin-left:100px;
    }
</style>

<div class="subjects-container">
    <h2>Enrolled Subject</h2>

    <div class="subjects-list">
        <?php foreach ($subjects as $item): ?>
            <div class="subject-block">
                <div class="subject-name"><?= htmlspecialchars($item['subject']) ?></div>
                <div class="instructor-label">Instructor:</div>
                <div class="instructor-name"><?= htmlspecialchars($item['instructor']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
