<?php
include 'db_connection.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subject'])) {
    $studentType = $_POST['student_type'] ?? '';
    $subjectNamesRaw = $_POST['subject_names'] ?? '';
    $subjects = array_filter(array_map('trim', explode(',', $subjectNamesRaw)));

    if (!empty($studentType) && in_array($studentType, ['JHS', 'SHS'])) {
        $stmt = $conn->prepare("INSERT IGNORE INTO subjects (subject_name, student_type) VALUES (?, ?)");
        foreach ($subjects as $subject) {
            if (!empty($subject)) {
                $stmt->bind_param("ss", $subject, $studentType);
                $stmt->execute();
            }
        }
        header("Location: " . $_SERVER['PHP_SELF']); // Prevent resubmission on refresh
        exit();
    } else {
        $error = "Please enter valid subject names and select a type.";
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    $conn->query("DELETE FROM subjects WHERE id = $deleteId");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$editSubject = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM subjects WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    $editSubject = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_subject'])) {
    $subjectId = intval($_POST['subject_id']);
    $updatedName = trim($_POST['updated_subject_name']);

    if (!empty($updatedName)) {
        $stmt = $conn->prepare("UPDATE subjects SET subject_name = ? WHERE id = ?");
        $stmt->bind_param("si", $updatedName, $subjectId);
        $stmt->execute();
        header("Location: subject_management.php"); // Redirect to avoid resubmission
        exit;
    } else {
        $error = "Subject name cannot be empty.";
    }
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Subject Management</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <style>
    body {
      background-color: #f0f2f5;
    }

    .main-content {
      margin-left: 300px;
      padding: 30px;
    }

    .card {
      background-color: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.08);
      margin-bottom: 30px;
      max-width: 600px;
    }

    h3 {
      margin-bottom: 15px;
      color: #2a2185;
    }

    label {
      font-weight: 500;
      display: block;
      margin-bottom: 6px;
    }

    input[type="text"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      margin-bottom: 15px;
    }

    button {
      background-color: #2a2185;
      color: #fff;
      border: none;
      padding: 10px 18px;
      border-radius: 8px;
      cursor: pointer;
      width: 100%;
    }

    .alert {
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 6px;
    }

    .alert-success {
      background-color: #d4edda;
      color: #155724;
    }

    .alert-danger {
      background-color: #f8d7da;
      color: #721c24;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      border-radius: 10px;
      overflow: hidden;
    }

    th, td {
      padding: 12px 16px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    th {
      background-color: #2a2185;
      color: #fff;
    }
    /* Dropdown navigation */
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

    .subject-cell {
  font-size: 13px;
  vertical-align: middle;
  line-height: 1.5;
  padding: 8px 12px;
}

.subject-name {
  display: inline-block;
  width: 150px; /* fixed width for consistent alignment */
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.action-icon {
  margin-left: 8px;
  text-decoration: none;
  font-size: 15px;
}

  </style>
</head>
<body>

<?php include('sidebar.php'); ?>
<div class="main-content">

  <div class="dropdown-nav">
    <label>Navigate to:</label>
    <select onchange="navigate(this.value)">
      <option value="">-- Select an option --</option>
      <option value="subject_management.php">Subject List</option>
      <option value="assign_subjects_grade.php">Subjects - Grade Level</option>
      <option value="assign_teacher_subjects.php">Teacher - Subject - Section</option>
    </select>
  </div>

<div class="main-content">
  <?php if (isset($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php elseif (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

<div class="card">
  <h3>Add Subjects</h3>
  <form method="POST">
    <label>Subject Names (separated by comma)</label>
    <input type="text" name="subject_names" placeholder="e.g. Math, Science, Filipino" required />

    <label>Student Type</label>
    <select name="student_type" required>
      <option value="">-- Select Type --</option>
      <option value="JHS">JHS</option>
      <option value="SHS">SHS</option>
    </select>

    <button type="submit" name="add_subject">Add Subjects</button>
  </form>
</div>


<?php if ($editSubject): ?>
  <div class="card">
    <h3>Edit Subject</h3>
    <form method="POST">
      <input type="hidden" name="subject_id" value="<?= $editSubject['id'] ?>" />
      <label>New Subject Name</label>
      <input type="text" name="updated_subject_name" value="<?= htmlspecialchars($editSubject['subject_name']) ?>" required />
      <button type="submit" name="update_subject">Update</button>
    </form>
  </div>
<?php endif; ?>

<!-- Subject List Table -->
<div class="card">
  <h3>Subject List</h3>
  <table>
    <thead>
      <tr>
        <th>JHS Subjects</th>
        <th>SHS Subjects</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $jhsSubjects = $conn->query("SELECT * FROM subjects WHERE student_type = 'JHS' ORDER BY created_at DESC");
      $shsSubjects = $conn->query("SELECT * FROM subjects WHERE student_type = 'SHS' ORDER BY created_at DESC");

      $jhs = [];
      while ($row = $jhsSubjects->fetch_assoc()) $jhs[] = $row;

      $shs = [];
      while ($row = $shsSubjects->fetch_assoc()) $shs[] = $row;

      $maxRows = max(count($jhs), count($shs));

      for ($i = 0; $i < $maxRows; $i++):
        $jhsRow = $jhs[$i] ?? null;
        $shsRow = $shs[$i] ?? null;
      ?>
      <tr>
        <td class="subject-cell">
          <?php if ($jhsRow): ?>
            <span class="subject-name"><?= htmlspecialchars($jhsRow['subject_name']) ?></span>
            <a href="?edit=<?= $jhsRow['id'] ?>" class="action-icon">✏️</a>
            <a href="?delete=<?= $jhsRow['id'] ?>" class="action-icon" onclick="return confirm('Delete this subject?')">🗑️</a>
          <?php endif; ?>
        </td>
        <td class="subject-cell">
          <?php if ($shsRow): ?>
            <span class="subject-name"><?= htmlspecialchars($shsRow['subject_name']) ?></span>
            <a href="?edit=<?= $shsRow['id'] ?>" class="action-icon">✏️</a>
            <a href="?delete=<?= $shsRow['id'] ?>" class="action-icon" onclick="return confirm('Delete this subject?')">🗑️</a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endfor; ?>
    </tbody>
  </table>
</div>



<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
<script>
function navigate(path) {
  if (path) {
    window.location.href = path;
  }
}
</script>

</html>
