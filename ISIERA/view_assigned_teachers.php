<?php
include('db_connection.php');

$subject_id = $_GET['subject_id'] ?? null;

// Get subject info
$subject_query = $conn->prepare("SELECT subject_name FROM subjects WHERE id = ?");
$subject_query->bind_param("i", $subject_id);
$subject_query->execute();
$subject = $subject_query->get_result()->fetch_assoc();

// Get assigned teachers for Regular students
$assigned_regular_query = $conn->prepare("SELECT Faculty.id, Faculty.name 
                                          FROM assigned_subjects 
                                          JOIN Faculty ON assigned_subjects.teacher_id = Faculty.id 
                                          WHERE assigned_subjects.subject_id = ? AND assigned_subjects.student_type = 'Regular'");
$assigned_regular_query->bind_param("i", $subject_id);
$assigned_regular_query->execute();
$assigned_regular_teachers = $assigned_regular_query->get_result();

// Get assigned teachers for STI students
$assigned_sti_query = $conn->prepare("SELECT Faculty.id, Faculty.name 
                                      FROM assigned_subjects 
                                      JOIN Faculty ON assigned_subjects.teacher_id = Faculty.id 
                                      WHERE assigned_subjects.subject_id = ? AND assigned_subjects.student_type = 'STI'");
$assigned_sti_query->bind_param("i", $subject_id);
$assigned_sti_query->execute();
$assigned_sti_teachers = $assigned_sti_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigned Teachers for <?= htmlspecialchars($subject['subject_name']) ?></title>
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/view_subjects.css">
</head>
<body>

<!-- Include Sidebar -->
<?php include('sidebar.php'); ?>

<div class="subject-manager-container">
    <h2 class="subject-title">Assigned Teachers for <?= htmlspecialchars($subject['subject_name']) ?></h2>
    
    <div class="assigned-teachers-container">
        <!-- Regular Teachers Section -->
        <div class="assigned-teachers">
            <h3 class="student-type-title">Regular Students</h3>
            <div class="teacher-list">
                <?php if ($assigned_regular_teachers->num_rows > 0): ?>
                    <ul>
                        <?php while ($teacher = $assigned_regular_teachers->fetch_assoc()): ?>
                            <li class="teacher-item"><?= htmlspecialchars($teacher['name']) ?></li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="no-teachers">No teachers assigned for Regular students.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- STI Teachers Section -->
        <div class="assigned-teachers">
            <h3 class="student-type-title">STI Students</h3>
            <div class="teacher-list">
                <?php if ($assigned_sti_teachers->num_rows > 0): ?>
                    <ul>
                        <?php while ($teacher = $assigned_sti_teachers->fetch_assoc()): ?>
                            <li class="teacher-item"><?= htmlspecialchars($teacher['name']) ?></li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="no-teachers">No teachers assigned for STI students.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>
