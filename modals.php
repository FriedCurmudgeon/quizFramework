<?php
// Include database connection
include './config/connect.php';

// Determine the language based on the session or set a default language
include './config/lang.php';
$language = isset($_SESSION['language']) && ($_SESSION['language'] == 'no') ? $norwegian : $english;

// Initialize isAdmin variable
$isAdmin = isset($_SESSION['isAdmin']) ? $_SESSION['isAdmin'] : false;

// Define isAdminCheck
$isAdminCheck = $isAdmin ? '<strong>' . $language['langYouAreAdmin'] . '</strong>' : '';

// User Information Modal Content

// Fetch the user greeting from quiz_settings table
$user_greeting = $language['langNavGreeting']; // Default greeting if not found in the database
$sql_user_greeting = "SELECT setting_value FROM quiz_settings WHERE setting_key = 'user_greeting'";
$result_user_greeting = $conn->query($sql_user_greeting);

if ($result_user_greeting->num_rows > 0) {
    $row = $result_user_greeting->fetch_assoc();
    $user_greeting = $row['setting_value'];
}

$userInfoModalContent = "
<!-- User Information Modal -->
<div class='modal fade' id='userInfoModal' tabindex='-1' role='dialog' aria-labelledby='userInfoModalLabel' aria-hidden='true'>
    <div class='modal-dialog' role='document'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='userInfoModalLabel'>" . $language['langUserInfoTitle'] . "</h5>
                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                    <span aria-hidden='true'>&times;</span>
                </button>
            </div>
            <div class='modal-body'>
                <!-- Call the function to generate modal content -->
                <p>" . $user_greeting . "<p/>
                <p>" . $language['langUserInfoName'] . " ". $user_info['firstname'] . " " . $user_info['lastname'] . "</p>
            <p>" . $language['langUserInfoID'] . " ". $user_info['username'] . "</p>
            <!-- Add more user information if needed -->
            <p>" . $isAdminCheck . "</p>
            <hr>
            <p><small>Trim Nøtta versjon 0.9 - 2024</small></p>
            </div>
        </div>
    </div>
</div>";

$successModal = "
<!-- Success modal -->
<div class='modal fade' id='successModal' tabindex='-1' role='dialog' aria-labelledby='successModalLabel' aria-hidden='true'>
    <div class='modal-dialog' role='document'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='successModalLabel'>" . $language['langSuccess'] . "</h5>
                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                    <span aria-hidden='true'>&times;</span>
                </button>
            </div>
            <div class='modal-body'>
                <p>" . $language['langQuizSuccess'] . "</p>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-secondary' data-dismiss='modal'>" . $language['langOK'] . "</button>
            </div>
        </div>
    </div>
</div>";

$editSuccessModal = "
<!-- Edit Success modal -->
<div class='modal fade' id='editSuccessModal' tabindex='-1' role='dialog' aria-labelledby='editSuccessModalLabel' aria-hidden='true'>
    <div class='modal-dialog' role='document'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='editSuccessModalLabel'>" . $language['langSuccess'] . "</h5>
                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                    <span aria-hidden='true'>&times;</span>
                </button>
            </div>
            <div class='modal-body'>
                <p>" . $language['langEditQuestionFormLabel'] . "</p>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-secondary' data-dismiss='modal'>" . $language['langOK'] . "</button>
            </div>
        </div>
    </div>
</div>";

$addSuccessModal = "
<!-- Add Success modal -->
<div class='modal fade' id='addSuccessModal' tabindex='-1' role='dialog' aria-labelledby='addSuccessModalLabel' aria-hidden='true'>
    <div class='modal-dialog' role='document'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='addSuccessModalLabel'>" . $language['langSuccess'] . "</h5>
                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                    <span aria-hidden='true'>&times;</span>
                </button>
            </div>
            <div class='modal-body'>
                <p>" . $language['langAddQuestionFormLabel'] . "</p>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-secondary' data-dismiss='modal'>" . $language['langOK'] . "</button>
            </div>
        </div>
    </div>
</div>";

$deleteSuccessModal = "
<!-- Delete Success modal -->
<div class='modal fade' id='deleteSuccessModal' tabindex='-1' role='dialog' aria-labelledby='deleteSuccessModalLabel' aria-hidden='true'>
    <div class='modal-dialog' role='document'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='deleteSuccessModalLabel'>" . $language['langSuccess'] . "</h5>
                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                    <span aria-hidden='true'>&times;</span>
                </button>
            </div>
            <div class='modal-body'>
                <p>" . $language['langDeleteQuestionButton'] . "</p>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-secondary' data-dismiss='modal'>" . $language['langOK'] . "</button>
            </div>
        </div>
    </div>
</div>";

?>