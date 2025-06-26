<?php
include('db_connection.php');

// Count pending students
$pending_students_count = $conn->query("SELECT COUNT(*) as total FROM pending_students")->fetch_assoc()['total'];

// Count students with unassigned RFID
$unassigned_rfid_count = $conn->query("SELECT COUNT(*) as total FROM students WHERE rfid IS NULL OR rfid = ''")->fetch_assoc()['total'];

// Count teachers
$teacher_count = $conn->query("SELECT COUNT(*) as total FROM faculty")->fetch_assoc()['total'];

// Count students
$student_count = $conn->query("SELECT COUNT(*) as total FROM students")->fetch_assoc()['total'];

// Fetch ALL recent activity
$recent_query = $conn->query("
    SELECT f.name as teacher, s.subject_name, a.created_at
    FROM assign a 
    JOIN faculty f ON a.teacher_id = f.id 
    JOIN subjects s ON a.subject_id = s.id 
    ORDER BY a.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TapInTime Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        .dashboard-container {
            margin-left: 320px;
            padding: 20px;
            font-family: 'Segoe UI', sans-serif;
            background-color: #fdfdfd;
        }
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.06);
            transition: 0.3s;
        }
        .card:hover {
            transform: translateY(-2px);
        }
        .card ion-icon {
            font-size: 32px;
        }
        .card h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }
        .card p {
            margin: 2px 0 0;
            font-size: 22px;
            font-weight: bold;
            color: #2a2a2a;
        }
        .subjects-card { background-color: #fde2e4; color: #6a1b1b; }
        .teachers-card { background-color: #d8e2dc; color: #1b3a4b; }
        .students-card { background-color: #e2f0cb; color: #2e7d32; }
        .assigned-card { background-color: #cde7f0; color: #1e88e5; }
        .recent-activity {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.06);
            max-height: 400px;
            overflow-y: auto;
        }
        .recent-activity h2 {
            margin-bottom: 15px;
            font-size: 20px;
            color: #222;
        }
        .recent-activity ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .recent-activity li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            font-size: 16px;
        }
        .recent-activity li:last-child {
            border-bottom: none;
        }
        h1 {
            text-align: center;
            color: rgb(128, 189, 246);
            margin-bottom: 30px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-weight: 600;
        }
    </style>
</head>
<body>
<?php include('sidebar.php'); ?>

<div class="dashboard-container">
    <h1>Welcome to TapInTime Dashboard 👋</h1>

    <div class="dashboard-cards">

    <a href="student_verification.php" style="text-decoration: none;">
        <div class="card" style="background-color: #fff3cd; color: #856404; cursor: pointer;">
            <ion-icon name="person-add-outline"></ion-icon>
            <div>
                <h3>Pending Students</h3>
                <p><?= htmlspecialchars($pending_students_count) ?></p>
            </div>
        </div>
    </a>

    <a href="id_generation.php" style="text-decoration: none;">
        <div class="card" style="background-color: #f8d7da; color: #721c24; cursor: pointer;">
            <ion-icon name="card-outline"></ion-icon>
            <div>
                <h3>Unassigned RFID</h3>
                <p><?= htmlspecialchars($unassigned_rfid_count) ?></p>
            </div>
        </div>
    </a>

    <a href="faculty_registration.php" style="text-decoration: none;">
        <div class="card teachers-card" style="cursor: pointer;">
            <ion-icon name="people-outline"></ion-icon>
            <div>
                <h3>Total Teachers</h3>
                <p><?= htmlspecialchars($teacher_count) ?></p>
            </div>
        </div>
    </a>

    <a href="student_details.php" style="text-decoration: none;">
        <div class="card students-card" style="cursor: pointer;">
            <ion-icon name="school-outline"></ion-icon>
            <div>
                <h3>Total Students</h3>
                <p><?= htmlspecialchars($student_count) ?></p>
            </div>
        </div>
    </a>

</div>


    <div style="background: #fff; padding: 20px; margin-bottom: 30px; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.06);">
        <h2 style="margin-bottom: 15px; color: #444;">📊 Attendance Rate This Week</h2>
        <canvas id="attendanceChart" height="100"></canvas>
    </div>

    <div class="recent-activity">
        <h2>📌 Recent Activity</h2>
        <ul>
            <?php if ($recent_query->num_rows > 0): ?>
                <?php while ($activity = $recent_query->fetch_assoc()): ?>
                    <li>👨‍🏫 <?= htmlspecialchars($activity['teacher']) ?> assigned to <strong><?= htmlspecialchars($activity['subject_name']) ?></strong> on <em><?= date('F j, Y - g:i A', strtotime($activity['created_at'])) ?></em></li>
                <?php endwhile; ?>
            <?php else: ?>
                <li>No recent activity available.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<!-- Chart Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            datasets: [{
                label: 'Attendance Rate (%)',
                data: [92, 80, 90, 79, 87],
                fill: true,
                backgroundColor: 'rgba(173, 216, 230, 0.2)',
                borderColor: 'rgba(100, 149, 237, 1)',
                borderWidth: 2,
                tension: 0.3,
                pointBackgroundColor: '#6495ED'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        stepSize: 10,
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + '% attendance';
                        }
                    }
                }
            }
        }
    });
</script>
</body>
</html>
