<?php
// Test file to diagnose the issue
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "Testing API...\n\n";

// Test 1: Check if files exist
echo "1. Checking required files...\n";
$files = ['transactionDAO.php', 'Transaction.php', 'auth.php', 'database_connection.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "   ✓ $file exists\n";
    } else {
        echo "   ✗ $file MISSING!\n";
    }
}

// Test 2: Check database connection
echo "\n2. Testing database connection...\n";
try {
    require_once 'database_connection.php';
    $db = new DatabaseConnection("localhost", "root", "", "php_expense_income_db");
    echo "   ✓ Database connection successful\n";
} catch (Exception $e) {
    echo "   ✗ Database connection failed: " . $e->getMessage() . "\n";
}

// Test 3: Check if table exists
echo "\n3. Checking transaction_table...\n";
try {
    $db = new DatabaseConnection("localhost", "root", "", "php_expense_income_db");
    $result = $db->fetchAll("SHOW TABLES LIKE 'transaction_table'");
    if (count($result) > 0) {
        echo "   ✓ transaction_table exists\n";
    } else {
        echo "   ✗ transaction_table does NOT exist!\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Test 4: Check if user_id column exists
echo "\n4. Checking user_id column...\n";
try {
    $db = new DatabaseConnection("localhost", "root", "", "php_expense_income_db");
    $result = $db->fetchAll("SHOW COLUMNS FROM transaction_table LIKE 'user_id'");
    if (count($result) > 0) {
        echo "   ✓ user_id column exists\n";
    } else {
        echo "   ✗ user_id column does NOT exist - will try to add it...\n";
        try {
            $db->addColumnIfNotExists('transaction_table', 'user_id', '`user_id` int(11) NULL AFTER `category`');
            echo "   ✓ user_id column added successfully\n";
        } catch (Exception $e2) {
            echo "   ✗ Failed to add column: " . $e2->getMessage() . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Test 5: Test TransactionDAO
echo "\n5. Testing TransactionDAO...\n";
try {
    require_once 'transactionDAO.php';
    $dao = new TransactionDAO();
    echo "   ✓ TransactionDAO created successfully\n";
    
    // Try to get transactions
    $transactions = $dao->getAllTransactions(null);
    echo "   ✓ getAllTransactions works. Found " . count($transactions) . " transactions\n";
} catch (Exception $e) {
    echo "   ✗ TransactionDAO error: " . $e->getMessage() . "\n";
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
}

// Test 6: Test API endpoint simulation
echo "\n6. Testing API endpoint (GET request simulation)...\n";
try {
    session_start();
    require_once 'auth.php';
    require_once 'transactionDAO.php';
    
    $userId = getCurrentUserId();
    echo "   User ID: " . ($userId ?? 'null (not logged in)') . "\n";
    
    $dao = new TransactionDAO();
    $transactions = $dao->getAllTransactions($userId);
    
    $formatted = array_map(function($t) {
        return [
            'id' => (string)($t['id'] ?? ''),
            'type' => $t['transaction_type'] ?? '',
            'description' => $t['description'] ?? '',
            'category' => $t['category'] ?? null,
            'amount' => (float)($t['amount'] ?? 0),
        ];
    }, $transactions);
    
    $json = json_encode(['success' => true, 'data' => $formatted]);
    if ($json === false) {
        echo "   ✗ JSON encoding failed: " . json_last_error_msg() . "\n";
    } else {
        echo "   ✓ API simulation successful\n";
        echo "   Response length: " . strlen($json) . " bytes\n";
    }
} catch (Exception $e) {
    echo "   ✗ API simulation failed: " . $e->getMessage() . "\n";
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n\nTest completed!\n";
?>
