<?php
include 'db_connection.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_subjects'])) {
    $gradeLevel = $_POST['grade_level'] ?? '';
    $subjectIds = $_POST['subject_ids'] ?? [];

    if (!empty($gradeLevel) && !empty($subjectIds)) {
        foreach ($subjectIds as $subjectId) {
            $stmt = $conn->prepare("INSERT IGNORE INTO subject_grade_level (subject_id, grade_level) VALUES (?, ?)");
            $stmt->bind_param("is", $subjectId, $gradeLevel);
            $stmt->execute();
        }
        $success = "Subjects successfully assigned to Grade $gradeLevel.";
    } else {
        $error = "Please select a grade level and at least one subject.";
    }
}

$result = $conn->query("SELECT * FROM subjects ORDER BY subject_name ASC");
$subjects = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Assign Subjects to Grade</title>
  <style>
    body { padding: 20px; font-family: sans-serif; background: #f0f2f5; }
    .card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 8px rgba(0,0,0,0.1); max-width: 600px; margin: auto; }
    select, input[type=checkbox] { margin-bottom: 10px; }
    .alert { padding: 10px; border-radius: 6px; margin-bottom: 10px; }
    .success { background: #d4edda; color: #155724; }
    .error { background: #f8d7da; color: #721c24; }
  </style>
</head>
<body>
  <div class="card">
    <h3>Assign Subjects to Grade Level</h3>

    <?php if (isset($success)): ?>
      <div class="alert success"><?= $success ?></div>
    <?php elseif (isset($error)): ?>
      <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>Grade Level</label>
      <select name="grade_level" required>
        <option value="">-- Select Grade --</option>
        <?php foreach (range(7, 12) as $grade): ?>
          <option value="<?= $grade ?>">Grade <?= $grade ?></option>
        <?php endforeach; ?>
      </select>

      <label>Select Subjects</label><br>
      <?php foreach ($subjects as $subject): ?>
        <input type="checkbox" name="subject_ids[]" value="<?= $subject['id'] ?>" />
        <?= htmlspecialchars($subject['subject_name']) ?> (<?= $subject['student_type'] ?>)<br>
      <?php endforeach; ?>

      <br><button type="submit" name="assign_subjects">Assign Subjects</button>
    </form>
  </div>
</body>
</html>
