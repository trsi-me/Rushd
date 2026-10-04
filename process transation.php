<?php
require_once 'Transaction.php';
require_once 'transactionDAO.php';

header('Content-Type: application/json');

// Retrieve JSON data from the client
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

if (empty($data['type']) || empty($data['description']) || !is_numeric($data['amount']) || $data['amount'] <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing data.']);
    exit;
}

$category = ($data['type'] === 'Income') ? null : ($data['category'] ?? null);

try {
    // Create a new Transaction object
    $newTransaction = new Transaction(null, $data['type'], $data['description'], $data['amount'], $category);
    
    // Create a new TransactionDAO
    $transactionDAO = new TransactionDAO();
    
    // Add the new transaction to the database
    $result = $transactionDAO->addTransaction($newTransaction);
    
    if ($result) {
        http_response_code(201);
        echo json_encode(['status' => 'success', 'message' => 'Transaction added successfully.']);
    } else {
        throw new Exception("Database failed to execute insert query.");
    }
} catch (Exception $e) {
    error_log("POST Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to add transaction.']);
}
?>