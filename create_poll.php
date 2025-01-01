<?php
require 'conn.php';
session_start();

if (isset($_SESSION['user_id']) && $_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $question = mysqli_real_escape_string($conn, $_POST['question']);
    $sql = "INSERT INTO Polls (user_id, question) VALUES ('$user_id', '$question')";
    
    if (mysqli_query($conn, $sql)) {
        $poll_id = mysqli_insert_id($conn);
        foreach ($_POST['options'] as $option) {
            $option_text = mysqli_real_escape_string($conn, $option);
            $sql = "INSERT INTO Options (poll_id, option_text) VALUES ('$poll_id', '$option_text')";
            mysqli_query($conn, $sql);
        }
        echo "Poll created successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "You must be logged in to create a poll.";
}
?>
