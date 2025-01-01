<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "online_voting");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// SQL to create table Users if not exists
$sql = "CREATE TABLE IF NOT EXISTS Users (
    user_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";


// SQL to create table Polls if not exists
$sql = "CREATE TABLE IF NOT EXISTS Polls (
    poll_id INT(6) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) NOT NULL,
    question TEXT NOT NULL,
    description TEXT,
    expiration_date DATE,
    voting_limit INT,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
)";

// SQL to create table Options if not exists
$sql = "CREATE TABLE IF NOT EXISTS Options (
    option_id INT(6) AUTO_INCREMENT PRIMARY KEY,
    poll_id INT(6) NOT NULL,
    option_text TEXT NOT NULL,
    FOREIGN KEY (poll_id) REFERENCES Polls(poll_id)
)";


// SQL to create table Votes if not exists
$sql = "CREATE TABLE IF NOT EXISTS Votes (
    vote_id INT(6) AUTO_INCREMENT PRIMARY KEY,
    poll_id INT(6) NOT NULL,
    user_id INT(6) NOT NULL,
    option_id INT(6) NOT NULL,
    FOREIGN KEY (poll_id) REFERENCES Polls(poll_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (option_id) REFERENCES Options(option_id)
)";
?>
