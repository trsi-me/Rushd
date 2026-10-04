<?php
// Simple connection test
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing Database Connection</h2>";

// Test 1: Check if PDO MySQL extension is available
echo "<h3>1. Checking PHP Extensions:</h3>";
if (extension_loaded('pdo_mysql')) {
    echo "✅ PDO MySQL extension is loaded<br>";
} else {
    echo "❌ PDO MySQL extension is NOT loaded!<br>";
    echo "You need to enable pdo_mysql in php.ini<br>";
}

// Test 2: Try to connect
echo "<h3>2. Testing Connection:</h3>";
try {
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "php_expense_income_db";
    
    echo "Attempting to connect with:<br>";
    echo "Host: $host<br>";
    echo "Username: $username<br>";
    echo "Password: " . (empty($password) ? "(empty)" : "***") . "<br>";
    echo "Database: $database<br><br>";
    
    $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ <strong>Connection successful!</strong><br><br>";
    
    // Test 3: Check if database exists
    echo "<h3>3. Checking Database:</h3>";
    $stmt = $pdo->query("SELECT DATABASE()");
    $currentDb = $stmt->fetchColumn();
    echo "Current database: <strong>$currentDb</strong><br>";
    
    // Test 4: Check if tables exist
    echo "<h3>4. Checking Tables:</h3>";
    $tables = ['users', 'transaction_table'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Table '$table' exists<br>";
            
            // Check columns
            $stmt = $pdo->query("SHOW COLUMNS FROM `$table`");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "&nbsp;&nbsp;Columns: " . implode(', ', $columns) . "<br>";
        } else {
            echo "❌ Table '$table' does NOT exist!<br>";
        }
    }
    
    // Test 5: Try a simple query
    echo "<h3>5. Testing Query:</h3>";
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM transaction_table");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "✅ Query successful! Found " . $result['count'] . " transactions<br>";
    } catch (Exception $e) {
        echo "❌ Query failed: " . $e->getMessage() . "<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ <strong>Connection failed!</strong><br>";
    echo "Error: " . $e->getMessage() . "<br><br>";
    
    echo "<h3>Common Solutions:</h3>";
    echo "<ul>";
    echo "<li>Check if MySQL is running in XAMPP</li>";
    echo "<li>Verify database name is correct: 'php_expense_income_db'</li>";
    echo "<li>Check username and password</li>";
    echo "<li>Try creating the database manually in phpMyAdmin</li>";
    echo "</ul>";
}

echo "<br><hr>";
echo "<h3>Next Steps:</h3>";
echo "<p>If connection is successful but api.php still fails, check:</p>";
echo "<ul>";
echo "<li>File permissions</li>";
echo "<li>PHP error logs</li>";
echo "<li>Check transactionDAO.php line 14 for correct credentials</li>";
echo "</ul>";
?>
