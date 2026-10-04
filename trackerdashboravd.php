<?php
require_once 'transactionDAO.php';
require_once 'transactionValuescalction.php';

try {
    $trsDAO = new TransactionDAO();
    // Retrieve all transactions
    $transactions = $trsDAO->getAllTransactions();

    $expenses = TransactionValuesCalculation::getTotalExpenses($transactions);
    $incomes = TransactionValuesCalculation::getTotalIncomes($transactions);
    $total = $incomes - $expenses;

    if($total < 0){
        $formattedTotal = '-$'.number_format(abs($total), 2);
    } else {
        $formattedTotal = '+$'.number_format(abs($total), 2);
    }
} catch (Exception $e) {
    $expenses = 0;
    $incomes = 0;
    $formattedTotal = '$0.00';
    error_log("Error in trackerdashboravd.php: " . $e->getMessage());
}
?>
