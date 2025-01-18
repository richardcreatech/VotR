<?php
require 'conn.php';
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Restrict access to admins only
if ($_SESSION['user_type'] !== 'admin') {
    echo "<p style='color: red;'>Access denied. Only admins can create polls.</p>";
    echo "<a href='index.php'>Back to Home</a>";
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = mysqli_real_escape_string($conn, trim($_POST['question']));
    $options = array_map('trim', explode(',', $_POST['options']));
    $creator_id = $_SESSION['user_id'];
    $expires_at = mysqli_real_escape_string($conn, $_POST['expires_at']);


    if (empty($question)) {
        $message = "Poll question is required.";
    } elseif (count($options) < 2) {
        $message = "Please provide at least two options for the poll.";
    } elseif (empty($expires_at)) {
        $message = "Please provide an expiration date.";
    } else {
        // Insert poll into database
        $poll_query = "INSERT INTO polls (question, creator_id, expires_at) VALUES (?, ?, ?)";
        $poll_stmt = mysqli_prepare($conn, $poll_query);
        mysqli_stmt_bind_param($poll_stmt, 'sis', $question, $creator_id, $expires_at);

        if (mysqli_stmt_execute($poll_stmt)) {
            $poll_id = mysqli_insert_id($conn);

            // Insert poll options
            $option_query = "INSERT INTO poll_options (poll_id, option_text) VALUES (?, ?)";
            $option_stmt = mysqli_prepare($conn, $option_query);

            foreach ($options as $option) {
                if (!empty($option)) { // Ignore empty options
                    mysqli_stmt_bind_param($option_stmt, 'is', $poll_id, $option);
                    mysqli_stmt_execute($option_stmt);
                }
            }

            $message = "Poll created successfully!";
        } else {
            $message = "Failed to create poll. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Poll</title>
</head>
<body>
    <h1>Create a Poll</h1>

    <?php if (isset($message)): ?>
        <p><?= htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form action="create_poll.php" method="post">
        <label for="question">Poll Question:</label>
        <input type="text" id="question" name="question" required><br><br>

        <label for="options">Poll Options (comma-separated):</label>
        <input type="text" id="options" name="options" required><br><br>

        <label for="expires_at">Expiration Date (YYYY-MM-DD HH:MM:SS):</label>
        <input type="datetime-local" id="expires_at" name="expires_at" required><br><br>

        <button type="submit">Create Poll</button>
    </form>
    <a href="index.php">Back to Home</a>
</body>
</html>

