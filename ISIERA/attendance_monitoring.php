<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Attendance Monitoring</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
<?php include('sidebar.php'); ?>

<!-- Main Content -->
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

<h2 id="attendanceHeading" style="display: none;">Attendance Record</h2>

  <!-- Search Bar -->
  <div class="search-container" id="searchContainer" style="display: none;">
    <div class="left-search">
      <form id="searchForm" onsubmit="return handleSearch(event)">
        <input type="text" id="searchInput" placeholder="Search by section..." />
        <button type="submit">Search</button>
      </form>
    </div>
  </div>

  <!-- Student Table -->
  <table class="student-table" id="studentTable" style="display: none;">
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

function showStudents(yearLevel) {
  const currentGrade = yearLevel.trim().toLowerCase();

  // Show dropdown, heading, table and search
  document.querySelector(".dropdown-nav").style.display = "block";
  document.getElementById("attendanceHeading").style.display = "block";
  document.getElementById("studentTable").style.display = "table";
  document.getElementById("searchContainer").style.display = "flex";

  // Filter rows by selected grade level
  const rows = document.querySelectorAll("#studentTableBody tr");
  let hasMatch = false;

  rows.forEach(row => {
    const grade = row.getAttribute("data-grade")?.trim().toLowerCase();
    if (grade === currentGrade) {
      row.style.display = "";
      hasMatch = true;
    } else {
      row.style.display = "none";
    }
  });

  // Show message if no match
  if (!hasMatch) {
    document.getElementById("studentTableBody").innerHTML = `
      <tr><td colspan="3">No data available for ${yearLevel}</td></tr>`;
  }
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
