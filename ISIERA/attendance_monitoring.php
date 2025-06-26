<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Monitoring</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<?php include('sidebar.php'); ?>

    <!-- Main Content -->
    <div class="main-content">
        <h2>Attendance Record</h2>

  <!-- Search Bar -->
  <div class="search-container" id="searchContainer" style="display:none;">
    <div class="left-search">
      <form id="searchForm" onsubmit="return handleSearch(event)">
        <input type="text" id="searchInput" placeholder="Search by section..." />
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
                <tr data-grade="Grade 7">
                    <td>Mabini</td>
                    <td>44</td>
                    <td>
                        <button class="views-btn" onclick="location.href='attendance_page.html'"><ion-icon name="eye-outline"></ion-icon></button>
                        <button class="student-btn" onclick="location.href='attendance_students.html'"><ion-icon name="person-outline"></ion-icon></button>
                        <button class="report-btn" onclick="location.href='attendance_report.html'"><ion-icon name="document-outline"></ion-icon></button>
                    </td>
                </tr>
                <tr data-grade="Grade 7">
                    <td>Mangga</td>
                    <td>36</td>
                    <td>
                        <button class="views-btn" onclick="location.href='attendance_page.html'"><ion-icon name="eye-outline"></ion-icon></button>
                        <button class="student-btn" onclick="location.href='attendance_students.html'"><ion-icon name="person-outline"></ion-icon></button>
                        <button class="report-btn" onclick="location.href='attendance_report.html'"><ion-icon name="document-outline"></ion-icon></button>
                    </td>
                </tr>
            </tbody>            
        </table>

        <script>
    function showStudents(yearLevel) {
                document.querySelector(".year-levels").style.display = "none";
                document.getElementById("studentTable").style.display = "table";
                document.getElementById("searchContainer").style.display = "flex";
            }

    function searchStudent() {
      const input = document.getElementById("searchInput").value.toUpperCase();
      const rows = document.querySelectorAll("#studentTableBody tr");

      rows.forEach(row => {
        if (row.style.display !== "none") {
          const sectionCell = row.querySelector("td");
          if (sectionCell) {
            const section = sectionCell.textContent.toUpperCase();
            row.style.display = section.includes(input) ? "" : "none";
          }
        }
      });
    }

    document.addEventListener("DOMContentLoaded", () => {
      document.getElementById("searchInput").addEventListener("input", searchStudent);
    });
        </script>

    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
