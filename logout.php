<?php
session_start();

// Clear all session data
$_SESSION = [];
session_unset();
session_destroy();

// Prevent browser caching old session
header("Cache-Control: no-cache, must-revalidate");
header("Expires: 0");

// Redirect to login
header("Location: login.php");
exit();
?>
