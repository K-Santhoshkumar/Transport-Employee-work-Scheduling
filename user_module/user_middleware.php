<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function middleware() {
    // Check if user is logged in
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
        // Clear any existing session data
        session_unset();
        session_destroy();
        
        // Redirect to login page
        header('Location: user_login.html');
        exit();
    }
    
    // Optional: Check if session is still valid (you can add timestamp checking here)
    // For now, we'll just ensure the session variables exist
}
?>