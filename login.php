<?php
require 'conn.php';
require 'functions.php';
session_start();

$message = ''; // Variable to store messages for user feedback

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $message = "Please enter both email and password.";
    } else {
        // Query to fetch the user by email
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['user_type'] = $user['user_type'];

                $message = "Login successful! Redirecting to the home page...";
                // Redirect after a delay to show the success message
                echo "<script>
                        setTimeout(() => {
                            window.location.href = 'index.php';
                        }, 2000);
                      </script>";
            } else {
                $message = "Invalid email or password. Please try again.";
            }
        } else {
            $message = "Invalid email or password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>

    <form action="login.php" method="post">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
