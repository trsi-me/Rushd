<?php
error_reporting(0);
ini_set('display_errors', 0);
class DatabaseConnection{
    private $host;
    private $username;
    private $password;
    private $database;
    private $charset;
    private $pdo; 

    public function __construct($host, $username, $password, $database, $charset = "utf8mb4")
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->charset = $charset;

        $this->connect();
    }
    
    private function connect(){ 
        // First try to connect to MySQL server without database
        try {
            $dsn_no_db = "mysql:host={$this->host};charset={$this->charset}";
            $temp_pdo = new PDO($dsn_no_db, $this->username, $this->password);
            $temp_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Check if database exists (case-insensitive)
            $stmt = $temp_pdo->query("SHOW DATABASES");
            $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $dbExists = false;
            $actualDbName = null;
            
            foreach ($databases as $db) {
                if (strtolower($db) === strtolower($this->database)) {
                    $dbExists = true;
                    $actualDbName = $db; // Get actual case-sensitive name
                    break;
                }
            }
            
            if (!$dbExists) {
                $errorMsg = "Database '{$this->database}' does not exist!\n";
                $errorMsg .= "Available databases: " . implode(', ', $databases) . "\n";
                $errorMsg .= "SOLUTION: Run complete_database.sql to create the database.\n";
                error_log($errorMsg);
                throw new Exception($errorMsg);
            }
            
            // Use actual database name (case-sensitive)
            $this->database = $actualDbName;
            
        } catch (PDOException $e) {
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();
            
            $errorMsg = "MySQL server connection failed!\n";
            $errorMsg .= "Error Code: {$errorCode}\n";
            $errorMsg .= "Error Message: {$errorMessage}\n";
            
            if ($errorCode == 2002 || strpos($errorMessage, 'Connection refused') !== false) {
                $errorMsg .= "SOLUTION: MySQL server is not running. Start MySQL in XAMPP Control Panel.\n";
            } elseif ($errorCode == 1045) {
                $errorMsg .= "SOLUTION: Wrong username or password.\n";
            }
            
            error_log($errorMsg);
            throw new Exception($errorMsg);
        }
        
        // Now connect to the specific database
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();
            
            $errorMsg = "Database connection failed!\n";
            $errorMsg .= "Error Code: {$errorCode}\n";
            $errorMsg .= "Error Message: {$errorMessage}\n";
            $errorMsg .= "Database: {$this->database}\n";
            
            error_log($errorMsg);
            throw new Exception($errorMsg);
        }
    }
  
    public function getPdo(){
        return $this->pdo;
    }

    public function query($sql, $params = []){
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database Query Error: " . $e->getMessage() . " | SQL: " . $sql);
            throw new Exception("Database query failed: " . $e->getMessage());
        }
    }

    public function fetchAll($sql, $params = []){
        try {
            $stmt = $this->query($sql, $params);
            $result = $stmt->fetchAll();
            return $result !== false ? $result : [];
        } catch (Exception $e) {
            error_log("fetchAll Error: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
    
    // Check if column exists in table
    public function columnExists($tableName, $columnName) {
        try {
            // Try using SHOW COLUMNS first (more reliable)
            $sql = "SHOW COLUMNS FROM `{$tableName}` LIKE ?";
            $result = $this->fetchAll($sql, [$columnName]);
            if (count($result) > 0) {
                return true;
            }
            
            // Fallback to INFORMATION_SCHEMA
            $sql = "SELECT COUNT(*) as count 
                    FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_SCHEMA = ? 
                    AND TABLE_NAME = ? 
                    AND COLUMN_NAME = ?";
            $result = $this->fetchAll($sql, [$this->database, $tableName, $columnName]);
            return isset($result[0]['count']) && $result[0]['count'] > 0;
        } catch (Exception $e) {
            error_log("columnExists Error: " . $e->getMessage());
            // If we can't check, assume it doesn't exist (safer)
            return false;
        }
    }
    
    // Add column if it doesn't exist
    public function addColumnIfNotExists($tableName, $columnName, $columnDefinition) {
        try {
            if (!$this->columnExists($tableName, $columnName)) {
                $sql = "ALTER TABLE `{$tableName}` ADD COLUMN {$columnDefinition}";
                $this->query($sql);
                error_log("Added column {$columnName} to table {$tableName}");
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log("addColumnIfNotExists Error: " . $e->getMessage());
            // Don't throw - column might already exist
            return false;
        }
    }
}
?>
