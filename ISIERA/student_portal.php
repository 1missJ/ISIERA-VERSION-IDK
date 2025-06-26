<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Portal | TapInTime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/student_portal_nav.css">
</head>
<body>

<?php include('student_portal_navigation.php'); ?>

<div class="portal-content" id="content-area">
    <h2>📘 Welcome to your Student Portal</h2>
    <p>Select an option from the menu above to get started.</p>
</div>

<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
