<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

// Get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Get current username
function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}

// Require login - redirect to login page if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: rushd login.html');
        exit;
    }
}

// Logout function
function logout() {
    session_start();
    session_unset();
    session_destroy();
    header('Location: rushd login.html');
    exit;
}
?>
