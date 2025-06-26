<?php
// Include the database connection file
include 'db_connection.php';

// Get the year level (Grade 7, Grade 8, etc.) from the request
$year_level = $_GET['year_level'];

// Query to fetch all students data filtered by grade
$query = "SELECT lrn, CONCAT(first_name, ' ', middle_name, ' ', last_name) AS fullname, email, section FROM students WHERE section LIKE '%$year_level%'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $students = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
    echo json_encode($students); // Return the data as JSON
} else {
    echo json_encode([]); // Return an empty array if no students found
}

mysqli_close($conn);
?>
