<?php
// Ensure no accidental output is sent before session starts
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function middleware() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
        header("Location: user_login.html");
        exit(); // Always exit after header redirection
    }
}
?>
