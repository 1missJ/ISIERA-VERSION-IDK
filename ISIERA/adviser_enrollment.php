<?php
include('db_connection.php');
session_start();

if (!isset($_SESSION['teacher_id'])) {
    header("Location: index.php");
    exit();
}

$teacherId = $_SESSION['teacher_id'];
$teacherName = $_SESSION['teacher_name'];
// Get adviser's section info
$sectionStmt = $conn->prepare("
    SELECT s.section_name, s.grade_level
    FROM section_advisers sa
    JOIN sections s ON sa.section_id = s.id
    WHERE sa.teacher_id = ?
");

$sectionStmt->bind_param("i", $teacherId);
$sectionStmt->execute();
$sectionResult = $sectionStmt->get_result();
$sectionRow = $sectionResult->fetch_assoc();

$adviserSection = $sectionRow['section_name'];
$gradeLevelRaw = $sectionRow['grade_level'];
$gradeLevel = (int) filter_var($gradeLevelRaw, FILTER_SANITIZE_NUMBER_INT);

$strandId = null;


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Subject Enrollment</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
<?php include('adviser_sidebar.php'); ?>

<!-- Main Content -->
<div class="main-content">
  <h2>List of Students in Section: <?= htmlspecialchars($adviserSection) ?></h2>

  <!-- Search Bar -->
  <div class="search-container" style="display: flex; margin-bottom: 20px;">
    <div class="left-search">
      <form id="searchForm" onsubmit="return false;">
        <input type="text" id="searchInput" placeholder="Search by student name..." />
        <button type="submit">Search</button>
      </form>
    </div>
  </div>

  <!-- Student Table -->
  <table class="student-table" id="studentTable">
    <thead>
      <tr>
        <th>LRN</th>
        <th>Name</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody id="studentTableBody">
      <?php
      $stmt = $conn->prepare("SELECT lrn, first_name, middle_name, last_name FROM students WHERE section = ? ORDER BY last_name ASC");
      $stmt->bind_param("s", $adviserSection);
      $stmt->execute();
      $result = $stmt->get_result();

      while ($row = $result->fetch_assoc()):
          $fullName = ucfirst(strtolower($row['last_name'])) . ', ' . ucfirst(strtolower($row['first_name']));
          if (!empty($row['middle_name'])) {
              $fullName .= ' ' . strtoupper(substr($row['middle_name'], 0, 1)) . '.';
          }
      ?>
        <tr>
          <td><?= htmlspecialchars($row['lrn']) ?></td>
          <td><?= htmlspecialchars($fullName) ?></td>
          <td>
            <button onclick="openSubjectModal('<?= $row['lrn'] ?>')">Enroll Subjects</button>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Subject Enrollment Modal -->
<div id="subjectModal" class="modal" style="display:none;">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h3>Enroll Subjects for <span id="modalLRNDisplay"></span></h3>

    <form id="enrollForm">
      <input type="hidden" id="modalLRN" name="lrn" />
      
      <div id="subjectList">
        <!-- Subjects will be dynamically loaded here -->
      </div>

      <label><input type="checkbox" id="selectAll" /> Select All</label>
      <br><br>

      <button type="submit">Enroll Selected Subjects</button>
    </form>
  </div>
</div>

<script>
function openSubjectModal(lrn) {
  document.getElementById("modalLRN").value = lrn;
  document.getElementById("modalLRNDisplay").textContent = lrn;
  document.getElementById("subjectList").innerHTML = "Loading subjects...";

  // Fetch subject list dynamically based on student
  let url = `adviser_fetch_subjects.php?grade_level=${gradeLevel}`;
if (gradeLevel >= 11 && strandId !== null) {
  url += `&strand_id=${strandId}`;
}

fetch(url)
  .then(response => response.text())
  .then(data => {
    document.getElementById("subjectList").innerHTML = data;
  });


  document.getElementById("subjectModal").style.display = "block";
}

function closeModal() {
  document.getElementById("subjectModal").style.display = "none";
}

document.getElementById("selectAll").addEventListener("change", function() {
  const checkboxes = document.querySelectorAll('#subjectList input[type="checkbox"]');
  checkboxes.forEach(cb => cb.checked = this.checked);
});

document.getElementById("enrollForm").addEventListener("submit", function(e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch("process_enrollment.php", {
    method: "POST",
    body: formData
  }).then(response => response.text())
    .then(result => {
      alert(result);
      closeModal();
    });
});
</script>

<script>
function searchStudent() {
  const input = document.getElementById("searchInput").value.toUpperCase();
  const rows = document.querySelectorAll("#studentTableBody tr");

  rows.forEach(row => {
    const nameCell = row.querySelectorAll("td")[1];
    if (nameCell) {
      const studentName = nameCell.textContent.toUpperCase();
      row.style.display = studentName.includes(input) ? "" : "none";
    }
  });
}

document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("searchInput").addEventListener("input", searchStudent);
});
</script>

<script>
  const gradeLevel = <?= intval($gradeLevel) ?>;
  const strandId = <?= $strandId !== null ? intval($strandId) : 'null' ?>;
</script>


<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>
