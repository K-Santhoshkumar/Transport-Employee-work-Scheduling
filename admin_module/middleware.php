<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function middleware() {
    // Check if admin is logged in
    if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_email'])) {
        // Clear any existing session data
        session_unset();
        session_destroy();
        
        // Redirect to login page
        header('Location: admin_login.html');
        exit();
    }
    
    // Optional: Check if session is still valid (you can add timestamp checking here)
    // For now, we'll just ensure the session variables exist
}
?>