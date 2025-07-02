<?php
include('db_connection.php');

if (!isset($_GET['grade_level']) || !isset($_GET['section_id'])) {
    echo "<p style='color:red;'>Missing parameters.</p>";
    exit();
}

$gradeLevel = intval($_GET['grade_level']);
$sectionId = intval($_GET['section_id']);

try {
    // First get strand_id from section if SHS
    $strandId = null;
    if ($gradeLevel >= 11 && $gradeLevel <= 12) {
        $strandQuery = "SELECT strand_id FROM sections WHERE id = ?";
        $strandStmt = $conn->prepare($strandQuery);
        $strandStmt->bind_param("i", $sectionId);
        $strandStmt->execute();
        $strandResult = $strandStmt->get_result();
        
        if ($strandResult->num_rows === 0) {
            echo "<p style='color:red;'>Invalid section selected.</p>";
            exit();
        }
        
        $strandRow = $strandResult->fetch_assoc();
        $strandId = $strandRow['strand_id'];
        
        if (!$strandId) {
            echo "<p style='color:red;'>Selected SHS section has no strand assigned.</p>";
            exit();
        }
    }

    if ($gradeLevel >= 7 && $gradeLevel <= 10) {
        // JHS subjects - use your working query
        $query = "
            SELECT s.id, s.subject_name
            FROM subject_grade_strand_assignments a
            JOIN subjects s ON s.id = a.subject_id
            WHERE a.grade_level = ? AND a.strand_id IS NULL
            ORDER BY s.subject_name
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $gradeLevel);
    } else {
        // SHS subjects - use strand_id from section
        $query = "
            SELECT s.id, s.subject_name
            FROM subject_grade_strand_assignments a
            JOIN subjects s ON s.id = a.subject_id
            WHERE a.grade_level = ? AND a.strand_id = ?
            ORDER BY s.subject_name
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $gradeLevel, $strandId);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p style='color:red;'>No subjects assigned for Grade $gradeLevel" . 
             ($gradeLevel >= 11 ? " (Strand ID $strandId)" : "") . ".</p>";
        exit();
    }

    while ($row = $result->fetch_assoc()) {
        echo '<label><input type="checkbox" name="subjects[]" value="' . 
             $row['id'] . '"> ' . 
             htmlspecialchars($row['subject_name']) . '</label><br>';
    }

} catch (Exception $e) {
    echo "<p style='color:red;'>Database error: " . $e->getMessage() . "</p>";
}
?>