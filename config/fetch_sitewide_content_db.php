<?php
// Fetch the page title and favicon URL from quiz_settings table
$sql_settings = "SELECT setting_key, setting_value FROM quiz_settings";
$result_settings = $conn->query($sql_settings);
$settings = [];
while ($row = $result_settings->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}


// Fetch the page title from quiz_settings table
$page_title = "Default Title"; // Default title in case setting is not found
if (isset($settings['page_title'])) {
    $page_title = $settings['page_title'];
}

// Fetch the favicon URL from quiz_settings table
$favicon_url = isset($settings['favicon_url']) ? $settings['favicon_url'] : '';
?>