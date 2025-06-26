<?php
// Include database connection
include('db_connection.php');

// Get filter parameters from URL
$gradeLevel = isset($_GET['grade_level']) ? $_GET['grade_level'] : '';
$studentType = isset($_GET['student_type']) ? $_GET['student_type'] : '';
$section = isset($_GET['section']) ? $_GET['section'] : '';

// Build SQL query with filters
$query = "SELECT lrn, 
    CONCAT(
        UPPER(LEFT(last_name, 1)), LOWER(SUBSTRING(last_name, 2)), ', ',
        UPPER(LEFT(first_name, 1)), LOWER(SUBSTRING(first_name, 2)), ' ',
        UPPER(LEFT(middle_name, 1)), '.'
    ) AS fullname, 
    email, student_type 
    FROM students
    WHERE 1=1";

if ($gradeLevel) {
    $query .= " AND grade_level = '" . mysqli_real_escape_string($conn, $gradeLevel) . "'";
}
if ($studentType) {
    $query .= " AND student_type = '" . mysqli_real_escape_string($conn, $studentType) . "'";
}
if ($section) {
    $query .= " AND section = '" . mysqli_real_escape_string($conn, $section) . "'";
}

$query .= " ORDER BY last_name ASC, first_name ASC, middle_name ASC";

$result = mysqli_query($conn, $query);

// Fetch data
$students = [];
if (mysqli_num_rows($result) > 0) {
    $students = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Details</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include('sidebar.php'); ?>

<div class="main-content">
    <h2>Student List</h2>

    <div class="search-container">
        <div class="left-search">
            <form id="searchForm" onsubmit="return handleSearch(event)">
                <input type="text" id="searchInput" placeholder="Search by LRN or Name...">
                <button type="submit">Search</button>
            </form>
        </div>
    </div>

    <table class="student-table" id="studentTable">
        <thead>
            <tr>
                <th>LRN</th>
                <th>Full Name</th>
                <th>Email</th>
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
            <?php if (!empty($students)): ?>
                <?php foreach ($students as $student): ?>
                    <tr data-type="<?= strtolower($student['student_type']) ?>">
                        <td><?= $student['lrn'] ?></td>
                        <td><?= $student['fullname'] ?></td>
                        <td><?= $student['email'] ?></td>
                        <td>
                            <button class='edit' title="Edit" onclick='redirectToStudentInfo("<?= $student['lrn'] ?>", "edit")'>
                                <ion-icon name="create-outline"></ion-icon>
                            </button>
                            <button class='archive' title="Archive" onclick='archiveStudent("<?= $student['lrn'] ?>")'>
                                <ion-icon name="archive-outline"></ion-icon>
                            </button>
                            <button class='view' title="View" onclick='redirectToStudentInfo("<?= $student['lrn'] ?>", "view")'>
                                <ion-icon name='eye-outline'></ion-icon>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr id="noDataRow"><td colspan="4">No data available.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function searchStudent() {
    const input = document.getElementById("searchInput").value.toUpperCase();
    const rows = document.querySelectorAll("#studentTableBody tr");
    let hasMatch = false;

    rows.forEach(row => {
        if (row.id === "noDataRow") {
            row.style.display = "none";
            return;
        }

        const lrn = row.cells[0].textContent.toUpperCase();
        const name = row.cells[1].textContent.toUpperCase();
        const match = lrn.includes(input) || name.includes(input);
        row.style.display = match ? "" : "none";
        if (match) hasMatch = true;
    });

    const existingNoDataRow = document.getElementById("noDataRow");
    if (existingNoDataRow) existingNoDataRow.remove();

    if (!hasMatch) {
        const tbody = document.getElementById("studentTableBody");
        const newRow = document.createElement("tr");
        newRow.id = "noDataRow";
        newRow.innerHTML = `<td colspan='4'>No matching results.</td>`;
        tbody.appendChild(newRow);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("searchInput").addEventListener("input", searchStudent);
});

function redirectToStudentInfo(studentLrn, mode) {
    window.location.href = `student_profile.php?lrn=${studentLrn}&mode=${mode}`;
}

function archiveStudent(lrn) {
    if (!confirm(`Are you sure you want to archive student LRN: ${lrn}?`)) return;

    fetch('archive_student_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `lrn=${encodeURIComponent(lrn)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Student archived successfully!');
            location.reload();
        } else {
            alert('Error archiving student: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Request failed: ' + error);
    });
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

function archiveAll() {
    window.location.href = 'archive_students.php';
}
</script>

<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>