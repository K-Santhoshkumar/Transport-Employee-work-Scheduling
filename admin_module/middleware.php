<?php
session_start(); // Start the session

function middleware() {
    if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_email'])) {
        header('Location: admin_login.html');
        exit(); // Stop further execution after redirection
    }
}
?>
