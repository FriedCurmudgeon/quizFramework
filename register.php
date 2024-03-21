<?php
session_start();

// Database connection
include ('./config/connect.php');

// Retrieve form data
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Simple validation to prevent attacks
$firstname = htmlspecialchars($firstname);
$lastname = htmlspecialchars($lastname);

// Check if user already exists with the same first name and last name
$sql_check = "SELECT * FROM quiz_users WHERE firstname = '$firstname' AND lastname = '$lastname'";
$result_check = $conn->query($sql_check);
if ($result_check->num_rows > 0) {
    // Set registration error and redirect back to registration page
    $_SESSION['registration_error'] = "A user with the same <strong>first name</strong> and <strong>last name</strong> already exists. Please choose a different name.";
    header("Location: registration.php");
    exit();
}

// Generate a unique username
$unique_username = uniqid();

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user into the database
$sql = "INSERT INTO quiz_users (username, firstname, lastname, password) VALUES ('$unique_username', '$firstname', '$lastname', '$hashed_password')";
if ($conn->query($sql) === TRUE) {
    $_SESSION['user_id'] = $conn->insert_id;
    $_SESSION['username'] = $unique_username;

    // Redirect to registration success page
    header("Location: registration_success.php?username=$unique_username");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close database connection
$conn->close();
?>
