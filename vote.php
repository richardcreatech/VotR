<?php
require 'conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$poll_id = $_POST['poll_id'] ?? '';
$options = [];

// Fetch polls
$polls_query = "SELECT * FROM polls WHERE expires_at > NOW()";
$polls_result = mysqli_query($conn, $polls_query);

// Fetch options for selected poll
if ($poll_id) {
    $options_query = "SELECT * FROM poll_options WHERE poll_id = '$poll_id'";
    $options_result = mysqli_query($conn, $options_query);
    while ($option = mysqli_fetch_assoc($options_result)) {
        $options[] = $option;
    }
}

// Handle voting
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['option_id'])) {
    $option_id = mysqli_real_escape_string($conn, $_POST['option_id']);
    $user_id = $_SESSION['user_id'];

    // Check if the user has already voted
    $check_query = "SELECT * FROM votes WHERE poll_id = '$poll_id' AND user_id = '$user_id'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $message = "You have already voted on this poll.";
    } else {
        if ($poll_id) {
            $poll_check_query = "SELECT expires_at FROM polls WHERE id = '$poll_id'";
            $poll_check_result = mysqli_query($conn, $poll_check_query);
            $poll_data = mysqli_fetch_assoc($poll_check_result);

            if (strtotime($poll_data['expires_at']) < time()) {
                $message = "This poll has expired. You cannot vote.";
            } else {
                $vote_query = "INSERT INTO votes (poll_id, option_id, user_id) VALUES ('$poll_id', '$option_id', '$user_id')";
                $message = mysqli_query($conn, $vote_query) ? "Vote cast successfully!" : "Error casting vote.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Vote</title>
</head>
<body>
    <h1>Vote on a Poll</h1>

    <?php if ($message): ?>
        <p><?= $message; ?></p>
    <?php endif; ?>

    <form action="vote.php" method="post">
        <label for="poll">Poll:</label>
        <select id="poll" name="poll_id" required onchange="this.form.submit()">
            <option value="">Select a poll</option>
            <?php while ($poll = mysqli_fetch_assoc($polls_result)): ?>
                <option value="<?= $poll['id']; ?>" <?= $poll['id'] == $poll_id ? 'selected' : ''; ?>>
                    <?= $poll['question']; ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <?php if ($poll_id && !empty($options)): ?>
            <label for="option">Option:</label>
            <select id="option" name="option_id" required>
                <option value="">Select an option</option>
                <?php foreach ($options as $option): ?>
                    <option value="<?= $option['id']; ?>"><?= $option['option_text']; ?></option>
                <?php endforeach; ?>
            </select><br><br>
            <button type="submit">Vote</button>
        <?php elseif ($poll_id): ?>
            <p>No options available for this poll.</p>
        <?php endif; ?>
    </form>
    <a href="index.php">Back to Home</a>
</body>
</html>
