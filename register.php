?php
require 'conn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $check_sql = "SELECT * FROM Users WHERE email = '$email'";
    $result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($result) > 0) {
        echo "Error: Duplicate email.";
    } else {
        $sql = "INSERT INTO Users (username, password, email, registration_date) VALUES ('$username', '$password', '$email', NOW())";
        if (mysqli_query($conn, $sql)) {
            echo "Registration successful!";
            $_SESSION['user_id'] = mysqli_insert_id($conn);
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

function display_polls($conn) {
    $sql = "SELECT * FROM Polls";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<form method='post' action='vote.php'>
                <h3>" . $row['question'] . "</h3>
                <input type='hidden' name='poll_id' value='" . $row['poll_id'] . "'>";
        $poll_id = $row['poll_id'];
        $options_sql = "SELECT * FROM Options WHERE poll_id = '$poll_id'";
        $options_result = mysqli_query($conn, $options_sql);
        while ($option = mysqli_fetch_assoc($options_result)) {
            echo "<input type='radio' name='option_id' value='" . $option['option_id'] . "'>" . $option['option_text'] . "<br>";
        }
        echo "<button type='submit' name='vote'>Vote</button>
              </form><hr>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register and Create Poll</title>
</head>
<body>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <h2>Register</h2>
        <form method="POST" action="register.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>
            <button type="submit">Register</button>
        </form>
    <?php else: ?>
        <h2>Create Poll</h2>
        <form method="POST" action="create_poll.php">
            <label for="question">Question:</label>
            <input type="text" id="question" name="question" required><br>
            <label for="option1">Option 1:</label>
            <input type="text" id="option1" name="options[]" required><br>
            <label for="option2">Option 2:</label>
            <input type="text" id="option2" name="options[]" required><br>
            <button type="submit">Create Poll</button>
        </form>
        <h2>Vote on Polls</h2>
        <?php display_polls($conn); ?>
    <?php endif; ?>
</body>
</html>
