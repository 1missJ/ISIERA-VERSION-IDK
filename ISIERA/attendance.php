<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$filter = $_GET['filter'] ?? 'all'; // Default to all
$selectedSubject = $_GET['subject'] ?? 'all'; // For UI only

// Sample subjects (no DB)
$subjects = ['Math', 'English', 'Science', 'PE'];

// Set filtering condition
$dateCondition = "";
if ($filter === 'weekly') {
    $dateCondition = "AND date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
} elseif ($filter === 'monthly') {
    $dateCondition = "AND date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
} elseif ($filter === 'quarterly') {
    $dateCondition = "AND date >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
}

$stmt = $conn->prepare("SELECT date, time, remarks FROM attendance WHERE student_id = ? $dateCondition ORDER BY date DESC, time DESC");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$attendance_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Record</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<?php include('student_portal_navigation.php'); ?>

<div class="container mt-5">
    <h3 class="text-primary">Attendance Record</h3>

    <!-- Filters -->
    <form method="GET" class="mb-4 d-flex gap-4 flex-wrap">
        <!-- Subject Dropdown (UI only) -->
        <div>
            <label for="subject" class="form-label">Subject:</label>
            <select name="subject" id="subject" class="form-select" onchange="this.form.submit()">
                <option value="all" <?= $selectedSubject === 'all' ? 'selected' : '' ?>>All</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?= htmlspecialchars($subject) ?>" <?= $selectedSubject === $subject ? 'selected' : '' ?>>
                        <?= htmlspecialchars($subject) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Filter by Date Range -->
        <div>
            <label for="filter" class="form-label">Filter by:</label>
            <select name="filter" id="filter" class="form-select" onchange="this.form.submit()">
                <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All</option>
                <option value="weekly" <?= $filter === 'weekly' ? 'selected' : '' ?>>Weekly</option>
                <option value="monthly" <?= $filter === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                <option value="quarterly" <?= $filter === 'quarterly' ? 'selected' : '' ?>>Quarterly</option>
            </select>
        </div>
    </form>

    <!-- Attendance Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center">
            <thead class="table-primary">
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($attendance_result->num_rows > 0): ?>
                    <?php while ($row = $attendance_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars(date("F j, Y", strtotime($row['date']))) ?></td>
                            <td><?= htmlspecialchars(date("h:i A", strtotime($row['time']))) ?></td>
                            <td><?= htmlspecialchars($row['remarks']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-muted">No attendance records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
