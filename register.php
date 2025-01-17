<?php
require 'conn.php';
require 'functions.php';

session_start();

// Check the admin limit before processing the form
$admin_limit_reached = false;
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $admin_count_query = "SELECT COUNT(*) as admin_count FROM users WHERE user_type = 'admin'";
    $result = mysqli_query($conn, $admin_count_query);
    $row = mysqli_fetch_assoc($result);
    if ($row['admin_count'] >= 3) {
        $admin_limit_reached = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $school_id = mysqli_real_escape_string($conn, $_POST['school_id']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $user_type = mysqli_real_escape_string($conn, $_POST['user_type']);
    $level = $user_type === 'student' ? mysqli_real_escape_string($conn, $_POST['level']) : null;

    // Check admin limit
    if ($user_type === 'admin' && $admin_limit_reached) {
        echo "<p style='color: red;'>Admin limit reached. Registration failed.</p>";
        exit;
    }

    // Register the user
    if (register_user($school_id, $full_name, $email, $password, $user_type, $level)) {
        header('Location: login.php');
        exit;
    } else {
        echo "<p style='color: red;'>Registration failed! Please try again.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Registration</title>
    <script>
        function toggleLevel() {
            const userType = document.getElementById('user_type').value;
            const levelSelection = document.getElementById('level_selection');
            levelSelection.style.display = userType === 'student' ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <h1>Register</h1>

    <form action="register.php" method="post">
        <label for="school_id">School ID:</label>
        <input type="text" id="school_id" name="school_id" required><br><br>

        <label for="full_name">Full Name:</label>
        <input type="text" id="full_name" name="full_name" required><br><br>

        <label for="email">School Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <label for="user_type">User Type:</label>
        <select id="user_type" name="user_type" onchange="toggleLevel()" required>
            <option value="">Select</option>
            <option value="student">Student</option>
            <option value="staff">Staff</option>
            <?php if (!$admin_limit_reached): ?>
                <option value="admin">Admin</option>
            <?php endif; ?>
        </select><br><br>

        <div id="level_selection" style="display: none;">
            <label for="level">Level:</label>
            <select id="level" name="level">
                <option value="">Select</option>
                <option value="100lvl">100 Level</option>
                <option value="200lvl">200 Level</option>
                <option value="300lvl">300 Level</option>
                <option value="400lvl">400 Level</option>
            </select><br><br>
        </div>

        <button type="submit" <?= $admin_limit_reached ? 'disabled' : ''; ?>>Register</button>
    </form>
</body>
</html>

