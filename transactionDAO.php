<?php
error_reporting(0);
ini_set('display_errors', 0);
require_once 'database_connection.php'; 
require_once 'Transaction.php'; 

class TransactionDAO
{
    private $db;

    public function __construct()
    {
        try {
            $this->db = new DatabaseConnection("localhost", "u741730784_admin_rushed", "PdDede.comMSLPHI25@!", "u741730784_rushed");
            
            // Auto-fix: Ensure user_id column exists (non-blocking)
            $this->ensureUserIdColumnExists();
        } catch (Exception $e) {
            // Re-throw connection errors
            throw $e;
        }
    }
    
    // Check and add user_id column if missing
    private function ensureUserIdColumnExists() {
        try {
            // First check if table exists
            $tables = $this->db->fetchAll("SHOW TABLES LIKE 'transaction_table'");
            if (count($tables) == 0) {
                // Table doesn't exist yet, skip column check
                return;
            }
            
            // Try to add column if it doesn't exist
            $this->db->addColumnIfNotExists(
                'transaction_table', 
                'user_id', 
                '`user_id` int(11) NULL AFTER `category`'
            );
        } catch (Exception $e) {
            // Silently fail - column might already exist or there's a permission issue
            // Don't throw - this is not critical for basic functionality
            error_log("ensureUserIdColumnExists: " . $e->getMessage());
        }
    }
 
    public function getAllTransactions($userId = null)
    {
        try {
            // Check if user_id column exists before using it
            $hasUserIdColumn = $this->db->columnExists('transaction_table', 'user_id');
            
            if ($userId && $hasUserIdColumn) {
                $sql = "SELECT * FROM `transaction_table` WHERE `user_id` = ? ORDER BY `id` DESC";
                $result = $this->db->fetchAll($sql, [$userId]);
            } else {
                // If user_id column doesn't exist or userId is null, get all transactions
                $sql = "SELECT * FROM `transaction_table` ORDER BY `id` DESC";
                $result = $this->db->fetchAll($sql);
            }
            
            // Ensure we return an array even if empty
            return is_array($result) ? $result : [];
        } catch (Exception $e) {
            error_log("getAllTransactions Error: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function addTransaction(Transaction $transaction, $userId = null)
    {
        // Check if user_id column exists
        $hasUserIdColumn = $this->db->columnExists('transaction_table', 'user_id');
        
        if ($userId && $hasUserIdColumn) {
            $sql = "INSERT INTO `transaction_table`(`transaction_type`, `description`, `category`, `amount`, `user_id`) 
                    VALUES (?, ?, ?, ?, ?)";
            $params = [
                $transaction->getType(), 
                $transaction->getDescription(), 
                $transaction->getCategory(), 
                $transaction->getAmount(),
                $userId
            ];
        } else {
            $sql = "INSERT INTO `transaction_table`(`transaction_type`, `description`, `category`, `amount`) 
                    VALUES (?, ?, ?, ?)";
            $params = [
                $transaction->getType(), 
                $transaction->getDescription(), 
                $transaction->getCategory(), 
                $transaction->getAmount()
            ];
        }
        return $this->db->query($sql, $params);
    }

    public function deleteTransaction($id, $userId = null)
    {
        // Check if user_id column exists
        $hasUserIdColumn = $this->db->columnExists('transaction_table', 'user_id');
        
        if ($userId && $hasUserIdColumn) {
            $sql = "DELETE FROM `transaction_table` WHERE id = ? AND user_id = ?";
            return $this->db->query($sql, [$id, $userId]);
        } else {
            $sql = "DELETE FROM `transaction_table` WHERE id = ?";
            return $this->db->query($sql, [$id]);
        }
    }
}
?>
