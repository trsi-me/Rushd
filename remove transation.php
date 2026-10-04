<?php
require_once 'transactionDAO.php';

header('Content-Type: application/json');

// Retrieve JSON data from the client
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

$id = $data['transactionId'] ?? null;

if (empty($id) || !is_numeric($id)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing or invalid transaction ID.']);
    exit;
}

try {
    $transactionDao = new TransactionDAO();
    $transactionDao->deleteTransaction($id);
    
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Transaction removed successfully.']);
} catch (Exception $e) {
    error_log("DELETE Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete transaction.']);
}
?>