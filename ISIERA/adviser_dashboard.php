<?php
session_start();

// Redirect if not logged in as teacher
if (!isset($_SESSION['teacher_id'])) {
    header("Location: index.php");
    exit();
}

$teacher_name = $_SESSION['teacher_name'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class Adviser Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include('adviser_sidebar.php'); ?>

 <script src="assets/js/main.js"></script>      
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>