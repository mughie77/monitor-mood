<?php
require_once 'app/functions.php';

start_session();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page
redirect('login');
?>
