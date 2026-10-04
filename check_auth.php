<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();

header('Content-Type: application/json');

try {
    require_once 'auth.php';
    
    if (isLoggedIn()) {
        echo json_encode([
            'loggedIn' => true,
            'username' => getCurrentUsername(),
            'userId' => getCurrentUserId()
        ]);
    } else {
        echo json_encode([
            'loggedIn' => false
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'loggedIn' => false,
        'error' => 'Auth check failed'
    ]);
}
?>
