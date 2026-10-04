<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
session_unset();
session_destroy();

header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
exit;
?>
