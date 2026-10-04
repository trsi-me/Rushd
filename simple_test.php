<?php
// Very simple test - just try to connect
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing...<br>";

try {
    require_once 'database_connection.php';
    $db = new DatabaseConnection("localhost", "root", "", "php_expense_income_db");
    echo "✅ Connection OK!<br>";
    
    // Try a simple query
    $result = $db->fetchAll("SELECT 1 as test");
    echo "✅ Query OK!<br>";
    
    // Check table
    $result = $db->fetchAll("SHOW TABLES LIKE 'transaction_table'");
    if (count($result) > 0) {
        echo "✅ Table exists!<br>";
    } else {
        echo "❌ Table NOT found!<br>";
    }
    
} catch (Exception $e) {
    echo "❌ <strong>ERROR:</strong><br>";
    $errorMsg = $e->getMessage();
    // Replace newlines with <br> for HTML display
    $errorMsg = nl2br(htmlspecialchars($errorMsg));
    echo "<div style='background: #fee; padding: 15px; border: 1px solid #fcc; border-radius: 5px; margin: 10px 0;'>";
    echo $errorMsg;
    echo "</div>";
    
    echo "<h3>Quick Fix:</h3>";
    echo "<ol>";
    echo "<li>Open <strong>XAMPP Control Panel</strong></li>";
    echo "<li>Click <strong>Start</strong> next to <strong>MySQL</strong></li>";
    echo "<li>Wait until it shows <strong>Running</strong> (green)</li>";
    echo "<li>Open <strong>phpMyAdmin</strong> (http://localhost/phpmyadmin)</li>";
    echo "<li>Click <strong>Import</strong> tab</li>";
    echo "<li>Select file: <strong>complete_database.sql</strong></li>";
    echo "<li>Click <strong>Go</strong></li>";
    echo "<li>Refresh this page</li>";
    echo "</ol>";
}
?>
