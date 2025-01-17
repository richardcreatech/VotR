<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "online_voting");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// SQL to create the tables
$table_queries = [
    "CREATE TABLE IF NOT EXISTS `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `school_id` int(20) NOT NULL,
        `full_name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `password` varchar(255) NOT NULL,
        `user_type` enum('student','staff','admin') NOT NULL,
        `level` enum('100lvl','200lvl','300lvl','400lvl') DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
    )",

    "CREATE TABLE IF NOT EXISTS `polls` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `question` text NOT NULL,
        `creator_id` int(11) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `creator_id` (`creator_id`),
        FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS `poll_options` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `poll_id` int(11) NOT NULL,
        `option_text` varchar(255) NOT NULL,
        PRIMARY KEY (`id`),
        KEY `poll_id` (`poll_id`),
        FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE
    )",

    "CREATE TABLE IF NOT EXISTS `votes` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `poll_id` int(11) NOT NULL,
        `option_id` int(11) NOT NULL,
        `user_id` int(11) NOT NULL,
        `voted_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `poll_id_user_id` (`poll_id`, `user_id`),
        KEY `option_id` (`option_id`),
        KEY `user_id` (`user_id`),
        FOREIGN KEY (`poll_id`) REFERENCES `polls` (`id`) ON DELETE CASCADE,
        FOREIGN KEY (`option_id`) REFERENCES `poll_options` (`id`) ON DELETE CASCADE,
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    )"
];

// Execute each query
foreach ($table_queries as $query) {
    if (!mysqli_query($conn, $query)) {
        echo "Error creating table: " . mysqli_error($conn);
    }
}

echo "Tables created successfully!";
?>

