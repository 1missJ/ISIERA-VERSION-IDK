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
    SELECT s.id, s.section_name, s.grade_level, s.strand_id
    FROM section_advisers sa
    JOIN sections s ON sa.section_id = s.id
    WHERE sa.teacher_id = ?
");
$sectionStmt->bind_param("i", $teacherId);
$sectionStmt->execute();
$sectionResult = $sectionStmt->get_result();
$sectionRow = $sectionResult->fetch_assoc();

$adviserSection = $sectionRow['section_name'];
$gradeLevel = (int) filter_var($sectionRow['grade_level'], FILTER_SANITIZE_NUMBER_INT);
$strandId = $sectionRow['strand_id'];
$sectionId = $sectionRow['id'];

// Get all subjects for this section
$subjectsStmt = $conn->prepare("
    SELECT s.id, s.subject_name
    FROM subject_grade_strand_assignments a
    JOIN subjects s ON a.subject_id = s.id
    WHERE a.grade_level = ?
    " . ($gradeLevel >= 11 ? "AND a.strand_id = ?" : "AND a.strand_id IS NULL") . "
    ORDER BY s.subject_name
");
if ($gradeLevel >= 11) {
    $subjectsStmt->bind_param("ii", $gradeLevel, $strandId);
} else {
    $subjectsStmt->bind_param("i", $gradeLevel);
}
$subjectsStmt->execute();
$subjectsResult = $subjectsStmt->get_result();
$allSubjects = $subjectsResult->fetch_all(MYSQLI_ASSOC);
$totalSubjects = count($allSubjects);

// Get students with enrollment status
$studentStmt = $conn->prepare("
    SELECT s.lrn, s.first_name, s.middle_name, s.last_name, 
           COUNT(e.subject_id) as enrolled_count
    FROM students s
    LEFT JOIN student_enrollments e ON s.lrn = e.student_lrn
    WHERE s.section = ?
    GROUP BY s.lrn
    ORDER BY s.last_name ASC
");
$studentStmt->bind_param("s", $adviserSection);
$studentStmt->execute();
$studentResult = $studentStmt->get_result();
$students = $studentResult->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Subject Enrollment</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <style>
    /* Improved modal styling */
    .modal {
      display: none;
      position: fixed;
      z-index: 100;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.4);
    }
    
    .modal-content {
      background-color: #fefefe;
      margin: 5% auto;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      width: 85%;
      max-width: 750px;
    }
    
    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }
    
    .close:hover {
      color: black;
    }
    
    /* Consistent subject grid for both modals */
    .subject-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 12px;
      margin: 20px 0;
    }
    
    .subject-item {
      background: #f8f9fa;
      padding: 12px;
      border-radius: 6px;
      border: 1px solid #dee2e6;
      display: flex;
      align-items: center;
    }
    
    .subject-item label {
      display: flex;
      align-items: center;
      gap: 8px;
      width: 100%;
      cursor: pointer;
    }
    
    /* Enrollment status indicators */
    .enrollment-status {
      display: inline-block;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      margin-left: 5px;
    }
    
    .enrolled {
      background-color: #28a745;
    }
    
    .not-enrolled {
      background-color: #dc3545;
    }
    
    .partial-enrolled {
      background-color: #ffc107;
    }
    
    /* Action buttons */
    .action-btn {
      padding: 8px 16px;
      background: #4e73df;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background 0.3s;
    }
    
    .action-btn:hover {
      background: #2e59d9;
    }
    
    /* Search and bulk action containers */
    .search-container {
      margin-bottom: 20px;
    }
    
    .bulk-actions {
      margin: 20px 0;
    }
    
    /* Table styling */
    .student-table {
      width: 100%;
      border-collapse: collapse;
    }
    
    .student-table th, .student-table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    
    .student-table th {
      background-color:rgb(87, 165, 243);
      font-weight: 600;
    }
  </style>
</head>

<body>
<?php include('adviser_sidebar.php'); ?>

<!-- Main Content -->
<div class="main-content">
  <h2>List of Students in Section: <?= htmlspecialchars($adviserSection) ?></h2>

  <!-- Bulk Action -->
  <div class="bulk-actions">
    <button class="action-btn" onclick="openSectionEnrollmentModal()">Enroll Section to Subjects</button>
  </div>

  <!-- Search Bar -->
  <div class="search-container">
    <form id="searchForm" onsubmit="return false;">
      <input type="text" id="searchInput" placeholder="Search by student name..." />
      <button type="submit" class="action-btn">Search</button>
    </form>
  </div>

  <!-- Student Table -->
  <table class="student-table" id="studentTable">
    <thead>
      <tr>
        <th>LRN</th>
        <th>Name</th>
        <th>Enrollment Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody id="studentTableBody">
      <?php foreach ($students as $row): 
          $fullName = ucfirst(strtolower($row['last_name'])) . ', ' . ucfirst(strtolower($row['first_name']));
          if (!empty($row['middle_name'])) {
              $fullName .= ' ' . strtoupper(substr($row['middle_name'], 0, 1)) . '.';
          }
          
          // Determine enrollment status
          if ($row['enrolled_count'] == 0) {
              $statusClass = 'not-enrolled';
              $statusText = 'Not Enrolled';
          } elseif ($row['enrolled_count'] == $totalSubjects) {
              $statusClass = 'enrolled';
              $statusText = 'Fully Enrolled';
          } else {
              $statusClass = 'partial-enrolled';
              $statusText = 'Partially Enrolled';
          }
      ?>
        <tr>
          <td><?= htmlspecialchars($row['lrn']) ?></td>
          <td><?= htmlspecialchars($fullName) ?></td>
          <td>
            <span class="enrollment-status <?= $statusClass ?>" title="<?= $statusText ?>"></span>
            <?= $statusText ?>
          </td>
          <td>
            <button class="action-btn" onclick="openSubjectModal('<?= $row['lrn'] ?>')">Manage Enrollment</button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Subject Enrollment Modal -->
<div id="subjectModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h3>Manage Enrollment for <span id="modalLRNDisplay"></span></h3>
    <form id="enrollForm">
      <input type="hidden" id="modalLRN" name="lrn" />
      <div id="subjectList" class="subject-grid"></div>
      <div style="margin-top: 20px;">
        <label><input type="checkbox" id="selectAll" onchange="toggleSelectAll()" /> Select All</label>
        <button type="submit" class="action-btn" style="float: right;">Update Enrollment</button>
      </div>
    </form>
  </div>
</div>

<!-- Section Enrollment Modal -->
<div id="sectionModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeSectionModal()">&times;</span>
    <h3>Enroll Section to Subjects</h3>
    <form id="sectionEnrollForm">
      <div class="subject-grid">
        <?php foreach ($allSubjects as $subject): ?>
          <div class="subject-item">
            <label>
              <input type="checkbox" name="sectionSubjects[]" value="<?= $subject['id'] ?>">
              <?= htmlspecialchars($subject['subject_name']) ?>
            </label>
          </div>
        <?php endforeach; ?>
      </div>
      <div style="margin-top: 20px;">
        <label><input type="checkbox" id="selectAllSection" onchange="toggleSelectAllSection()" /> Select All</label>
        <button type="submit" class="action-btn" style="float: right;">Enroll Section</button>
      </div>
    </form>
  </div>
</div>

<script>
// Global variables
const gradeLevel = <?= $gradeLevel ?>;
const sectionId = <?= $sectionId ?>;
const totalSubjects = <?= $totalSubjects ?>;

// Modal functions
function openSubjectModal(lrn) {
  document.getElementById("modalLRN").value = lrn;
  document.getElementById("modalLRNDisplay").textContent = lrn;
  document.getElementById("subjectList").innerHTML = "Loading subjects...";
  
  fetch(`adviser_fetch_subjects.php?grade_level=${gradeLevel}&section_id=${sectionId}&lrn=${lrn}`)
    .then(response => response.text())
    .then(data => {
      document.getElementById("subjectList").innerHTML = `
        <div class="subject-grid">${data}</div>
      `;
      // Update select all checkbox based on checked status
      const checkboxes = document.querySelectorAll('#subjectList input[type="checkbox"]');
      const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
      document.getElementById("selectAll").checked = checkedCount === checkboxes.length;
    });

  document.getElementById("subjectModal").style.display = "block";
}

function openSectionEnrollmentModal() {
  document.getElementById("sectionModal").style.display = "block";
}

function closeModal() {
  document.getElementById("subjectModal").style.display = "none";
}

function closeSectionModal() {
  document.getElementById("sectionModal").style.display = "none";
}

// Form submissions
document.getElementById("enrollForm").addEventListener("submit", function(e) {
  e.preventDefault();
  const selectedSubjects = Array.from(document.querySelectorAll('#subjectList input[type="checkbox"]:checked'))
    .map(checkbox => checkbox.value);

  if (selectedSubjects.length === 0) {
    alert("Please select at least one subject");
    return;
  }

  fetch("process_enrollment.php", {
    method: "POST",
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      lrn: document.getElementById("modalLRN").value,
      subjects: selectedSubjects,
      section_id: sectionId
    })
  }).then(response => response.text())
    .then(result => {
      alert(result);
      closeModal();
      location.reload(); // Refresh to update status indicators
    });
});

document.getElementById("sectionEnrollForm").addEventListener("submit", function(e) {
  e.preventDefault();
  const selectedSubjects = Array.from(document.querySelectorAll('#sectionModal input[type="checkbox"]:checked'))
    .map(checkbox => checkbox.value);

  if (selectedSubjects.length === 0) {
    alert("Please select at least one subject");
    return;
  }

  if (confirm("Enroll ALL students in this section to the selected subjects?")) {
    fetch("process_bulk_enrollment.php", {
      method: "POST",
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        section_id: sectionId,
        subject_ids: selectedSubjects
      })
    }).then(response => response.text())
      .then(result => {
        alert(result);
        closeSectionModal();
        location.reload(); // Refresh to update status indicators
      });
  }
});

// Helper functions
function toggleSelectAll() {
  const checkboxes = document.querySelectorAll('#subjectList input[type="checkbox"]');
  const selectAll = document.getElementById("selectAll");
  checkboxes.forEach(cb => cb.checked = selectAll.checked);
}

function toggleSelectAllSection() {
  const checkboxes = document.querySelectorAll('#sectionModal input[type="checkbox"]');
  const selectAll = document.getElementById("selectAllSection");
  checkboxes.forEach(cb => cb.checked = selectAll.checked);
}

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

// Initialize
document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("searchInput").addEventListener("input", searchStudent);
});
</script>

<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>