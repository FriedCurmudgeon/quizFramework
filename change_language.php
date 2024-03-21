<?php
session_start();

// Check if language parameter is set in the URL
if(isset($_GET['lang'])) {
    // Get the selected language from the URL parameter
    $selected_lang = $_GET['lang'];
    
    // Check if the selected language is supported
    if($selected_lang == 'en' || $selected_lang == 'no') {
        // Set the selected language in the session
        $_SESSION['language'] = $selected_lang;
    }
}

// Redirect back to the previous page or homepage
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>
