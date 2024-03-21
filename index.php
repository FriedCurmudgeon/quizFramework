<?php
session_start();

// Redirect logged-in users to the appropriate page based on isAdmin value
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['isAdmin'] == 1) {
        header("Location: adm_addQuestion.php");
    } else {
        header("Location: quiz.php");
    }
    exit();
}

// Get username from URL parameter
$username = isset($_GET['username']) ? $_GET['username'] : '';

// Debug message to display retrieved username
//echo "DEBUG: Retrieved username: $username";

if (empty($username)) {
    $error_message = "Fant ikke brukernavn i URL!<br>Opprett en konto om du ikke har 😊";
} else {
    $error_message = "";
}

if (isset($_POST['password'])) {
    // Retrieve form data
    $password = $_POST['password'];

    // Database connection
    include ('./config/connect.php');

    // fetch page title and favicon from db
    include ('./config/fetch_sitewide_content_db.php');

    // Simple validation to prevent attacks
    $username = htmlspecialchars($username);

    // Check user credentials
    $sql = "SELECT id, username, password, isAdmin FROM quiz_users WHERE username = '$username'";
    $result = $conn->query($sql);

    // Debug message to display SQL query
    // echo "DEBUG: SQL query: $sql";

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $row['password'])) {
            // Password is correct
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['isAdmin'] = $row['isAdmin'];
            header("Location: quiz.php");
            exit();
        } else {
            // Invalid password
            $error_message = "Feil passord!";
        }
    } else {
        // User not found
        $error_message = "Fant ikke bruker!";
    }

    // Close database connection
    $conn->close();
}

 // Database connection
 include ('./config/connect.php');

 // fetch page title and favicon from db
 include ('./config/fetch_sitewide_content_db.php');

 // Close database connection
 $conn->close();
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Sign in</title>
    <link rel="icon" href="data:image/svg+xml,<?php echo htmlspecialchars($favicon_url); ?>">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="registration-box">
                    <h1 class="text-center">🐣 Påskequiz</h1>
                    <h2>Logg inn</h2><br>
                    <!-- Hide username field and utilize username from URL -->
                    <?php if (!empty($error_message)): ?>
                        <p style="color: red;"><?php echo $error_message; ?></p>
                    <?php endif; ?>
                    <form action="" method="post">
                        <!-- Display username in the login form if available -->
                        <?php if (!empty($username)): ?>
                            <p>Din unike ID er: <b><?php echo $username; ?></b></p>
                            <input type="hidden" name="username" value="<?php echo $username; ?>">
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="password">Passord:</label><br>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Logg inn</button>
                    </form>
                    <p>Har du ikke konto? <a href="registration.php">Lag en her</a>.</p>
                </div>
                <p><center><small>Trim Nøtta versjon 0.9 - 2024</small></center></p>
            </div>
        </div>
    </div>

    <!-- Cookie information bar -->
    <?php include ('cookieInfo.php'); ?>
</body>
</html>