<?php
session_start(); 
ob_clean(); // Clear any prior output
error_reporting(0);
ini_set('display_errors', 0);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Optional: include DB if it doesn't output anything
// include 'db_connection.php';

$input = json_decode(file_get_contents("php://input"), true);
$teacherId = $input["teacher_id"];
$dob = $input["dob"];

$conn = new mysqli("localhost", "root", "", "tapintime");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed"]);
    exit;
}

$query = "SELECT * FROM faculty WHERE teacher_id = ? AND dob = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $teacherId, $dob);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    echo json_encode([
        "success" => true,
        "message" => "Login successful",
        "teacher" => [
            "id" => $row["teacher_id"],
            "name" => $row["name"] // Adjust column name if needed
        ]
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid ID or DOB"]);
}

$conn->close();
?>
