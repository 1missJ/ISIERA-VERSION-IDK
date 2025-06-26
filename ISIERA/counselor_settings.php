<?php
session_start();
include 'db_connection.php';

$success_message = "";
$error_message = "";

// Only allow logged-in guidance counselor
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'counselor') {
    echo "Access Denied.";
    exit;
}

$user_id = $_SESSION['user_id'];

// Update username
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_username'])) {
    $new_username = $_POST['new_username'];

    $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
    $stmt->bind_param("si", $new_username, $user_id);

    if ($stmt->execute()) {
        $_SESSION['username'] = $new_username;
        $success_message = "Username successfully updated!";
    } else {
        $error_message = "Error updating username.";
    }
    $stmt->close();
}

// Update password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $new_password, $user_id);

    if ($stmt->execute()) {
        $success_message = "Password successfully updated!";
    } else {
        $error_message = "Error updating password.";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Guidance Counselor Settings</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .settings-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-left: 600px
        }

        .form-control {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        button {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            background: #28a745;
            color: #fff;
            font-size: 16px;
        }

        h2 {
            margin-bottom: 20px;
        }

        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
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
<?php include('counselor_sidebar.php'); ?>
<div class="settings-container">
    <h2>Account Settings</h2>

    <?php if (!empty($success_message)): ?>
        <div class="message success"><?= $success_message ?></div>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <div class="message error"><?= $error_message ?></div>
    <?php endif; ?>

    <!-- Change Username -->
    <form method="POST">
        <label for="new_username">New Username</label>
        <input type="text" name="new_username" id="new_username" class="form-control" required>
        <button type="submit" name="change_username">Update Username</button>
    </form>

    <br><br>

    <!-- Change Password -->
    <form method="POST">
        <label for="new_password">New Password</label>
        <input type="password" name="new_password" id="new_password" class="form-control" required>
        <button type="submit" name="change_password">Update Password</button>
    </form>
</div>


 <script src="assets/js/main.js"></script>      
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>
