<?php
// Register a user
function register_user($school_id, $full_name, $email, $password, $user_type, $level = null) {
    global $conn;
    $email = mysqli_real_escape_string($conn, $email);
    $check_query = "SELECT id FROM users WHERE email = '$email'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        return false; // Email already exists
    }

    $query = "INSERT INTO users (school_id, full_name, email, password, user_type, level) 
              VALUES ('$school_id', '$full_name', '$email', '$password', '$user_type', '$level')";
    return mysqli_query($conn, $query);
}

function login_user($email, $password) {
    global $conn;
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password
        if (password_verify($password, $user['password'])) {
            return $user;
        }
    }
    return false;
}
