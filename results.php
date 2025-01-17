<?php
require 'conn.php';

// Fetch all polls
$polls_query = "SELECT * FROM polls";
$polls_result = mysqli_query($conn, $polls_query);

// Initialize variables
$selected_poll_id = isset($_GET['poll_id']) ? $_GET['poll_id'] : null;
$poll_question = null;
$poll_results = [];

// Fetch results for the selected poll
if ($selected_poll_id) {
    // Get poll question
    $poll_question_query = "SELECT question FROM polls WHERE id = $selected_poll_id";
    $poll_question_result = mysqli_query($conn, $poll_question_query);
    $poll_question_data = mysqli_fetch_assoc($poll_question_result);
    $poll_question = $poll_question_data['question'];

    // Get poll options and their vote counts
    $results_query = "
        SELECT po.option_text, COUNT(v.id) AS vote_count
        FROM poll_options po
        LEFT JOIN votes v ON po.id = v.option_id
        WHERE po.poll_id = $selected_poll_id
        GROUP BY po.id";
    $poll_results_result = mysqli_query($conn, $results_query);

    while ($row = mysqli_fetch_assoc($poll_results_result)) {
        $poll_results[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Poll Results</title>
</head>
<body>
    <h1>Poll Results</h1>

    <form method="get" action="results.php">
        <label for="poll">Select Poll:</label>
        <select id="poll" name="poll_id" onchange="this.form.submit()" required>
            <option value="">Select a poll</option>
            <?php while ($poll = mysqli_fetch_assoc($polls_result)): ?>
                <option value="<?= $poll['id']; ?>" <?= $selected_poll_id == $poll['id'] ? 'selected' : ''; ?>>
                    <?= $poll['question']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($selected_poll_id && $poll_question): ?>
        <h2><?= $poll_question; ?></h2>
        <?php if (!empty($poll_results)): ?>
            <ul>
                <?php foreach ($poll_results as $result): ?>
                    <li><?= $result['option_text']; ?>: <?= $result['vote_count']; ?> vote(s)</li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No votes have been cast for this poll yet.</p>
        <?php endif; ?>
    <?php elseif ($selected_poll_id): ?>
        <p>Poll not found or no results available.</p>
    <?php endif; ?>

    <a href="index.php">Back to Home</a>
</body>
</html>
