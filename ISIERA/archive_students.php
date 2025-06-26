<?php
include('db_connection.php');

// Fetch grouped archive data by grade_level and section only (no more student_type)
$query = "SELECT grade_level, section, COUNT(*) AS total_students 
          FROM archived_students 
          GROUP BY grade_level, section 
          ORDER BY grade_level ASC, section ASC";
$result = mysqli_query($conn, $query);
$groups = mysqli_num_rows($result) > 0 ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Archive</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include('sidebar.php'); ?>

<div class="main-content">
    <h2>Archive</h2>

    <div class="search-container" style="display:none;">
        <form id="searchForm" onsubmit="return false;">
            <input type="text" id="searchInput" placeholder="Search by Section..." oninput="searchGroup()">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Grade Level Filter Buttons -->
    <div class="year-levels">
        <?php
        $grades = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
        foreach ($grades as $grade) {
            echo "<div class='year-box-wrapper'>
                    <div class='year-box' onclick=\"filterByGrade('{$grade}')\">{$grade}</div>
                  </div>";
        }
        ?>
    </div>

    <table class="student-table" id="studentTable" style="display: none;">
        <thead>
            <tr>
                <th>Section</th>
                <th>No. of Students</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="studentTableBody">
        <?php if (!empty($groups)): ?>
            <?php foreach ($groups as $group): ?>
                <tr data-grade="<?= htmlspecialchars(strtolower($group['grade_level'])) ?>" data-section="<?= htmlspecialchars($group['section']) ?>">
                    <td><?= htmlspecialchars($group['section']) ?></td>
                    <td><?= htmlspecialchars($group['total_students']) ?></td>
                    <td>
                        <button class='view' title='View' onclick="window.location.href='student_archive.php?section=<?= urlencode($group['section']) ?>&grade_level=<?= urlencode($group['grade_level']) ?>'">
                            <ion-icon name='eye-outline'></ion-icon>
                        </button>
                        <button class='unarchive' title='Unarchive' onclick="unarchiveSection('<?= htmlspecialchars($group['section']) ?>')">
                            <ion-icon name='arrow-undo-outline'></ion-icon>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr id="noDataRow"><td colspan="3">No archived students found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
let selectedGrade = null;

function searchGroup() {
    const input = document.getElementById("searchInput").value.toUpperCase();
    const rows = document.querySelectorAll("#studentTableBody tr");
    let hasMatch = false;

    document.getElementById('noDataRow')?.remove();

    rows.forEach(row => {
        if (!row.hasAttribute("data-grade")) return;

        const rowGrade = row.getAttribute("data-grade");
        if (rowGrade !== selectedGrade) {
            row.style.display = "none";
            return;
        }

        const section = row.cells[0]?.textContent.toUpperCase() || "";
        const match = section.includes(input);

        row.style.display = match ? "" : "none";
        if (match) hasMatch = true;
    });

    if (!hasMatch) {
        const tbody = document.getElementById("studentTableBody");
        const noDataRow = document.createElement("tr");
        noDataRow.id = "noDataRow";
        noDataRow.innerHTML = `<td colspan="3">No matching results.</td>`;
        tbody.appendChild(noDataRow);
    }
}

function filterByGrade(grade) {
    selectedGrade = grade.trim().toLowerCase();

    document.querySelector(".year-levels").style.display = "none";
    document.querySelector(".search-container").style.display = "block";

    const table = document.getElementById("studentTable");
    const tbody = document.getElementById("studentTableBody");
    const rows = tbody.querySelectorAll("tr");

    document.getElementById('noDataRow')?.remove();

    let found = false;

    rows.forEach(row => {
        const rowGrade = row.getAttribute("data-grade");

        if (rowGrade === selectedGrade) {
            row.style.display = "";
            found = true;
        } else {
            row.style.display = "none";
        }
    });

    if (!found) {
        const noDataRow = document.createElement("tr");
        noDataRow.id = "noDataRow";
        noDataRow.innerHTML = `<td colspan="3">No data available.</td>`;
        tbody.appendChild(noDataRow);
    }

    table.style.display = "table";
}

function unarchiveSection(section) {
    if (!confirm(`Unarchive all students from section: ${section}?`)) return;

    fetch('unarchive_section_action.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `section=${encodeURIComponent(section)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Section unarchived successfully!');

            const rows = document.querySelectorAll("#studentTableBody tr");
            rows.forEach(row => {
                const rowSection = row.getAttribute("data-section");
                if (rowSection === section) row.remove();
            });

            const visibleRows = Array.from(document.querySelectorAll("#studentTableBody tr"))
                .filter(r => r.style.display !== "none");

            if (visibleRows.length === 0) {
                const noDataRow = document.createElement("tr");
                noDataRow.id = "noDataRow";
                noDataRow.innerHTML = `<td colspan="3">No data available.</td>`;
                document.getElementById("studentTableBody").appendChild(noDataRow);
            }
        } else {
            alert('Failed to unarchive: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(err => {
        alert('Request failed: ' + err);
    });
}
</script>

<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>