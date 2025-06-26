<?php
include 'db_connection.php';

// Create table if not exists (for development/demo purpose)
$conn->query("CREATE TABLE IF NOT EXISTS sections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  section_name VARCHAR(100) UNIQUE NOT NULL,
  student_type ENUM('JHS','SHS') NOT NULL,
  grade_level INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Add Section
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_section'])) {
    $name = trim($_POST['section_name']);
    $type = $_POST['student_type'];
    $grade = $_POST['grade_level'];

    if ($name && $type && $grade) {
        $stmt = $conn->prepare("INSERT IGNORE INTO sections (section_name, student_type, grade_level) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $name, $type, $grade);
        $stmt->execute();
        $success = "Section added successfully!";
    } else {
        $error = "Please fill in all fields.";
    }
}

// Fetch all sections
$sectionsResult = $conn->query("SELECT * FROM sections ORDER BY grade_level ASC, section_name ASC");
$sections = $sectionsResult->fetch_all(MYSQLI_ASSOC);

// Fetch students by section (assuming table: approved_students_mobile has a section column)
$studentsBySection = [];
foreach ($sections as $sec) {
    $secName = $sec['section_name'];
    $res = $conn->prepare("SELECT * FROM approved_students_mobile WHERE section = ? ORDER BY lastname ASC");
    $res->bind_param("s", $secName);
    $res->execute();
    $studentsBySection[$secName] = $res->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Section Management</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <style>
    .card {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
      margin-bottom: 20px;
      max-width: 700px;
    }
    .alert { padding: 10px; border-radius: 6px; margin-bottom: 15px; }
    .success { background: #d4edda; color: #155724; }
    .error { background: #f8d7da; color: #721c24; }
    .toggle-btn { cursor: pointer; color: #2a2185; font-weight: bold; }
    .student-list { display: none; padding-left: 20px; }
    table { width: 100%; margin-top: 10px; border-collapse: collapse; }
    th, td { padding: 8px; border: 1px solid #ccc; text-align: left; font-size: 14px; }
  </style>
</head>
<body>
<?php include('sidebar.php'); ?>
<div class="main-content">
  <h2>Section Management</h2>

  <?php if (isset($success)) echo "<div class='alert success'>$success</div>"; ?>
  <?php if (isset($error)) echo "<div class='alert error'>$error</div>"; ?>

  <div class="card">
    <h3>Add New Section</h3>
    <form method="POST">
      <label>Section Name</label>
      <input type="text" name="section_name" required style="width: 100%; padding: 8px; margin-bottom: 10px;">

      <label>Student Type</label>
      <select name="student_type" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
        <option value="">-- Select --</option>
        <option value="JHS">JHS</option>
        <option value="SHS">SHS</option>
      </select>

      <label>Grade Level</label>
      <select name="grade_level" required style="width: 100%; padding: 8px;">
        <option value="">-- Select --</option>
        <?php for ($i = 7; $i <= 12; $i++): ?>
          <option value="<?= $i ?>">Grade <?= $i ?></option>
        <?php endfor; ?>
      </select>

      <button type="submit" name="add_section" style="margin-top: 15px; padding: 10px; width: 100%; background-color: #2a2185; color: #fff; border: none; border-radius: 6px;">Add Section</button>
    </form>
  </div>

  <div class="card">
    <h3>Sections & Students</h3>
    <?php foreach ($sections as $sec): ?>
      <div style="margin-bottom: 10px;">
        <div class="toggle-btn" onclick="toggleStudents('<?= $sec['id'] ?>')">
          <?= htmlspecialchars($sec['section_name']) ?> - <?= $sec['student_type'] ?> (Grade <?= $sec['grade_level'] ?>)
        </div>
        <div id="students_<?= $sec['id'] ?>" class="student-list">
          <?php if (!empty($studentsBySection[$sec['section_name']])): ?>
            <table>
              <thead><tr><th>Student ID</th><th>Name</th></tr></thead>
              <tbody>
              <?php foreach ($studentsBySection[$sec['section_name']] as $student): ?>
                <tr>
                  <td><?= htmlspecialchars($student['student_id']) ?></td>
                  <td><?= htmlspecialchars($student['lastname'] . ', ' . $student['firstname']) ?></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p>No students in this section.</p>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
function toggleStudents(sectionId) {
  const list = document.getElementById('students_' + sectionId);
  list.style.display = (list.style.display === 'none' || list.style.display === '') ? 'block' : 'none';
}
</script>
</body>
</html>
