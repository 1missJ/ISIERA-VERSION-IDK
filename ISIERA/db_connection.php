<?php
$servername = "localhost";
$username = "root"; // Change if using a different database user
$password = ""; // Change if your MySQL user has a password
$dbname = "TapInTime"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
