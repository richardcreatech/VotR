<?php
require 'conn.php';

if (isset($_SESSION['user_id']) && $_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $poll_id = mysqli_real_escape_string($conn, $_POST['poll_id']);
    $option_id = mysqli_real_escape_string($conn, $_POST['option_id']);
    
    // Check if the user has already voted on this poll
    $check_sql = "SELECT * FROM Votes WHERE user_id = '$user_id' AND poll_id = '$poll_id'";
    $result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($result) > 0) {
        echo "You have already voted on this poll.";
    } else {
        // Insert the vote
        $sql = "INSERT INTO Votes (user_id, poll_id, option_id) VALUES ('$user_id', '$poll_id', '$option_id')";
        if (mysqli_query($conn, $sql)) {
            echo "Vote cast successfully!";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
} else {
    echo "You must be logged in to vote.";
}
?>
