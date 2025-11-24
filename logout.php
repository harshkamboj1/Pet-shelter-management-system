<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Clear remember me cookies if they exist
if (isset($_COOKIE['remember_user']) && isset($_COOKIE['remember_token'])) {
    setcookie("remember_user", "", time() - 3600, "/");
    setcookie("remember_token", "", time() - 3600, "/");
}

// Redirect to the home page
header("Location: index.php");
exit();
?>