<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_teacher'])) {
    $teacherId = $_POST['teacher_id'] ?? '';
    $subjectId = $_POST['subject_id'] ?? '';
    $section = $_POST['section'] ?? '';

    if (!empty($teacherId) && !empty($subjectId) && !empty($section)) {
        $stmt = $conn->prepare("INSERT IGNORE INTO assign (teacher_id, subject_id, section) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $teacherId, $subjectId, $section);
        $stmt->execute();
        $success = "Teacher assigned successfully.";
    } else {
        $error = "Please complete all fields.";
    }
}

$teachers = $conn->query("SELECT id, teacher_name FROM teachers")->fetch_all(MYSQLI_ASSOC);
$subjects = $conn->query("SELECT id, subject_name FROM subjects")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Assign Teachers</title>
  <style>
    body { padding: 20px; font-family: sans-serif; background: #f0f2f5; }
    .card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 8px rgba(0,0,0,0.1); max-width: 600px; margin: auto; }
    .alert { padding: 10px; border-radius: 6px; margin-bottom: 10px; }
    .success { background: #d4edda; color: #155724; }
    .error { background: #f8d7da; color: #721c24; }
  </style>
</head>
<body>
  <div class="card">
    <h3>Assign Teacher to Subject & Section</h3>

    <?php if (isset($success)): ?>
      <div class="alert success"><?= $success ?></div>
    <?php elseif (isset($error)): ?>
      <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>Teacher</label>
      <select name="teacher_id" required>
        <option value="">-- Select Teacher --</option>
        <?php foreach ($teachers as $teacher): ?>
          <option value="<?= $teacher['id'] ?>"><?= htmlspecialchars($teacher['teacher_name']) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Subject</label>
      <select name="subject_id" required>
        <option value="">-- Select Subject --</option>
        <?php foreach ($subjects as $subject): ?>
          <option value="<?= $subject['id'] ?>"><?= htmlspecialchars($subject['subject_name']) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Section</label>
      <input type="text" name="section" placeholder="e.g. 7-Newton" required />

      <br><br><button type="submit" name="assign_teacher">Assign</button>
    </form>
  </div>
</body>
</html>
