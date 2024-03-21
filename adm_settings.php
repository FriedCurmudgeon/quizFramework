<?php
session_start();

// Include language arrays
include './config/lang.php';

// Determine the language based on the session or set Norwegian as the default language
$language = isset($_SESSION['language']) && ($_SESSION['language'] == 'en') ? $english : $norwegian;

// Check if the user is logged in and if isAdmin is set in the session
if (!isset($_SESSION['user_id']) || $_SESSION['isAdmin'] != 1) {
    header("Location: index.php");
    exit();
}

// Check if the user is an admin
$isAdmin = $_SESSION['isAdmin'];

// Database connection
include ('./config/connect.php');

// Fetch user information
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT firstname, lastname, username FROM quiz_users WHERE id = $user_id";
$result_user = $conn->query($sql_user);
$user_info = $result_user->fetch_assoc();

// Fetch global settings
$sql_settings = "SELECT * FROM quiz_settings";
$result_settings = $conn->query($sql_settings);
$settings = [];
while ($row = $result_settings->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Update global settings
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST as $key => $value) {
        $sql_update = "UPDATE quiz_settings SET setting_value = '$value' WHERE setting_key = '$key'";
        $conn->query($sql_update);
    }
    // Redirect to avoid resubmission
    header("Location: adm_settings.php");
    exit();
}

// Fetch the favicon URL from quiz_settings table
$favicon_url = isset($settings['favicon_url']) ? $settings['favicon_url'] : '';


// Fetch all users
$sql_users = "SELECT id, firstname, lastname, username FROM quiz_users";
$result_users = $conn->query($sql_users);

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
    <title><?php echo $page_title; ?> - Settings</title>
    <link rel="icon" href="data:image/svg+xml,<?php echo htmlspecialchars($favicon_url); ?>">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php $pageID = 'settings'; ?>
<!-- Include modals -->    
<?php include 'modals.php'; ?>

<?php include 'navigation.php' ?>

<div class="container">
    <h1 class="text-center"><?php echo $language['langSettingsPageTitle']; ?></h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <label for="page_title"><?php echo $language['langPageTitleSetting']; ?></label>
            <input type="text" class="form-control" id="page_title" name="page_title" value="<?php echo $setting_title = isset($settings['page_title']) ? $settings['page_title'] : ''; ?>">
            <small id="pageTitleHelp" class="form-text text-muted"><?php echo $language['langPageTitleHelp']; ?></small>
        </div>
        <div class="form-group">
            <label for="favicon_url"><?php echo $language['langFaviconURL']; ?></label>
            <input type="text" class="form-control" id="favicon_url" name="favicon_url" value="<?php echo isset($settings['favicon_url']) ? $settings['favicon_url'] : ''; ?>">
            <small id="faviconUrlHelp" class="form-text text-muted"><?php echo $language['langFaviconURLHelp']; ?></small>
        </div>
        <div class="form-group">
            <label for="quiz_name"><?php echo $language['langQuizName']; ?></label>
            <input type="text" class="form-control" id="quiz_name" name="quiz_name" value="<?php echo $settings_quiz_name = isset($settings['quiz_name']) ? $settings['quiz_name'] : ''; ?>">
            <small id="quizNameHelp" class="form-text text-muted"><?php echo $language['langQuizNameHelp']; ?></small>
        </div>
        <div class="form-group">
            <label for="user_greeting"><?php echo $language['langUserGreeting']; ?></label>
            <input type="text" class="form-control" id="user_greeting" name="user_greeting" value="<?php echo $settings_user_greeting = isset($settings['user_greeting']) ? $settings['user_greeting'] : ''; ?>">
            <small id="userGreetingHelp" class="form-text text-muted"><?php echo $language['langUserGreetingHelp']; ?></small>
        </div>
        <div class="form-group">
            <label for="welcome_message"><?php echo $language['langWelcomeMessage']; ?></label>
            <textarea class="form-control" id="welcome_message" name="welcome_message"><?php echo isset($settings['welcome_message']) ? $settings['welcome_message'] : ''; ?></textarea>
            <small id="welcomeMessageHelp" class="form-text text-muted"><?php echo $language['langWelcomeMessageHelp']; ?></small>
        </div>
        <div class="form-group">
            <label for="isMaintMode"><?php echo $language['langMaintMode']; ?></label>
            <select class="form-control" id="isMaintMode" name="isMaintMode">
                <option value="1" <?php if(isset($settings['isMaintMode']) && $settings['isMaintMode'] == 1) echo 'selected'; ?>><?php echo $language['langMaintModOn']; ?></option>
                <option value="0" <?php if(isset($settings['isMaintMode']) && $settings['isMaintMode'] == 0) echo 'selected'; ?>><?php echo $language['langMaintModeOff']; ?></option>
            </select>
            <small id="isMaintModeHelp" class="form-text text-muted"><?php echo $language['langMaintModeHelp']; ?></small>
        </div>
        <div class="form-group">
            <label for="maint_mode_text"><?php echo $language['langMaintModeText']; ?></label>
            <textarea class="form-control" id="maint_mode_text" name="maint_mode_text"><?php echo isset($settings['maint_mode_text_en']) ? $settings['maint_mode_text_en'] : ''; ?></textarea>
            <textarea class="form-control" id="maint_mode_text" name="maint_mode_text"><?php echo isset($settings['maint_mode_text_no']) ? $settings['maint_mode_text_no'] : ''; ?></textarea>
            <small id="maint_mode_textHelp" class="form-text text-muted"><?php echo $language['langMaintModeTextHelp']; ?></small>
        </div>

        <!-- div class="form-group">
            <label for="language">< ?php echo $language['langLanguage']; ?></label>
            <select class="form-control" id="language" name="language" onchange="this.form.submit()">
                <option value="en" < ?php echo ($_SESSION['language'] == 'en') ? 'selected' : ''; ?>>< ?php echo $language['langEnglish']; ?></option>
                <option value="no" < ?php echo ($_SESSION['language'] == 'no') ? 'selected' : ''; ?>>< ?php echo $language['langNorwegian']; ?></option>
            </select>
        </div -->
        <button type="submit" class="btn btn-primary"><?php echo $language['langSaveSettings']; ?></button>
    </form>

    <hr>

    <h2 class="text-center"><?php echo $language['langRegisteredUsers']; ?></h2>
    <table class="table">
        <thead>
            <tr>
                <th><?php echo $language['langFullName']; ?></th>
                <th><?php echo $language['langUsername']; ?></th>
                <th><?php echo $language['langAction']; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_users->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td>
                        <a href="reset_password.php?user_id=<?php echo $row['id']; ?>" class="btn btn-primary"><?php echo $language['langResetPassword']; ?></a>
                        <a href="delete_user.php?user_id=<?php echo $row['id']; ?>" class="btn btn-danger"><?php echo $language['langDeleteUser']; ?></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Success Modal -->
<div class="modal" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel"><?php echo $language['langSuccess']; ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo $language['langPasswordReset']; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="window.location.href='adm_settings.php'"><?php echo $language['langOK']; ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Load user information Modal -->            
<?php echo $userInfoModalContent; ?>

<!-- Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php
// Check if the password reset was successful and display the success modal
if (isset($_SESSION['password_reset_success']) && $_SESSION['password_reset_success'] === true) {
    echo '<script>$("#successModal").modal("show");</script>';
    // Reset the session variable
    $_SESSION['password_reset_success'] = false;
}
?>
</body>
</html>
