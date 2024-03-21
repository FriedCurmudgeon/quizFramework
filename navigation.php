<?php
// Database connection
include './config/connect.php';

// Include language arrays
include './config/lang.php';

// Determine the language based on the session or set Norwegian as the default language
$language = isset($_SESSION['language']) && ($_SESSION['language'] == 'en') ? $english : $norwegian;

// Fetch the page title from quiz_settings table
$sql_page_title = "SELECT setting_value FROM quiz_settings WHERE setting_key = 'page_title'";
$result_page_title = $conn->query($sql_page_title);

if ($result_page_title->num_rows > 0) {
    $row = $result_page_title->fetch_assoc();
    $page_title = $row['setting_value'];
} else {
    $page_title = $language['langDefaultTitle']; // Set a default title if not found in the database
}

// Fetch the maintenance mode status and message from quiz_settings table
$sql_maintenance_mode = "SELECT setting_value FROM quiz_settings WHERE setting_key = 'isMaintMode'";
$result_maintenance_mode = $conn->query($sql_maintenance_mode);

if ($result_maintenance_mode->num_rows > 0) {
    $row = $result_maintenance_mode->fetch_assoc();
    $maintenance_mode = $row['setting_value'];
} else {
    $maintenance_mode = 0; // Set maintenance mode to off by default
}

// Fetch the maintenance mode messages for both English and Norwegian
$sql_maintenance_messages = "SELECT * FROM quiz_settings WHERE setting_key IN ('maint_mode_text_en', 'maint_mode_text_no')";
$result_maintenance_messages = $conn->query($sql_maintenance_messages);

// Initialize variables to store messages
$maintenance_message_en = "";
$maintenance_message_no = "";

// Process the results
if ($result_maintenance_messages->num_rows > 0) {
    while ($row = $result_maintenance_messages->fetch_assoc()) {
        if ($row['setting_key'] === 'maint_mode_text_en') {
            $maintenance_message_en = $row['setting_value'];
        } elseif ($row['setting_key'] === 'maint_mode_text_no') {
            $maintenance_message_no = $row['setting_value'];
        }
    }
}

// Determine the maintenance message based on the selected language
$maintenance_message = isset($_SESSION['language']) && ($_SESSION['language'] == 'en') ? $maintenance_message_en : $maintenance_message_no;

?>

<?php if ($maintenance_mode == 1): ?>
    <div class="alert alert-danger text-center" role="alert">
        <?php echo $maintenance_message; ?>
    </div>
<?php endif; ?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#"><?php echo $page_title; ?> <?php if ($isAdmin == 1): ?>- Admin<?php endif; ?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <!-- Left buttons -->
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <!-- Use a link to open the modal -->
                <a class="nav-link" href="#" data-toggle="modal" data-target="#userInfoModal"><?php echo $language['langNavGreeting']; ?> <?php echo $user_info['firstname'] . ' ' . $user_info['lastname']; ?></a>
            </li>
        </ul>
       <!-- Language selection dropdown -->
<form class="form-inline my-2 my-lg-0 mr-3" action="change_language.php" method="get">
    <select class="custom-select" name="lang" onchange="this.form.submit()">
        <option value="en" <?php if (isset($_SESSION['language']) && ($_SESSION['language'] == 'en')) echo ''; else echo 'selected'; ?>>English</option>
        <option value="no" <?php if (!isset($_SESSION['language']) || ($_SESSION['language'] == 'no')) echo 'selected'; ?>>Norwegian</option>
    </select>
</form>


        <!-- Right buttons -->
        <ul class="navbar-nav">
            <!-- Admin button (visible to admins only) -->
            <?php if ($isAdmin == 1): ?>
                <?php if ($pageID != 'quiz'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="quiz.php"><?php echo $language['langNavQuiz']; ?></a>
                    </li>
                <?php endif; ?>
                <?php if ($pageID != 'manageQuestions'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="adm_addQuestion.php"><?php echo $language['langNavAddQuestion']; ?></a>
                    </li>
                <?php endif; ?>
                <?php if ($pageID != 'submissions'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="adm_submissions.php"><?php echo $language['langNavSubmissions']; ?></a>
                    </li>
                <?php endif; ?>
                <?php if ($pageID != 'settings'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="adm_settings.php"><?php echo $language['langNavSettings']; ?></a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <button class="btn btn-outline-danger my-2 my-sm-0" type="submit" formaction="logout.php"><?php echo $language['langNavSignOut']; ?></button>
        </form>
    </div>
</nav>
