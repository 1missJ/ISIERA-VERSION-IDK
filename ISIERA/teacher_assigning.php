<?php
include 'db_connection.php';

// Fetch all teachers
$teachers = $conn->query("SELECT * FROM faculty ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

// Fetch all sections
$sections = $conn->query("SELECT * FROM sections ORDER BY grade_level ASC, section_name ASC")->fetch_all(MYSQLI_ASSOC);

// Fetch all subjects
$subjects = $conn->query("SELECT * FROM subjects ORDER BY subject_name ASC")->fetch_all(MYSQLI_ASSOC);

// Assign subject to teacher
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_subject'])) {
    $teacherId = $_POST['teacher_id'];
    $subjectId = $_POST['subject_id'];
    $sectionId = $_POST['section_id'];

    if ($teacherId && $subjectId && $sectionId) {
        $stmt = $conn->prepare("INSERT IGNORE INTO teacher_subjects (teacher_id, subject_id, section_id) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $teacherId, $subjectId, $sectionId);
        $stmt->execute();
        $success = "Subject assigned successfully!";
    } else {
        $error = "Please fill in all fields.";
    }
}

// Assign section adviser
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_adviser'])) {
    $teacherId = $_POST['adviser_teacher_id'];
    $sectionId = $_POST['adviser_section_id'];

    if ($teacherId && $sectionId) {
        $stmt = $conn->prepare("INSERT INTO section_advisers (teacher_id, section_id) VALUES (?, ?) 
                                ON DUPLICATE KEY UPDATE teacher_id = VALUES(teacher_id)");
        $stmt->bind_param("ii", $teacherId, $sectionId);
        $stmt->execute();
        $success = "Adviser assigned successfully!";
    } else {
        $error = "Please select both teacher and section.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Assign Teachers</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <style>
    body {
      background: #f9f9f9;
      font-family: 'Segoe UI', sans-serif;
    }
    .main-content {
      margin-left: 300px;
      padding: 30px;
    }
    .dropdown-nav {
      background-color: #fff;
      border-radius: 12px;
      padding: 15px 20px;
      margin-bottom: 20px;
      box-shadow: 0 0 8px rgba(0,0,0,0.05);
    }
    .dropdown-nav select {
      padding: 8px 12px;
      border-radius: 6px;
    }
    button.add-btn {
      background-color: #2a2185;
      color: white;
      padding: 10px 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
      margin-bottom: 15px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      box-shadow: 0 0 5px rgba(0,0,0,0.05);
      margin-bottom: 30px;
    }
    table thead {
      background-color: #d0e8ff;
    }
    th, td {
      padding: 10px;
      border: 1px solid #ccc;
      text-align: left;
      font-size: 14px;
    }
    .modal-overlay {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      align-items: center;
      justify-content: center;
      z-index: 1000;
    }
    .modal-box {
      background: #fff;
      padding: 25px;
      max-width: 500px;
      width: 100%;
      border-radius: 10px;
      position: relative;
    }
    .modal-box h3 {
      color: #2a2185;
      margin-bottom: 15px;
    }
    .modal-box label {
      font-weight: 500;
      margin-bottom: 5px;
      display: block;
    }
    .modal-box select {
      width: 100%;
      padding: 8px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    .modal-box .close-btn {
      position: absolute;
      top: 10px;
      right: 15px;
      background: none;
      border: none;
      font-size: 18px;
      cursor: pointer;
    }
    .modal-box button {
      background-color: #2a2185;
      color: #fff;
      padding: 10px;
      width: 100%;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .alert {
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 6px;
    }
    .alert-success { background-color: #d4edda; color: #155724; }
    .alert-danger { background-color: #f8d7da; color: #721c24; }
  </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">

  <div class="dropdown-nav">
    <label>Navigate to:</label>
    <select onchange="navigate(this.value)">
      <option value="">-- Select an option --</option>
      <option value="section_management.php">Section Management</option>
      <option value="assign_subjects_grade.php">Curriculum | Subjects - Grade Level</option>
      <option value="teacher_assigning.php">Subject Teacher | Class adviser</option>
    </select>
  </div>

  <?php if (isset($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php elseif (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <button class="add-btn" onclick="openModal('subjectModal')">+ Assign Subject to Teacher</button>
  <button class="add-btn" onclick="openModal('adviserModal')">+ Assign Adviser to Section</button>
  
  <h2>Subject Teacher Assignments</h2>
  <table>
    <thead>
      <tr><th>Teacher</th><th>Subject</th><th>Section</th><th>Grade Level</th></tr>
    </thead>
    <tbody>
      <?php
      $assignments = $conn->query("SELECT f.name AS teacher_name, s.subject_name, sec.section_name, sec.grade_level FROM teacher_subjects ts JOIN faculty f ON ts.teacher_id = f.id JOIN subjects s ON ts.subject_id = s.id JOIN sections sec ON ts.section_id = sec.id ORDER BY sec.grade_level ASC, sec.section_name ASC");
      if ($assignments->num_rows > 0):
        while ($row = $assignments->fetch_assoc()):
      ?>
      <tr>
        <td><?= htmlspecialchars($row['teacher_name']) ?></td>
        <td><?= htmlspecialchars($row['subject_name']) ?></td>
        <td><?= htmlspecialchars($row['section_name']) ?></td>
        <td><?= htmlspecialchars($row['grade_level']) ?></td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="4" style="text-align:center;">No subject-teacher assignments found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <h2>Class Advisers</h2>
  <table>
    <thead>
      <tr><th>Teacher</th><th>Section</th><th>Grade Level</th></tr>
    </thead>
    <tbody>
      <?php
      $advisers = $conn->query("SELECT f.name AS teacher_name, sec.section_name, sec.grade_level FROM section_advisers sa JOIN faculty f ON sa.teacher_id = f.id JOIN sections sec ON sa.section_id = sec.id ORDER BY sec.grade_level ASC, sec.section_name ASC");
      if ($advisers->num_rows > 0):
        while ($row = $advisers->fetch_assoc()):
      ?>
      <tr>
        <td><?= htmlspecialchars($row['teacher_name']) ?></td>
        <td><?= htmlspecialchars($row['section_name']) ?></td>
        <td><?= htmlspecialchars($row['grade_level']) ?></td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="3" style="text-align:center;">No adviser assignments found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Assign Subject Modal -->
<div class="modal-overlay" id="subjectModal">
  <form method="POST" class="modal-box">
    <button type="button" class="close-btn" onclick="closeModal('subjectModal')">×</button>
    <h3>Assign Subject to Teacher</h3>

    <label>Teacher</label>
    <select name="teacher_id" required>
      <option value="">-- Select Teacher --</option>
      <?php foreach ($teachers as $t): ?>
        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
      <?php endforeach; ?>
    </select>

    <label>Subject</label>
    <select name="subject_id" required>
      <option value="">-- Select Subject --</option>
      <?php foreach ($subjects as $s): ?>
        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['subject_name']) ?></option>
      <?php endforeach; ?>
    </select>

    <label>Section</label>
    <select name="section_id" onchange="updateGradeLevel(this)" required>
      <option value="">-- Select Section --</option>
      <?php foreach ($sections as $sec): ?>
        <option value="<?= $sec['id'] ?>" data-grade="<?= $sec['grade_level'] ?>"><?= htmlspecialchars($sec['section_name']) ?></option>
      <?php endforeach; ?>
    </select>

    <div id="gradeLevelDisplay" style="margin-bottom: 10px; color:#555; font-weight: 500;"></div>

    <button type="submit" name="assign_subject">Assign</button>
  </form>
</div>

<!-- Assign Adviser Modal -->
<div class="modal-overlay" id="adviserModal">
  <form method="POST" class="modal-box">
    <button type="button" class="close-btn" onclick="closeModal('adviserModal')">×</button>
    <h3>Assign Adviser to Section</h3>

    <label>Teacher</label>
    <select name="adviser_teacher_id" required>
      <option value="">-- Select Teacher --</option>
      <?php foreach ($teachers as $t): ?>
        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
      <?php endforeach; ?>
    </select>

    <label>Section</label>
    <select name="adviser_section_id" required>
      <option value="">-- Select Section --</option>
      <?php foreach ($sections as $sec): ?>
        <option value="<?= $sec['id'] ?>"><?= htmlspecialchars($sec['section_name']) ?> (Grade <?= $sec['grade_level'] ?>)</option>
      <?php endforeach; ?>
    </select>

    <button type="submit" name="assign_adviser">Assign Adviser</button>
  </form>
</div>


<script>
function openModal(id) {
  document.getElementById(id).style.display = 'flex';
}
function closeModal(id) {
  document.getElementById(id).style.display = 'none';
}
window.onclick = function(event) {
  ['subjectModal', 'adviserModal'].forEach(id => {
    const modal = document.getElementById(id);
    if (event.target === modal) closeModal(id);
  });
}
function updateGradeLevel(select) {
  const selected = select.options[select.selectedIndex];
  const grade = selected.dataset.grade;
  document.getElementById('gradeLevelDisplay').innerText = grade ? `Grade Level: ${grade}` : '';
}
</script>


<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script>
function navigate(path) {
  if (path) {
    window.location.href = path;
  }
}
</script>
</body>
</html>
