<?php
// List all databases to help find the correct name
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Listing All MySQL Databases</h2>";

try {
    $host = "localhost";
    $username = "root";
    $password = "";
    
    $dsn = "mysql:host=$host;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Found " . count($databases) . " databases:</h3>";
    echo "<ul>";
    foreach ($databases as $db) {
        $highlight = (strtolower($db) === 'php_expense_income_db') ? " <strong style='color: green;'>(This is the one!)</strong>" : "";
        echo "<li>$db$highlight</li>";
    }
    echo "</ul>";
    
    // Check if our database exists (case-insensitive)
    $found = false;
    foreach ($databases as $db) {
        if (strtolower($db) === 'php_expense_income_db') {
            $found = true;
            echo "<p style='color: green;'><strong>✅ Database found! Actual name: '$db'</strong></p>";
            break;
        }
    }
    
    if (!$found) {
        echo "<p style='color: red;'><strong>❌ Database 'php_expense_income_db' NOT found!</strong></p>";
        echo "<p>Please run complete_database.sql to create it.</p>";
    }
    
} catch (PDOException $e) {
    echo "❌ <strong>Error:</strong> " . $e->getMessage() . "<br>";
    echo "<p>Make sure MySQL is running in XAMPP.</p>";
}
?>
