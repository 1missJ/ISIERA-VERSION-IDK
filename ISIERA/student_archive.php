<?php
include('db_connection.php');

// Get filters from GET parameters
$section = isset($_GET['section']) ? $_GET['section'] : '';
$grade_level = isset($_GET['grade_level']) ? $_GET['grade_level'] : '';

// Escape values
$sectionEscaped = mysqli_real_escape_string($conn, $section);
$gradeLevelEscaped = mysqli_real_escape_string($conn, $grade_level);

// Fetch students from archive WITHOUT student_type filtering
$query = "
    SELECT 
        lrn,
        CONCAT(UPPER(LEFT(last_name,1)), LOWER(SUBSTRING(last_name,2)), ', ',
               UPPER(LEFT(first_name,1)), LOWER(SUBSTRING(first_name,2)), ' ',
               UPPER(LEFT(middle_name,1)), '.') AS fullname,
        email
    FROM archived_students
    WHERE section = '$sectionEscaped'
      AND grade_level = '$gradeLevelEscaped'
    ORDER BY last_name, first_name, middle_name
";

$result = mysqli_query($conn, $query);
$students = mysqli_num_rows($result) > 0 ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Archived Students</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include('sidebar.php'); ?>

<div class="main-content">
    <h2>Archived Students</h2>

    <div class="search-container">
        <form id="searchForm" onsubmit="return false;">
            <input type="text" id="searchInput" placeholder="Search by LRN or Name..." oninput="searchStudent()">
            <button type="submit">Search</button>
        </form>
    </div>

    <form method="POST" action="unarchive_student_action.php">
        <table class="student-table" id="studentTable">
            <thead>
                <tr>
                    <th>LRN</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th style="position: relative;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span>Actions</span>
                            <ion-icon name="ellipsis-vertical-outline" id="archiveHeaderDropdownToggle" style="cursor: pointer;"></ion-icon>
                        </div>
                        <div id="archiveHeaderDropdownMenu" class="dropdown-menu" style="position: absolute; top: 100%; right: 0; background: white; border: 1px solid #ccc; display: none;">
                            <div class="dropdown-item" id="selectAllCheckboxes" style="padding: 8px 15px; cursor: pointer;">Select All</div>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody id="studentTableBody">
                <?php if (!empty($students)): ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['lrn']) ?></td>
                            <td><?= htmlspecialchars($student['fullname']) ?></td>
                            <td><?= htmlspecialchars($student['email']) ?></td>
                            <td>
                                <input type="checkbox" class="student-checkbox" name="selected_lrns[]" value="<?= $student['lrn'] ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr id="noDataRow"><td colspan="4">No data available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div style="margin-top: 10px; display: flex; justify-content: flex-end;">
            <button type="submit" class="promote-btn">Unarchive</button>
        </div>
    </form>
</div>

<!-- Scripts -->
<script>
function searchStudent() {
    const input = document.getElementById("searchInput").value.toUpperCase();
    const rows = document.querySelectorAll("#studentTableBody tr");

    let hasMatch = false;
    document.getElementById("noDataRow")?.remove();

    rows.forEach(row => {
        const lrn = row.cells[0].textContent.toUpperCase();
        const name = row.cells[1].textContent.toUpperCase();
        const match = lrn.includes(input) || name.includes(input);
        row.style.display = match ? "" : "none";
        if (match) hasMatch = true;
    });

    if (!hasMatch) {
        const tbody = document.getElementById("studentTableBody");
        const noMatch = document.createElement("tr");
        noMatch.id = "noDataRow";
        noMatch.innerHTML = "<td colspan='4'>No matching results.</td>";
        tbody.appendChild(noMatch);
    }
}

// Select All / Deselect All Logic
const archiveToggle = document.getElementById('archiveHeaderDropdownToggle');
const archiveDropdown = document.getElementById('archiveHeaderDropdownMenu');
const selectAllCheckboxes = document.getElementById('selectAllCheckboxes');
let allChecked = false;

archiveToggle.addEventListener('click', e => {
    e.stopPropagation();
    archiveDropdown.style.display = archiveDropdown.style.display === 'block' ? 'none' : 'block';
});

document.addEventListener('click', () => {
    archiveDropdown.style.display = 'none';
});

selectAllCheckboxes.addEventListener('click', () => {
    const checkboxes = document.querySelectorAll('.student-checkbox');
    allChecked = !allChecked;
    checkboxes.forEach(cb => cb.checked = allChecked);
    selectAllCheckboxes.textContent = allChecked ? 'Deselect All' : 'Select All';
    archiveDropdown.style.display = 'none';
});
</script>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>
