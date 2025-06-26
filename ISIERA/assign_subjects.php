<?php
include('db_connection.php');

$subject_id = $_GET['subject_id'] ?? null;

// Get subject info
$subject_query = $conn->prepare("SELECT subject_name, student_type FROM subjects WHERE id = ?");
$subject_query->bind_param("i", $subject_id);
$subject_query->execute();
$subject = $subject_query->get_result()->fetch_assoc();

// Handle search functionality
$search_term = '';
if (isset($_POST['search_term'])) {
    $search_term = $_POST['search_term'];
}

// Get teachers with optional search
$teachers_query = $conn->prepare("SELECT id, name FROM Faculty WHERE name LIKE ? ORDER BY name ASC");
$search_term = "%" . $search_term . "%"; // Add wildcard characters for LIKE query
$teachers_query->bind_param("s", $search_term);
$teachers_query->execute();
$teachers = $teachers_query->get_result();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['teacher_ids'])) {
    $selected_teachers = $_POST['teacher_ids'];
    $student_type = $_POST['student_type'];

    foreach ($selected_teachers as $teacher_id) {
        // Prevent duplicate assignment
        $check = $conn->prepare("SELECT * FROM assigned_subjects WHERE subject_id = ? AND teacher_id = ? AND student_type = ?");
        $check->bind_param("iis", $subject_id, $teacher_id, $student_type);
        $check->execute();
        if ($check->get_result()->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO assigned_subjects (subject_id, teacher_id, student_type) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $subject_id, $teacher_id, $student_type);
            $stmt->execute();
        }
    }

    echo "<script>alert('Teachers assigned successfully!'); window.location.href='subject_management.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Teachers</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/subject.css">
</head>
<body>
<!-- Include Sidebar -->
<?php include('sidebar.php'); ?>

<div class="subject-manager" style="margin-left: 420px;">
    <h2>Assign Teachers to: <?= htmlspecialchars($subject['subject_name']) ?></h2>

    <!-- Search Teachers -->
    <form method="POST">
        <label>Search Teachers by Name:</label>
        <input type="text" name="search_term" value="<?= htmlspecialchars($search_term) ?>" placeholder="Search..." style="width: 200px;">
        <input type="submit" value="Search">
    </form>

    <form method="POST">
        <label>Select Teachers:</label><br>
        <?php while ($row = $teachers->fetch_assoc()): ?>
            <input type="checkbox" name="teacher_ids[]" value="<?= $row['id'] ?>"> <?= htmlspecialchars($row['name']) ?><br>
        <?php endwhile; ?>

        <label>Select Student Type:</label>
        <select name="student_type" required>
            <option value="">-- Select Type --</option>
            <?php
            if ($subject['student_type'] === 'Both') {
                echo "<option value='Regular'>Regular</option>";
                echo "<option value='STI'>STI</option>";
            } else {
                echo "<option value='{$subject['student_type']}'>{$subject['student_type']}</option>";
            }
            ?>
        </select>

        <br><br>
        <input type="submit" value="Assign Selected Teachers">
    </form>
</div>

<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>
