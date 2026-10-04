<?php
// Test MySQL connection WITHOUT database first
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing MySQL Connection (Without Database)</h2>";

try {
    $host = "localhost";
    $username = "root";
    $password = "";
    
    echo "Attempting to connect to MySQL server...<br>";
    echo "Host: $host<br>";
    echo "Username: $username<br>";
    echo "Password: " . (empty($password) ? "(empty)" : "***") . "<br><br>";
    
    // Try to connect WITHOUT specifying database
    $dsn = "mysql:host=$host;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ <strong>MySQL server connection successful!</strong><br><br>";
    
    // Check if database exists
    echo "<h3>Checking if database exists:</h3>";
    $stmt = $pdo->query("SHOW DATABASES LIKE 'php_expense_income_db'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Database 'php_expense_income_db' exists<br>";
        
        // Try to connect WITH database
        echo "<h3>Testing connection WITH database:</h3>";
        try {
            $dsn2 = "mysql:host=$host;dbname=php_expense_income_db;charset=utf8mb4";
            $pdo2 = new PDO($dsn2, $username, $password);
            $pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "✅ Connection to database successful!<br>";
            
            // Check tables
            $stmt = $pdo2->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "Tables found: " . (count($tables) > 0 ? implode(', ', $tables) : "None") . "<br>";
            
        } catch (PDOException $e) {
            echo "❌ Cannot connect to database: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "❌ Database 'php_expense_income_db' does NOT exist!<br>";
        echo "<strong>SOLUTION:</strong> Run complete_database.sql in phpMyAdmin<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ <strong>MySQL server connection failed!</strong><br>";
    echo "Error Code: " . $e->getCode() . "<br>";
    echo "Error Message: " . $e->getMessage() . "<br><br>";
    
    echo "<h3>SOLUTION:</h3>";
    echo "<ol>";
    echo "<li>Open <strong>XAMPP Control Panel</strong></li>";
    echo "<li>Click <strong>Start</strong> next to <strong>MySQL</strong></li>";
    echo "<li>Wait until status shows <strong>Running</strong></li>";
    echo "<li>Refresh this page</li>";
    echo "</ol>";
}
?>
