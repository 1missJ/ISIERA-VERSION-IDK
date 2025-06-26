<?php
// Include database connection
include 'db_connection.php';

$lrn = isset($_GET['lrn']) ? $_GET['lrn'] : '';
if (empty($lrn)) {
    die("Invalid LRN.");
}

$sql = "SELECT first_name, middle_name, last_name, id_photo, guardian_name, guardian_address, guardian_contact, school_year 
        FROM students WHERE lrn = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $lrn);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die("Student not found.");
}

// Format student's name
$middle_initial = !empty($student['middle_name']) ? strtoupper(substr($student['middle_name'], 0, 1)) . '.' : '';
$full_name = strtoupper($student['first_name'] . ' ' . $middle_initial . ' ' . $student['last_name']);

// Set image path
$id_photo_path = htmlspecialchars($student['id_photo']);

if (!empty($id_photo_path) && file_exists($id_photo_path)) {
    $id_photo_path = $id_photo_path;
} else {
    $id_photo_path = "assets/default.png"; // Default image if not found
}

$principal_sql = "SELECT principal_name FROM principal LIMIT 1";
$principal_result = $conn->query($principal_sql);
if ($principal_result && $principal_result->num_rows > 0) {
    $principal_row = $principal_result->fetch_assoc();
    $principal_name = strtoupper($principal_row['principal_name']);
}

// Debugging output
if (!file_exists($id_photo_path)) {
    error_log("File not found: " . $id_photo_path);
}

// Format guardian name
function formatGuardianName($name) {
    $nameParts = explode(" ", trim($name));
    $first = isset($nameParts[0]) ? ucfirst(strtolower($nameParts[0])) : '';
    $middle = isset($nameParts[1]) ? strtoupper(substr($nameParts[1], 0, 1)) . '.' : '';
    $last = '';

    // If more than 2 parts, combine the rest as last name
    if (count($nameParts) > 2) {
        for ($i = 2; $i < count($nameParts); $i++) {
            $last .= ucfirst(strtolower($nameParts[$i])) . ' ';
        }
        $last = trim($last);
    }

    return trim("{$first} {$middle} {$last}");
}

$guardian_name_raw = !empty($student['guardian_name']) ? $student['guardian_name'] : '';
$guardian_name = !empty($guardian_name_raw) ? htmlspecialchars(formatGuardianName($guardian_name_raw)) : 'N/A';
$guardian_address = !empty($student['guardian_address']) ? htmlspecialchars($student['guardian_address']) : 'N/A';
$guardian_contact = !empty($student['guardian_contact']) ? htmlspecialchars($student['guardian_contact']) : 'N/A';

// Fetch school year
$school_year = !empty($student['school_year']) ? htmlspecialchars($student['school_year']) : 'N/A';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student ID - TapInTime</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/id_template.css">
</head>
<body>

<div class="print-area">
    <div class="id-wrapper">

<!-- Front ID -->
<div class="id-container">
    <img src="assets/id/id_f.jpg" class="background-img">
    <img id="modalIDPhoto" src="<?php echo $id_photo_path; ?>" class="id-photo">
    <div class="student-name-container">
        <div class="student-name"><?php echo $full_name; ?></div>
    </div>
    <div class="student-lrn-container">
        <div class="student-lrn"><?php echo htmlspecialchars($lrn); ?></div>
    </div>
    <div class="principal-container">
        <div class="principal"><?php echo htmlspecialchars($principal_name); ?></div>
    </div>
</div>


        <!-- Back ID -->
<div class="id-container">
    <img src="assets/id/id_b.jpg" class="background-img">
    
    <!-- School Year Section -->
<div class="school-year-container">
    <div class="school-year"><?php echo $school_year; ?></div>
</div>
<div class="guardian-info-container">
    <div class="guardian-info">
        <div class="guardian-name"><?php echo $guardian_name; ?></div>
        <div class="guardian-address"><?php echo $guardian_address; ?></div>
        <div class="guardian-contact"><?php echo $guardian_contact; ?></div>
    </div>
</div>

        <!-- Print Button positioned correctly below Back ID -->
        <button class="print-btn" onclick="window.print();">Print ID</button>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const container = document.querySelector('.student-name-container');
    const name = document.querySelector('.student-name');

    if (container && name) {
        let fontSize = 20;
        name.style.fontSize = fontSize + "px";

        // Adjust only if too wide
        while ((name.scrollWidth > container.clientWidth) && fontSize > 8) {
            fontSize -= 0.5;
            name.style.fontSize = fontSize + "px";
        }
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const nameEl = document.querySelector('.guardian-name');
    const container = document.querySelector('.guardian-info-container');

    if (nameEl && container) {
        let fontSize = parseFloat(window.getComputedStyle(nameEl).fontSize);
        const minFontSize = 10;

        // Try to reduce font size to make it fit in 2 lines
        while ((nameEl.scrollHeight > nameEl.offsetHeight || nameEl.scrollWidth > container.clientWidth) && fontSize > minFontSize) {
            fontSize -= 0.5;
            nameEl.style.fontSize = fontSize + "px";
        }
    }
});
</script>

</body>
</html>
