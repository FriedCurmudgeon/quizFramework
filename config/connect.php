<?php
$db_servername = "localhost";
$db_username = "username";
$db_password = "pass";
$dbname = "dbname";

$conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>