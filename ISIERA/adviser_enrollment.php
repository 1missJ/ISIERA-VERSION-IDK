<?php
include('db_connection.php');
session_start();

if (!isset($_SESSION['teacher_id'])) {
    header("Location: index.php");
    exit();
}

$teacherName = $_SESSION['teacher_name'];
?>

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
<?php include('adviser_sidebar.php'); ?>

<!-- Main Content -->
<div class="main-content">
  <h2>List of Students</h2>

  <!-- Search Bar -->
  <div class="search-container" style="display: flex; margin-bottom: 20px;">
    <div class="left-search">
      <form id="searchForm" onsubmit="return false;">
        <input type="text" id="searchInput" placeholder="Search by student name..." />
        <button type="submit">Search</button>
      </form>
    </div>
  </div>

<!-- Student Table -->
<table class="student-table" id="studentTable">
  <thead>
    <tr>
      <th>LRN</th>
      <th>Name</th>
    </tr>
  </thead>
  <tbody id="studentTableBody">
    <?php
    $stmt = $conn->prepare("SELECT lrn, CONCAT(last_name, ', ', first_name) AS name FROM students ORDER BY last_name ASC");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()):
    ?>
      <tr>
        <td><?= htmlspecialchars($row['lrn']) ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>


  <script>
    function searchStudent() {
      const input = document.getElementById("searchInput").value.toUpperCase();
      const rows = document.querySelectorAll("#studentTableBody tr");

      rows.forEach(row => {
        const nameCell = row.querySelectorAll("td")[1]; // name column
        if (nameCell) {
          const studentName = nameCell.textContent.toUpperCase();
          row.style.display = studentName.includes(input) ? "" : "none";
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
