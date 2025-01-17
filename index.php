<?php
session_start();

$user_name = $_SESSION['full_name'] ?? 'Guest';
$user_type = $_SESSION['user_type'] ?? 'Unknown';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Home</title>
</head>
<body>
    <h1>Welcome! to the Nile Polling System</h1>
    <p>Hello, <?= htmlspecialchars($user_name); ?>!</p>
    <p>You are logged in as <?= htmlspecialchars($user_type); ?>.</p>

    <a href="register.php">Register</a> |
    <a href="login.php">Login</a> |
    <a href="create_poll.php">Create Poll</a> |
    <a href="vote.php">Vote</a> |
    <a href="results.php">Results</a> |
    <a href="logout.php">Logout</a>
</body>
</html>
