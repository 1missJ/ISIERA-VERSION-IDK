<?php
include('db_connection.php');

$query = "SELECT section, grade_level, COUNT(*) AS student_count 
          FROM students 
          GROUP BY section, grade_level
          ORDER BY section ASC";

$result = mysqli_query($conn, $query);

$students = [];

if (mysqli_num_rows($result) > 0) {
    $students = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Information</title>
    <link rel="stylesheet" href="assets/css/style.css">
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

<h2 id="studentHeading" style="display:none;">Student Information</h2>
    <!-- Search Bar -->
    <div class="search-container" style="display:none;">
        <div class="left-search">
            <form id="searchForm" onsubmit="return handleSearch(event)">
                <input type="text" id="searchInput" placeholder="Search by Section...">
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
                <th class="actions-header" style="position: relative;">
                    <div class="header-content">
                        <span>Actions</span>
                        <ion-icon name="ellipsis-vertical-outline" class="header-icon" id="headerDropdownToggle"></ion-icon>
                    </div>
                    <div id="headerDropdownMenu" class="dropdown-menu">
                        <div class="dropdown-item" onclick="archiveAll()">Archive</div>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody id="studentTableBody">
            <?php
            if (!empty($students)) {
                foreach ($students as $student) {
                    $sectionFormatted = ucwords(strtolower($student['section']));
                    $sectionUrl = urlencode($student['section']);
                    echo "<tr data-grade='" . strtolower($student['grade_level']) . "'>
                            <td>{$sectionFormatted}</td>
                            <td>{$student['student_count']}</td>
                            <td>
<button class='view-btn' title='View' onclick=\"location.href='details_student.php?grade_level=" . urlencode($student['grade_level']) . "&section={$sectionUrl}'\">
                                    <ion-icon name='eye-outline'></ion-icon>
                                </button>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr id='noDataRow'><td colspan='3'>No data available.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
let currentGrade = "";

function searchStudent() {
    const input = document.getElementById("searchInput").value.toUpperCase();
    const rows = document.querySelectorAll("#studentTableBody tr");

    let hasMatch = false;

    rows.forEach(row => {
        if (row.id === "noDataRow") {
            row.style.display = "none";
            return;
        }

        const grade = row.getAttribute("data-grade")?.trim().toLowerCase();
        const section = row.querySelector("td:nth-child(1)")?.textContent.toUpperCase() || "";

        if (grade === currentGrade) {
            if (section.includes(input)) {
                row.style.display = "";
                hasMatch = true;
            } else {
                row.style.display = "none";
            }
        } else {
            row.style.display = "none";
        }
    });

    const existingNoDataRow = document.getElementById("noDataRow");
    if (existingNoDataRow) existingNoDataRow.remove();

    if (!hasMatch) {
        const tbody = document.getElementById("studentTableBody");

        const hasAnyData = Array.from(rows).some(row =>
            row.id !== "noDataRow" &&
            row.getAttribute("data-grade") === currentGrade
        );

        const newRow = document.createElement("tr");
        newRow.id = "noDataRow";
        newRow.innerHTML = `<td colspan='3'>${hasAnyData ? "No matching results." : "No data available."}</td>`;
        tbody.appendChild(newRow);
    }
}

function showStudents(yearLevel) {
    currentGrade = yearLevel.trim().toLowerCase();

    // Keep dropdown visible

    // Show search bar, heading, and student table
    document.querySelector(".search-container").style.display = "flex";
    document.getElementById("studentHeading").style.display = "block";
    document.getElementById("searchInput").value = "";
    document.getElementById("studentTable").style.display = "table";

    searchStudent();
}

document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("searchInput").addEventListener("input", searchStudent);
});

function archiveAll() {
    window.location.href = 'archive_students.php';
}

const dropdownToggle = document.getElementById('headerDropdownToggle');
const dropdownMenu = document.getElementById('headerDropdownMenu');

dropdownToggle.addEventListener('click', function(event) {
    event.stopPropagation();
    dropdownMenu.style.display = (dropdownMenu.style.display === 'block') ? 'none' : 'block';
});

document.addEventListener('click', function() {
    dropdownMenu.style.display = 'none';
});
</script>

<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>