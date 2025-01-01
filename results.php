<?php
require 'conn.php';
session_start();

if (isset($_GET['poll_id'])) {
    $poll_id = mysqli_real_escape_string($conn, $_GET['poll_id']);
    $poll_sql = "SELECT question FROM Polls WHERE poll_id = '$poll_id'";
    $poll_result = mysqli_query($conn, $poll_sql);
    
    if ($poll_row = mysqli_fetch_assoc($poll_result)) {
        echo "<h2>Results for: " . $poll_row['question'] . "</h2>";
        
        $options_sql = "SELECT option_text, COUNT(Votes.option_id) as votes 
                        FROM Options 
                        LEFT JOIN Votes ON Options.option_id = Votes.option_id
                        WHERE Options.poll_id = '$poll_id'
                        GROUP BY Options.option_id";
        $options_result = mysqli_query($conn, $options_sql);
        
        while ($option_row = mysqli_fetch_assoc($options_result)) {
            echo "<p>" . $option_row['option_text'] . ": " . $option_row['votes'] . " votes</p>";
        }
    } else {
        echo "Invalid poll ID.";
    }
} else {
    echo "No poll ID provided.";
}
?>
