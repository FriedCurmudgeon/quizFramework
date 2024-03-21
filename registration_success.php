<?php
session_start();

// Check if user is logged in and if username is provided in the URL
if (!isset($_SESSION['user_id']) || !isset($_GET['username'])) {
    header("Location: index.php");
    exit();
}

// Database connection
include ('./config/connect.php');

// fetch page title and favicon from db
include ('./config/fetch_sitewide_content_db.php');

// Close database connection
$conn->close();

// Get the username from the URL
$username = $_GET['username'];
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Registrering vellykket</title>
    <link rel="icon" href="data:image/svg+xml,<?php echo htmlspecialchars($favicon_url); ?>">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 50px;
        }
        .registration-box {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="registration-box">
                    <h2 class="text-center">Registrering vellykket</h2>
                    <p>Du kan nå logge inn med din unike innloggins-lenke:</p>
                    <p><strong><span class='text-success'>https://tangeland.net/quiz/index.php?username=<?php echo $username; ?></span></strong></p>
                    <p>Husk å lagre denne på et sikkert sted. Du <strong>trenger</strong> denne for å logge deg inn senere.</p>
                    <a href="index.php" class="btn btn-primary btn-block">Logg inn</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
