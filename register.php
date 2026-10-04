<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();

header('Content-Type: application/json');

require_once 'database_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

$username = trim($data['username']);
$email = trim($data['email']);
$password = $data['password'];

// Validation
if (strlen($username) < 3) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username must be at least 3 characters']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit;
}

try {
    $db = new DatabaseConnection("localhost", "root", "", "php_expense_income_db");
    
    // Check if username or email already exists
    $checkSql = "SELECT id FROM users WHERE username = ? OR email = ?";
    $checkStmt = $db->getPdo()->prepare($checkSql);
    $checkStmt->execute([$username, $email]);
    
    if ($checkStmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Username or email already exists']);
        exit;
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $insertSql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $insertStmt = $db->getPdo()->prepare($insertSql);
    $insertStmt->execute([$username, $email, $hashedPassword]);
    
    $userId = $db->lastInsertId();
    
    // Auto login after registration
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    
    http_response_code(201);
    echo json_encode([
        'success' => true, 
        'message' => 'Registration successful',
        'user' => [
            'id' => $userId,
            'username' => $username
        ]
    ]);
} catch (Exception $e) {
    error_log("Register Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
}
?>
