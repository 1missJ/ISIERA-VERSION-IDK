<?php
include('db_connection.php');
$schoolYear = "2024-2025"; // fallback

// Fetch the current school year
$sy_stmt = $conn->prepare("SELECT year FROM school_years WHERE is_current = 1 LIMIT 1");
$sy_stmt->execute();
$sy_result = $sy_stmt->get_result();

if ($sy_result->num_rows > 0) {
    $row = $sy_result->fetch_assoc();
    $schoolYear = $row['year'];
}

// Fetch the student's name
$student_id = $_SESSION['student_id']; // Assuming the student ID is stored in the session

$name_stmt = $conn->prepare("SELECT first_name, middle_name, last_name FROM students WHERE id = ?");
$name_stmt->bind_param("i", $student_id);
$name_stmt->execute();
$name_result = $name_stmt->get_result();

$student_name = "Name not found"; // Default in case no name is found
if ($name_result->num_rows > 0) {
    $name_row = $name_result->fetch_assoc();
    $student_name = $name_row['first_name'] . " " . $name_row['middle_name'] . " " . $name_row['last_name'];
}
?>
<style>
    .student-nav-container {
        background-color: #0057a3;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 5px 20px;
        height: 60px;
        font-family: Arial, sans-serif;
    }

    .brand-logo {
        list-style: none;
        margin-left:20px;
        padding: 0;
    }

    .logo-container img {
        height: 40px;
        width: auto;
    }

    .student-nav {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .student-nav a {
        text-decoration: none;
font-weight: 600;
color: whitesmoke;
font-size: 16px;
padding: 6px 10px;
border-radius: 8px;
transition: background-color 0.2s ease;
display: flex;
align-items: center;
gap: 6px;
margin-left:50px;
    }
    </style>

<div class="student-nav-container">
    <li class="brand-logo">
        <a href="#">
            <div class="logo-container">
                <img src="assets/imgs/logo.png" alt="Logo">
            </div>
        </a>
    </li>

    <div class="student-nav">
        <a href="information.php" class="active">
            <ion-icon name="person-circle-outline"></ion-icon>
            Information
        </a>
        <a href="enrollment.php">
            <ion-icon name="document-text-outline"></ion-icon>
            Enrollment
        </a>
        <a href="enrolled_subject.php">
            <ion-icon name="book-outline"></ion-icon>
            Subjects
        </a>
        <a href="#">
            <ion-icon name="calendar-outline"></ion-icon>
            SY: <?= htmlspecialchars($schoolYear) ?>
        </a>
        <a href="student_setting.php">
            <ion-icon name="settings-outline"></ion-icon>
            User: <?= htmlspecialchars($student_name) ?>
        </a>
        <a href="logout.php">
            <ion-icon name="log-out-outline"></ion-icon>
            Sign Out
        </a>
    </div>
</div>