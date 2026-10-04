<?php
class TransactionValuesCalculation
{
    // Method to calculate the total expenses
    public static function getTotalExpenses($transactions): float
    {
        $expenses = array_filter($transactions, function($transaction){
            return $transaction['transaction_type'] == 'Expense';
        });
        return array_sum(array_column($expenses, 'amount'));
    }

    // Method to calculate the total incomes
    public static function getTotalIncomes($transactions): float
    {
        $incomes = array_filter($transactions, function($transaction){
            return $transaction['transaction_type'] == 'Income';
        });
        return array_sum(array_column($incomes, 'amount'));
    }

    /**
     * Method to calculate the total amount of 'Luxury' expenses
     */
    public static function getLuxuryExpenses($transactions): float
    {
        $luxuryExpenses = array_filter($transactions, function($transaction) {
            return $transaction['transaction_type'] == 'Expense' && 
                   (isset($transaction['category']) && $transaction['category'] == 'Luxury'); 
        });
        return array_sum(array_column($luxuryExpenses, 'amount'));
    }
}
?>
