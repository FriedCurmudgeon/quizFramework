<?php
session_start();

// Check if the user is logged in and if isAdmin is set in the session
if (!isset($_SESSION['user_id']) || $_SESSION['isAdmin'] != 1) {
    header("Location: index.php");
    exit();
}

// Check if the user is an admin
$isAdmin = $_SESSION['isAdmin'];

// Check if user_id is provided in the URL
if (!isset($_GET['user_id'])) {
    header("Location: adm_settings.php");
    exit();
}

// Database connection
include ('./config/connect.php');

// Get the user ID from the URL parameter
$user_id = $_GET['user_id'];

// Check if the user exists
$sql_check_user = "SELECT id FROM quiz_users WHERE id = $user_id";
$result_check_user = $conn->query($sql_check_user);

if ($result_check_user->num_rows == 0) {
    // User does not exist, redirect
    header("Location: adm_settings.php");
    exit();
}

// Delete the user from the database
$sql_delete_user = "DELETE FROM quiz_users WHERE id = $user_id";
$conn->query($sql_delete_user);

// Close database connection
$conn->close();

// Redirect back to settings page with a success message
header("Location: adm_settings.php?user_deleted=true");
exit();
