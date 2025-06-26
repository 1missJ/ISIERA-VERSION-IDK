<?php
include('db_connection.php');

// ✅ Default fallback values
$schoolYear = "Not Set";
$student_name = "Name not found";

// ✅ Fetch most recent school year from `school_years` table (no is_current)
$sy_stmt = $conn->prepare("SELECT year FROM school_years ORDER BY id DESC LIMIT 1");
$sy_stmt->execute();
$sy_result = $sy_stmt->get_result();
if ($sy_result->num_rows > 0) {
    $row = $sy_result->fetch_assoc();
    $schoolYear = $row['year'];
}

// ✅ Fetch student name
if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];
    $name_stmt = $conn->prepare("SELECT first_name, middle_name, last_name FROM students WHERE id = ?");
    $name_stmt->bind_param("i", $student_id);
    $name_stmt->execute();
    $name_result = $name_stmt->get_result();

    if ($name_result->num_rows > 0) {
        $name_row = $name_result->fetch_assoc();
        $student_name = $name_row['first_name'] . " " . $name_row['middle_name'] . " " . $name_row['last_name'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Portal | TapInTime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/student_portal_nav.css" />
</head>
<body>

<!-- ✅ Navigation Bar -->
<div class="student-nav-container">
  <a href="#" class="nav-item logo">
    <img src="assets/imgs/logo.png" alt="Logo" />
  </a>
  <a href="information.php" class="nav-item">
    <ion-icon name="person-circle-outline"></ion-icon>
    Information
  </a>
  <a href="attendance.php" class="nav-item">
    <ion-icon name="document-text-outline"></ion-icon>
    Attendance
  </a>
  <a href="#" class="nav-item">
    <ion-icon name="calendar-outline"></ion-icon>
    SY: <?= htmlspecialchars($schoolYear) ?>
  </a>
  <a href="student_setting.php" class="nav-item">
    <ion-icon name="settings-outline"></ion-icon>
    User: <?= htmlspecialchars($student_name) ?>
  </a>
  <a href="logout.php" class="nav-item">
    <ion-icon name="log-out-outline"></ion-icon>
    Sign Out
  </a>
</div>

<!-- ✅ Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
