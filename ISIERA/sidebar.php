<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$userRole = $_SESSION['user_role'] ?? ''; // Default to empty if not set
?>

<div class="container">
    <div class="navigation">
        <ul>
            <li class="brand-logo">
                <a href="#">
                    <div class="logo-container">
                        <img src="assets/imgs/logo.png">
                    </div>
                    <span class="title">TapTrack</span>
                </a>
            </li>

            <?php if ($userRole !== 'counselor'): ?>
                <li>
                    <a href="dashboard.php">
                        <span class="icon"><ion-icon name="home-outline"></ion-icon></span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($userRole === 'counselor' || $userRole === 'admin' || $userRole === 'superadmin'): ?>
                <li>
                    <a href="student_verification.php">
                        <span class="icon"><ion-icon name="checkmark-done-circle-outline"></ion-icon></span>
                        <span class="title">Student Verification</span>
                    </a>
                </li>

                <li>
                    <a href="student_details.php">
                        <span class="icon"><ion-icon name="people-circle-outline"></ion-icon> </span>
                        <span class="title">Student Information</span>
                    </a>
                </li>

                <li>
                    <a href="id_generation.php">
                        <span class="icon"><ion-icon name="card-outline"></ion-icon></span>
                        <span class="title">ID Generation with RFID</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($userRole !== 'counselor'): ?>
                <li>
                    <a href="faculty_registration.php">
                        <span class="icon"><ion-icon name="school-outline"></ion-icon></span>
                        <span class="title">Faculty Registration</span>
                    </a>
                </li>

                <li>
                    <a href="subject_management.php">
                        <span class="icon"><ion-icon name="library-outline"></ion-icon></span>
                        <span class="title">Subjects & Sections</span>

                    </a>
                </li>

                <li style="display: none;">
                            <a href="enrollment_admin.html">
                                <span class="icon"><ion-icon name="newspaper-outline"></ion-icon></span>
                                <span class="title">Enrollment</span>
                            </a>
                        </li>

                <li>
                    <a href="attendance_monitoring.php">
                        <span class="icon"><ion-icon name="stats-chart-outline"></ion-icon></span>
                        <span class="title">Attendance Monitoring</span>
                    </a>
                </li>

                <li>
                    <a href="student_promotion.php">
                        <span class="icon"><ion-icon name="ribbon-outline"></ion-icon></span>
                        <span class="title">Students Promotion</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($userRole === 'superadmin'): ?>
            <li>
            <a href="user.php">
            <span class="icon"><ion-icon name="person-outline"></ion-icon></span>
            <span class="title">Users</span>
            </a>
            </li>
            <?php elseif ($userRole === 'counselor'): ?>
            <li>
            <a href="counselor_settings.php">
            <span class="icon"><ion-icon name="settings-outline"></ion-icon></span>
            <span class="title">Account Settings</span>
            </a>
            </li>
            <?php endif; ?>


            <li>
                <a href="index.php">
                    <span class="icon"><ion-icon name="log-in-outline"></ion-icon></span>
                    <span class="title">Sign out</span>
                </a>
            </li>
        </ul>
    </div>
</div>