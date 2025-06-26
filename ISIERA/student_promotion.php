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
  <h2>Student Promotion</h2>

  <!-- Search Bar -->
  <div class="search-container" id="searchContainer" style="display:none;">
    <div class="left-search">
      <form id="searchForm" onsubmit="return handleSearch(event)">
        <input type="text" id="searchInput" placeholder="Search by Section..." />
        <button type="submit">Search</button>
      </form>
    </div>
  </div>     

  <!-- Grade Level and Student Type Dropdown -->
  <div class="year-levels">
    <?php
    $grades = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'];
    $types = ['Regular Student', 'STI Student'];

    foreach ($grades as $grade) {
        echo "<div class='year-box-wrapper'>
                <div class='year-box'>{$grade}</div>
                <div class='dropdown'>";
        foreach ($types as $type) {
            echo "<div onclick=\"showStudents('{$grade}', '{$type}')\">{$type}</div>";
        }
        echo "</div></div>";
    }
    ?>
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
      foreach ($grades as $grade_level) {
        $sql = "SELECT section, student_type, COUNT(*) as total_students 
                FROM students 
                WHERE grade_level = ? 
                GROUP BY section, student_type
                ORDER BY section ASC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $grade_level);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
          $section = ucfirst(strtolower(htmlspecialchars($row['section'])));
          $type = htmlspecialchars($row['student_type']);
          $count = $row['total_students'];

echo "<tr data-grade='" . strtolower($grade_level) . "' data-type='" . strtolower($type) . "' style='display: none;'>
        <td>{$section}</td>
        <td>{$count}</td>
        <td>
          <button class='view' onclick=\"location.href='promote_students.php?section=" 
            . urlencode($section) . "&grade_level=" 
            . urlencode($grade_level) . "&student_type=" 
            . urlencode($type) . "'\"><ion-icon name='eye-outline'></ion-icon></button>
          <button class='btn-promote' onclick='openSectionModal(" 
            . json_encode($grade_level) . ", " 
            . json_encode($section) . ", " 
            . json_encode($type) . ")'>Promote</button>
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
  <input type="hidden" name="student_type" id="studentTypeInput" />
  <input type="hidden" name="redirect_url" id="redirectUrlInput" />
  <button type="submit" id="sectionForm button">Confirm</button>
</form>
  </div>
</div>

  <!-- Scripts -->
  <script>
    let currentGrade = "";
let currentType = "";

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
    const type = row.getAttribute("data-type");

    if (grade === currentGrade && type === currentType) {
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

    document.addEventListener("DOMContentLoaded", () => {
      document.getElementById("searchInput").addEventListener("input", searchStudent);
    });
    
function showStudents(yearLevel, studentType) {
  document.querySelector(".year-levels").style.display = "none";
  document.getElementById("searchContainer").style.display = "flex";

  currentGrade = yearLevel.trim().toLowerCase();
  currentType = studentType.trim().toLowerCase();

  const table = document.getElementById("studentTable");
  const rows = document.querySelectorAll("#studentTableBody tr");

  let found = false;

  rows.forEach(row => {
    const grade = row.getAttribute("data-grade");
    const type = row.getAttribute("data-type");

    if (grade === currentGrade && type === currentType) {
      row.style.display = "";
      found = true;
    } else {
      row.style.display = "none";
    }
  });

  table.style.display = "table";

  if (!found) {
    document.getElementById("studentTableBody").innerHTML = 
      "<tr id='noDataRow'><td colspan='3'>No data available.</td></tr>";
  }
}

    document.addEventListener("DOMContentLoaded", () => {
  const sectionModal = document.getElementById('sectionModal');
  const sectionInput = document.getElementById('sectionInput');
  const currentGradeInput = document.getElementById('currentGradeInput');
  const currentSectionInput = document.getElementById('currentSectionInput');
  const studentTypeInput = document.getElementById('studentTypeInput');

window.openSectionModal = function(gradeLevel, section, studentType) {
  currentGradeInput.value = gradeLevel;
  currentSectionInput.value = section;
  studentTypeInput.value = studentType;
  sectionInput.value = '';
  sectionModal.style.display = 'flex';
  sectionInput.focus();

  // Save redirect back to current context
  const redirectUrl = `student_promotion.php?grade=${encodeURIComponent(gradeLevel)}&type=${encodeURIComponent(studentType)}`;
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
});

document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const grade = urlParams.get('grade');
  const type = urlParams.get('type');

  if (grade && type) {
    showStudents(grade, type);
  }
});

  </script>

  <!-- Ionicons -->
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</div>

</body>
</html>
