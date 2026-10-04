<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering to catch any warnings/errors
ob_start();

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Include required files
try {
    if (!file_exists('transactionDAO.php')) {
        throw new Exception('transactionDAO.php not found');
    }
    require_once 'transactionDAO.php';
    
    if (!file_exists('Transaction.php')) {
        throw new Exception('Transaction.php not found');
    }
    require_once 'Transaction.php'; 
    
    if (!file_exists('auth.php')) {
        throw new Exception('auth.php not found');
    }
    require_once 'auth.php';
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Required file missing: ' . $e->getMessage()
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// Check if user is logged in
$userId = getCurrentUserId();

try {
    $dao = new TransactionDAO(); 
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    $errorMsg = $e->getMessage();
    
    // Log full error for debugging
    error_log("TransactionDAO Creation Failed: " . $errorMsg);
    error_log("Stack trace: " . $e->getTraceAsString());
    
    // Extract actual error message
    $actualError = $errorMsg;
    if (strpos($errorMsg, 'SQLSTATE') !== false) {
        // Parse PDO error
        if (preg_match('/SQLSTATE\[(\w+)\]:\s*(.+)/', $errorMsg, $matches)) {
            $actualError = $matches[2];
        }
    }
    
    // Provide helpful error message
    $userFriendlyMsg = $actualError;
    if (strpos($errorMsg, 'Database connection') !== false || strpos($errorMsg, 'SQLSTATE') !== false) {
        $userFriendlyMsg = 'Database connection failed. Error: ' . $actualError;
    }
    
    echo json_encode([
        'success' => false, 
        'message' => 'API Initialization Failed: Database connection error.', 
        'error' => $userFriendlyMsg,
        'detailed_error' => $errorMsg,
        'help' => 'Test connection with: test_connection.php or simple_test.php'
    ]);
    exit; 
}


if ($method === 'GET') {
    
    try {
        $transactions = $dao->getAllTransactions($userId);
        
        // Check if transactions is valid array
        if (!is_array($transactions)) {
            throw new Exception("getAllTransactions did not return an array. Returned: " . gettype($transactions));
        }
        
        $formatted_transactions = array_map(function($t) {
            if (!is_array($t)) {
                throw new Exception("Transaction item is not an array");
            }
            return [
                'id' => (string)($t['id'] ?? ''),
                'type' => $t['transaction_type'] ?? '',
                'description' => $t['description'] ?? '',
                'category' => $t['category'] ?? null,
                'amount' => (float)($t['amount'] ?? 0),
            ];
        }, $transactions);
        
        echo json_encode(['success' => true, 'data' => $formatted_transactions]);

    } catch (Exception $e) {
        error_log("GET Error: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to fetch transactions.', 
            'error' => $e->getMessage()
        ]);
        exit;
    }

} elseif ($method === 'POST') {
    
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['type']) || empty($data['description']) || !is_numeric($data['amount']) || $data['amount'] <= 0) {
        http_response_code(400); 
        echo json_encode(['success' => false, 'message' => 'Invalid or missing data.']);
        return;
    }

    $category = ($data['type'] === 'Income') ? null : ($data['category'] ?? null);

    try {
        $newTransaction = new Transaction(
            null, 
            $data['type'], 
            $data['description'], 
            $data['amount'], 
            $category
        );
        
        $result = $dao->addTransaction($newTransaction, $userId);

        if ($result) {
            http_response_code(201);
            echo json_encode(['success' => true, 'message' => 'Transaction added successfully.']);
        } else {
            throw new Exception("Database failed to execute insert query.");
        }

    } catch (Exception $e) {
        error_log("POST Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to add transaction.']);
    }
} elseif ($method === 'DELETE') {
    
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;
    
    if (empty($id) || !is_numeric($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing or invalid transaction ID.']);
        return;
    }

    try {
        $dao->deleteTransaction($id, $userId);
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Transaction deleted successfully.']);

    } catch (Exception $e) {
        error_log("DELETE Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete transaction.']);
    }
} else {
    http_response_code(405); 
    echo json_encode(['success' => false, 'message' => 'Method not supported.']);
}
?>
