<?php
include 'db_connection.php';

// Add Section
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_section'])) {
    $name = trim($_POST['section_name']);
    $type = $_POST['student_type'];
    $gradeNum = $_POST['grade_level'];
    $gradeLevel = "Grade " . $gradeNum;
    $strand_id = ($type === 'SHS' && !empty($_POST['strand_id'])) ? intval($_POST['strand_id']) : null;

    if ($name && $type && $gradeNum) {
        $stmt = $conn->prepare("INSERT INTO sections (section_name, student_type, grade_level, strand_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $type, $gradeLevel, $strand_id);
        $stmt->execute();
        $success = "Section added successfully!";
    } else {
        $error = "Please fill in all fields.";
    }
}

// Fetch all sections with strand name
$sectionsResult = $conn->query("
    SELECT s.*, st.name AS strand_name 
    FROM sections s 
    LEFT JOIN strands st ON s.strand_id = st.id
    ORDER BY s.grade_level ASC, s.section_name ASC
");
$sections = $sectionsResult->fetch_all(MYSQLI_ASSOC);

// Fetch all strands
$strandsResult = $conn->query("SELECT id, name FROM strands ORDER BY name ASC");
$strands = $strandsResult->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Section Management</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <style>
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
} /* ← CLOSE the select block properly first */

    <style>
  body {
    font-family: 'Segoe UI', sans-serif;
    background: #f9f9f9;
    margin: 0;
    padding: 0;
  }

  .main-content {
    margin-left: 300px; /* prevent overlap with sidebar */
    padding: 20px;
  }

  h2 {
    margin-bottom: 20px;
    color: #333;
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
    width: 100%;
    max-width: 400px;
    border-radius: 10px;
    position: relative;
  }

  .modal-box h3 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #2a2185;
  }

  .modal-box label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #333;
  }

  .modal-box input, .modal-box select {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
  }

  .modal-box .close-btn {
    position: absolute;
    top: 10px; right: 15px;
    background: none;
    border: none;
    font-size: 18px;
    color: #555;
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
    font-size: 14px;
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
</style>

  </style>
</head>
<body>

<?php include('sidebar.php'); ?>

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

  <h2>Section Management</h2>

  <?php if (isset($success)) echo "<div class='alert success'>$success</div>"; ?>
  <?php if (isset($error)) echo "<div class='alert error'>$error</div>"; ?>

  <button class="add-btn" onclick="openModal()">+ Add Section</button>

<!-- Section Table -->
<table>
  <thead>
    <tr>
      <th>Section Name</th>
      <th>Grade Level</th>
      <th>Strand</th>
      <th>Student Type</th>
      <th>Created At</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($sections as $section): ?>
      <tr>
        <td><?= htmlspecialchars($section['section_name']) ?></td>
        <td><?= htmlspecialchars($section['grade_level']) ?></td>
        <td><?= $section['student_type'] === 'SHS' ? htmlspecialchars($section['strand_name']) : '-' ?></td>
        <td><?= htmlspecialchars($section['student_type']) ?></td>
        <td><?= htmlspecialchars($section['created_at']) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<!-- Add Section Modal -->
<div class="modal-overlay" id="addSectionModal">
  <form method="POST" class="modal-box">
    <button type="button" class="close-btn" onclick="closeModal()">×</button>
    <h3>Add New Section</h3>
    
    <label>Section Name</label>
    <input type="text" name="section_name" required>

    <label>Student Type</label>
<select name="student_type" required onchange="toggleStrand(this.value)">
  <option value="">-- Select --</option>
  <option value="JHS">JHS</option>
  <option value="SHS">SHS</option>
</select>

<label>Grade Level</label>
<select name="grade_level" required>
  <option value="">-- Select --</option>
  <?php for ($i = 7; $i <= 12; $i++): ?>
    <option value="<?= $i ?>">Grade <?= $i ?></option>
  <?php endfor; ?>
</select>


<div id="strandSelect" style="display: none;">
  <label>Strand</label>
  <select name="strand_id">
    <option value="">-- Select Strand --</option>
    <?php foreach ($strands as $strand): ?>
      <option value="<?= $strand['id'] ?>"><?= htmlspecialchars($strand['name']) ?></option>
    <?php endforeach; ?>
  </select>
</div>

    <button type="submit" name="add_section">Save Section</button>
  </form>
</div>

<script>
  function openModal() {
    document.getElementById('addSectionModal').style.display = 'flex';
  }

  function closeModal() {
    document.getElementById('addSectionModal').style.display = 'none';
  }

  // Optional: close modal on outside click
  window.onclick = function(e) {
    const modal = document.getElementById('addSectionModal');
    if (e.target === modal) closeModal();
  }
</script>

<script>
function toggleStrand(value) {
  const strandDiv = document.getElementById('strandSelect');
  strandDiv.style.display = (value === 'SHS') ? 'block' : 'none';
}

function openModal() {
  document.getElementById('addSectionModal').style.display = 'flex';
}

function closeModal() {
  document.getElementById('addSectionModal').style.display = 'none';
}

// Optional: close modal when clicking outside
window.onclick = function(e) {
  const modal = document.getElementById('addSectionModal');
  if (e.target === modal) closeModal();
}
</script>



<script>
function toggleStudents(sectionId) {
  const list = document.getElementById('students_' + sectionId);
  list.style.display = (list.style.display === 'none' || list.style.display === '') ? 'block' : 'none';
}
</script>

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

</body>
</html>
