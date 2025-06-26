<?php
include('db_connection.php'); 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to generate unique 6-digit teacher ID
function generateTeacherID($conn) {
    $yearPrefix = date("y"); // e.g., "24"
    do {
        $randomDigits = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT); // 0000-9999
        $teacherID = $yearPrefix . $randomDigits;

        // Check if ID exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM faculty WHERE teacher_id = ?");
        $stmt->bind_param("s", $teacherID);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    } while ($count > 0);

    return $teacherID;
}

// Handle faculty registration
if (isset($_POST['register'])) {
    $teacher_id = generateTeacherID($conn);
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $dob = $_POST['dob'];

    $hashedPassword = password_hash($dob, PASSWORD_DEFAULT); // hash DOB as password

    $sql = "INSERT INTO faculty (teacher_id, name, email, contact, dob, password) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $teacher_id, $name, $email, $contact, $dob, $hashedPassword);

    if ($stmt->execute()) {
        echo "<script>alert('Faculty successfully registered! Teacher ID: $teacher_id');</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Faculty Registration - TapInTime</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/faculty.css">
</head>
<body>

    <?php include('sidebar.php'); ?>

    <div class="faculty-party" style="margin-left: 420px;">

        <div class="faculty-card">
            <form method="POST" action="">
                <label>Full Name</label>
                <input type="text" name="name" required>

                <label>Email Address</label>
                <input type="email" name="email" required>

                <label>Contact Number</label>
                <input type="text" name="contact" required>

                <label>Date of Birth (used as default password)</label>
                <input type="date" name="dob" required>

                <input type="submit" name="register" value="Register Faculty">
            </form>
        </div>

        <h2>Registered Faculty List</h2>
        <table class="faculty-table">
            <thead>
                <tr>
                    <th>Teacher ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Date of Birth</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT teacher_id, name, email, contact, dob FROM faculty ORDER BY id DESC");
                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['teacher_id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['contact']) ?></td>
                    <td><?= htmlspecialchars($row['dob']) ?></td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="5">No faculty registered yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="assets/js/main.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>
