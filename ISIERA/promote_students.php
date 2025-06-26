<?php
$section = $_GET['section'] ?? '';
$grade_level = $_GET['grade_level'] ?? '';
$student_type = $_GET['student_type'] ?? '';

if (isset($_GET['promoted'], $_GET['grade_level'], $_GET['section'], $_GET['student_type'])) {
    $grade_level = $_GET['grade_level'];
    $section = $_GET['section'];
    $student_type = $_GET['student_type'];
}
?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Promote Students</title>
    <link rel="stylesheet" href="assets/css/style.css" />  
    <link rel="stylesheet" href="assets/css/section.css" />
</head>
<body>
<?php include('sidebar.php'); ?>

<div class="main-content">
    <h2>Promote Students<?></h2>

    <div class="search-container">
        <form id="searchForm" onsubmit="return handleSearch(event)">
            <input type="text" id="searchInput" placeholder="Search by LRN and Name..." />
            <button type="submit">Search</button>
        </form>
    </div>

<form method="post" action="process_promotion.php">
    <input type="hidden" name="current_section" value="<?= htmlspecialchars($section) ?>" />
    <input type="hidden" name="current_grade" value="<?= htmlspecialchars($grade_level) ?>" />
    <input type="hidden" name="student_type" value="<?= htmlspecialchars($student_type) ?>" />

    <table class="student-table" id="studentTable">
        <thead>
            <tr>
                <th>LRN</th>
                <th>Name</th>
                <th class="actions-header" style="position: relative;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span>Actions</span>
                        <ion-icon name="ellipsis-vertical-outline" class="header-icon" id="promoteHeaderDropdownToggle" style="cursor: pointer;"></ion-icon>
                    </div>
                    <div id="promoteHeaderDropdownMenu" class="dropdown-menu" style="position: absolute; top: 100%; right: 0; background: white; border: 1px solid #ccc; display: none;">
                        <div class="dropdown-item" id="selectAllOption" style="padding: 8px 15px; cursor: pointer;">Select All</div>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            include 'db_connection.php';

            $sql = "SELECT lrn, last_name, first_name, middle_name 
                    FROM students 
                    WHERE section = ? AND grade_level = ? AND student_type = ?
                    ORDER BY last_name ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $section, $grade_level, $student_type);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $lrn = htmlspecialchars($row['lrn']);
                $last_name = ucwords(strtolower($row['last_name']));
                $first_name = ucwords(strtolower($row['first_name']));
                $middle_initial = strtoupper(substr($row['middle_name'], 0, 1));
                $full_name = "{$last_name}, {$first_name}" . ($middle_initial ? " {$middle_initial}." : "");
                echo "<tr>
                        <td>{$lrn}</td>
                        <td>{$full_name}</td>
                        <td><input type='checkbox' class='student-checkbox' name='selected_students[]' value='{$lrn}'></td>
                      </tr>";
            }

            $stmt->close();
            $conn->close();
            ?>
        </tbody>
    </table>

<div style="margin-top: 10px; display: flex; justify-content: flex-end;">
    <button type="button" class="promote-btn" onclick="openSectionModal()">Promote</button>
</div

</form>

<!-- Modal -->
<div class="section-modal" id="sectionModal">
    <div class="section-modal-content">
        <span class="section-close" onclick="closeSectionModal()">&times;</span>
        <h3>Enter New Section</h3>
        <form id="sectionForm" onsubmit="handleSectionSubmit(event)">
            <input type="text" id="sectionInput" name="new_section" required />
            <button type="submit" id="sectionForm button">Confirm</button>
        </form>
    </div>
</div>

<!-- Scripts -->
<script>
function searchStudent() {
    const input = document.getElementById("searchInput").value.toUpperCase();
    const rows = document.querySelectorAll("#studentTable tbody tr");

    let hasMatch = false;

    rows.forEach(row => {
        const lrn = row.cells[0]?.textContent.toUpperCase() || "";
        const name = row.cells[1]?.textContent.toUpperCase() || "";
        const match = lrn.includes(input) || name.includes(input);

        row.style.display = match ? "" : "none";
        if (match) hasMatch = true;
    });

    // Optional: Display a message if no matches found
    const existingNoData = document.getElementById("noDataRow");
    if (existingNoData) existingNoData.remove();

    if (!hasMatch) {
        const tbody = document.querySelector("#studentTable tbody");
        const newRow = document.createElement("tr");
        newRow.id = "noDataRow";
        newRow.innerHTML = `<td colspan="3">No matching results.</td>`;
        tbody.appendChild(newRow);
    }
}

// Auto-run search on typing
document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("searchInput").addEventListener("input", searchStudent);
});

function openSectionModal() {
    const checked = document.querySelectorAll("input[name='selected_students[]']:checked");
    if (checked.length === 0) {
        alert("Please select at least one student.");
        return;
    }
    document.getElementById("sectionModal").classList.add("show");
}

function closeSectionModal() {
    document.getElementById("sectionModal").classList.remove("show");
}

    function handleSectionSubmit(event) {
        event.preventDefault();
        const section = document.getElementById("sectionInput").value.trim();
        if (!section) {
            alert("Please enter a new section.");
            return;
        }
        if (!confirm("Promote selected students to the next grade level and assign them to section " + section + "?")) {
            return;
        }
        const form = document.querySelector("form[action='process_promotion.php']");
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "new_section";
        input.value = section;
        form.appendChild(input);
        form.submit();
    }

    // Dropdown
    const promoteToggle = document.getElementById('promoteHeaderDropdownToggle');
    const promoteDropdown = document.getElementById('promoteHeaderDropdownMenu');
    const selectAllOption = document.getElementById('selectAllOption');
    let allSelected = false;

    promoteToggle.addEventListener('click', e => {
        e.stopPropagation();
        promoteDropdown.style.display = promoteDropdown.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', () => {
        promoteDropdown.style.display = 'none';
    });

    selectAllOption.addEventListener('click', () => {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        allSelected = !allSelected;
        checkboxes.forEach(cb => cb.checked = allSelected);
        selectAllOption.textContent = allSelected ? 'Deselect All' : 'Select All';
        promoteDropdown.style.display = 'none';
    });
</script>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>