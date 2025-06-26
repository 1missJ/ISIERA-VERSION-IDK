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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/student_portal_nav.css">

    <style>

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-control {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            background: #28a745;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
        }

        .success {
            background: #d4edda;
            color: #155724;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<?php include('student_portal_navigation.php'); ?>

<div class="container">
    <h2>Change Password</h2>

    <!-- Success and Error Messages -->
    <?php if (!empty($success_message)): ?>
        <div class="message success"><?= $success_message ?></div>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <div class="message error"><?= $error_message ?></div>
    <?php endif; ?>

    <!-- Password Change Form -->
    <form method="POST">
        <label for="old_password">Old Password</label>
        <input type="password" name="old_password" id="old_password" class="form-control" required>

        <label for="new_password">New Password</label>
        <input type="password" name="new_password" id="new_password" class="form-control" required>

        <button type="submit" name="change_password">Change Password</button>
    </form>
</div>

<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
