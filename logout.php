<?php
session_start();
// Get username from session if available
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
// Unset all session variables
$_SESSION = array();
// Destroy the session
session_destroy();
// Redirect to the login page with the username included
header("Location: index.php?username=$username");
exit();
?>