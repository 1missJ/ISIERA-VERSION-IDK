<?php
include('db_connection.php');

if (!isset($_GET['grade_level'])) {
    echo "<p style='color:red;'>Missing grade_level.</p>";
    exit();
}

$gradeLevel = intval($_GET['grade_level']);
$strandId = isset($_GET['strand_id']) ? intval($_GET['strand_id']) : null;

if ($gradeLevel >= 7 && $gradeLevel <= 10) {
    // JHS: no strand needed
    $query = "
        SELECT s.id, s.subject_name
        FROM subject_grade_strand_assignments a
        JOIN subjects s ON s.id = a.subject_id
        WHERE a.grade_level = ? AND a.strand_id IS NULL
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $gradeLevel);
} elseif ($gradeLevel >= 11 && $gradeLevel <= 12) {
    // SHS: strand required
    if (!$strandId) {
        echo "<p style='color:red;'>Strand ID missing for SHS.</p>";
        exit();
    }
    $query = "
        SELECT s.id, s.subject_name
        FROM subject_grade_strand_assignments a
        JOIN subjects s ON s.id = a.subject_id
        WHERE a.grade_level = ? AND a.strand_id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $gradeLevel, $strandId);
} else {
    echo "<p style='color:red;'>Invalid grade level.</p>";
    exit();
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p style='color:red;'>No subjects assigned for Grade $gradeLevel" . ($strandId ? " (Strand ID $strandId)" : "") . ".</p>";
    exit();
}

while ($row = $result->fetch_assoc()) {
    echo '<label><input type="checkbox" name="subjects[]" value="' . $row['id'] . '"> ' . htmlspecialchars($row['subject_name']) . '</label><br>';
}
?>
