<?php
session_start();

// Redirect if not a counselor
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'counselor') {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Counselor Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include('counselor_sidebar.php'); ?>

 <script src="assets/js/main.js"></script>      
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>
