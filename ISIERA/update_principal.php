<?php
include('db_connection.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newName = trim($_POST['principal_name']);

    if (!empty($newName)) {
        $check = $conn->query("SELECT * FROM principal");
        if ($check->num_rows > 0) {
            $conn->query("UPDATE principal SET principal_name = '$newName'");
        } else {
            $conn->query("INSERT INTO principal (principal_name) VALUES ('$newName')");
        }
    }
}

header("Location: dashboard.php");
exit;
?>