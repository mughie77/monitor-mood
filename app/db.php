<?php
// Include the configuration file
require_once __DIR__ . '/config.php';

// Create a new database connection
$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check for connection errors
if ($mysqli->connect_error) {
    // Stop execution and display an error message
    die('Database Connection Failed: ' . $mysqli->connect_error);
}

// Set the character set to utf8mb4 for full Unicode support
$mysqli->set_charset('utf8mb4');
?>
