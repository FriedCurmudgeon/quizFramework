<?php
session_start();

// Database connection
include ('./config/connect.php');

// Retrieve form data
$username = $_POST['username'];
$password = $_POST['password'];

// Simple validation to prevent attacks
$username = htmlspecialchars($username);

// Check user credentials
$sql = "SELECT id, username, password FROM quiz_users WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Verify password
    if (password_verify($password, $row['password'])) {
        // Password is correct
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        header("Location: quiz.php");
        exit();
    } else {
        // Invalid password
        echo "Invalid username or password!";
    }
} else {
    // User not found
    echo "Invalid username or password!";
}

// Close database connection
$conn->close();
?>
