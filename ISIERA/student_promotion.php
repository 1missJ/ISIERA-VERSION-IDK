<?php
include 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Student Promotion</title>
  <link rel="stylesheet" href="assets/css/style.css" />  
  <link rel="stylesheet" href="assets/css/section.css" />  
</head>
<body>
<?php include('sidebar.php'); ?>

<div class="main-content">

<div class="dropdown-nav">
  <label for="gradeSelect">Navigate to:</label>
  <select id="gradeSelect" onchange="showStudents(this.value)">
    <option value="">-- Select Grade Level --</option>
    <option value="Grade 7">Grade 7</option>
    <option value="Grade 8">Grade 8</option>
    <option value="Grade 9">Grade 9</option>
    <option value="Grade 10">Grade 10</option>
    <option value="Grade 11">Grade 11</option>
    <option value="Grade 12">Grade 12</option>
  </select>
</div>

<h2 id="promotionHeading" style="display: none;">Student Promotion</h2>

  <!-- Search Bar -->
  <div class="search-container" id="searchContainer" style="display:none;">
    <div class="left-search">
      <form id="searchForm" onsubmit="return handleSearch(event)">
        <input type="text" id="searchInput" placeholder="Search by Section..." />
        <button type="submit">Search</button>
      </form>
    </div>
  </div>     

  <!-- Student Table -->
  <table class="student-table" id="studentTable" style="display:none;">
    <thead>
      <tr>
        <th>Section</th>
        <th>No. of Students</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody id="studentTableBody">
    <?php
$grades = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
foreach ($grades as $grade_level) {
        $sql = "SELECT section, COUNT(*) as total_students 
                FROM students 
                WHERE grade_level = ? 
                GROUP BY section
                ORDER BY section ASC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $grade_level);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
          $section = ucfirst(strtolower(htmlspecialchars($row['section'])));
          $count = $row['total_students'];

          echo "<tr data-grade='" . strtolower($grade_level) . "' style='display: none;'>
                  <td>{$section}</td>
                  <td>{$count}</td>
                  <td>
                    <button class='view' onclick=\"location.href='promote_students.php?section=" 
                      . urlencode($section) . "&grade_level=" 
                      . urlencode($grade_level) . "'\"><ion-icon name='eye-outline'></ion-icon></button>
                    <button class='btn-promote' onclick='openSectionModal(" 
                      . json_encode($grade_level) . ", " 
                      . json_encode($section) . ")'>Promote</button>
                  </td>
                </tr>";
        }
        $stmt->close();
      }
      ?>
    </tbody>
  </table>

  <!-- Section Modal -->
  <div class="section-modal" id="sectionModal" style="display:none;">
    <div class="section-modal-content">
      <span class="section-close" onclick="closeSectionModal()">&times;</span>
      <h3>Enter New Section</h3>
      <form id="sectionForm" method="POST" action="student_promote.php">
        <input type="text" id="sectionInput" name="new_section" required />
        <input type="hidden" name="current_grade" id="currentGradeInput" />
        <input type="hidden" name="current_section" id="currentSectionInput" />
        <input type="hidden" name="redirect_url" id="redirectUrlInput" />
        <button type="submit" id="sectionForm button">Confirm</button>
      </form>
    </div>
  </div>

  <!-- Scripts -->
  <script>
    let currentGrade = "";

    function searchStudent() {
      const input = document.getElementById("searchInput").value.toUpperCase();
      const rows = document.querySelectorAll("#studentTableBody tr");
      let hasMatch = false;

      rows.forEach(row => {
        if (row.id === "noDataRow") {
          row.remove();
          return;
        }

        const grade = row.getAttribute("data-grade");

        if (grade === currentGrade) {
          const section = row.querySelector("td")?.textContent.toUpperCase() || "";
          const matched = section.includes(input);
          row.style.display = matched ? "" : "none";
          if (matched) hasMatch = true;
        } else {
          row.style.display = "none";
        }
      });

      const existing = document.getElementById("noDataRow");
      if (existing) existing.remove();

      if (!hasMatch) {
        const tbody = document.getElementById("studentTableBody");
        const noRow = document.createElement("tr");
        noRow.id = "noDataRow";
        noRow.innerHTML = "<td colspan='3'>No matching results.</td>";
        tbody.appendChild(noRow);
      }
    }

function showStudents(yearLevel) {
  currentGrade = yearLevel.trim().toLowerCase();

  document.querySelector(".dropdown-nav").style.display = "block";
  document.getElementById("promotionHeading").style.display = "block";
  document.getElementById("searchContainer").style.display = "flex";
  document.getElementById("studentTable").style.display = "table";

  const tbody = document.getElementById("studentTableBody");
  const rows = tbody.querySelectorAll("tr");
  let found = false;

  // Remove old "no data" row if any
  const oldNoRow = document.getElementById("noDataRow");
  if (oldNoRow) oldNoRow.remove();

  rows.forEach(row => {
    const grade = row.getAttribute("data-grade");
    if (!grade) return; // Skip invalid rows

    if (grade === currentGrade) {
      row.style.display = "";
      found = true;
    } else {
      row.style.display = "none";
    }
  });

  if (!found) {
    const noRow = document.createElement("tr");
    noRow.id = "noDataRow";
    noRow.innerHTML = "<td colspan='3'>No data available.</td>";
    tbody.appendChild(noRow);
  }

  // Reset search filter
  document.getElementById("searchInput").value = "";
}

    document.addEventListener("DOMContentLoaded", () => {
      document.getElementById("searchInput").addEventListener("input", searchStudent);

      const sectionModal = document.getElementById('sectionModal');
      const sectionInput = document.getElementById('sectionInput');
      const currentGradeInput = document.getElementById('currentGradeInput');
      const currentSectionInput = document.getElementById('currentSectionInput');

      window.openSectionModal = function(gradeLevel, section) {
        currentGradeInput.value = gradeLevel;
        currentSectionInput.value = section;
        sectionInput.value = '';
        sectionModal.style.display = 'flex';
        sectionInput.focus();

        const redirectUrl = `student_promotion.php?grade=${encodeURIComponent(gradeLevel)}`;
        document.getElementById("redirectUrlInput").value = redirectUrl;
      };

      window.closeSectionModal = function() {
        sectionModal.style.display = 'none';
      };

      window.onclick = function(event) {
        if (event.target == sectionModal) {
          closeSectionModal();
        }
      };

      // Show students directly if redirected from promote
      const urlParams = new URLSearchParams(window.location.search);
      const grade = urlParams.get('grade');
      if (grade) {
        showStudents(grade);
      }
    });
  </script>

  <!-- Ionicons -->
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</div>

</body>
</html>
