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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the new password from the form
    $new_password = $_POST['new_password'];
    
    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update the user's password in the database
    $sql_update_password = "UPDATE quiz_users SET password = '$hashed_password' WHERE id = $user_id";
    if ($conn->query($sql_update_password) === TRUE) {
        // Password updated successfully
        $_SESSION['password_reset_success'] = true;
    } else {
        // Error updating password
        echo "Error: " . $conn->error;
    }
    
    // Close database connection
    $conn->close();
    
    // Redirect back to settings page
    header("Location: adm_settings.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1 class="text-center">Reset Password</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?user_id=$user_id"); ?>">
        <div class="form-group">
            <label for="new_password">New Password:</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Reset Password</button>
    </form>
</div>
</body>
</html>
