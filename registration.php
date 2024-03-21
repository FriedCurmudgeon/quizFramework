<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: quiz.php");
    exit();
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
    <title><?php echo $page_title; ?> - Bruker registrering</title>
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
                    <h1 class="text-center">Registrering</h1>
                    <?php
                    // Check for any registration-related errors
                    if (isset($_SESSION['registration_error'])) {
                        echo '<div class="alert alert-danger" role="alert">' . $_SESSION['registration_error'] . '</div>';
                        unset($_SESSION['registration_error']);
                    }
                    ?>
                    <form id="registrationForm" action="register.php" method="post">
                        <div class="form-group">
                            <label for="firstname">Fornavn:</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" required>
                        </div>
                        <div class="form-group">
                            <label for="lastname">Etternavn:</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Passord:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Bekreft passord:</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <small id="passwordMatchError" class="text-danger"></small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Registrer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for password confirmation -->
    <script>
        document.getElementById('registrationForm').addEventListener('submit', function(event) {
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                document.getElementById('passwordMatchError').textContent = 'Passordene er ikke like';
                event.preventDefault(); // Prevent form submission
            } else {
                document.getElementById('passwordMatchError').textContent = '';
            }
        });
    </script>
</body>
</html>
