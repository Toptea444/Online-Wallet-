<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['reference'])) {
    $reference = $_GET['reference'];

    // Verify the transaction using Paystack API
    $paystackSecretKey = "sk_live_0f2efa47723501c662cb7780747d44072e98a566"; // Replace with your secret key
    $url = "https://api.paystack.co/transaction/verify/$reference";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $paystackSecretKey"
    ]);

    $paystackResponse = curl_exec($ch);
    curl_close($ch);

    $response = json_decode($paystackResponse, true);

    if ($response && $response['status'] === true) {
        $transactionData = $response['data'];

        // Check if transaction exists in the database
        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE paystack_reference = ? AND status = 'pending'");
        $stmt->execute([$reference]);
        $transaction = $stmt->fetch();

        if ($transaction) {
            if ($transactionData['status'] === 'success') {
                // Update transaction status to completed
                $stmt = $pdo->prepare("UPDATE transactions SET status = 'completed' WHERE paystack_reference = ?");
                $stmt->execute([$reference]);

                // Update wallet balance
                $userId = $transaction['user_id'];
                $amount = $transaction['amount'];
                $stmt = $pdo->prepare("UPDATE wallets SET balance = balance + ? WHERE user_id = ?");
                $stmt->execute([$amount, $userId]);

                echo "Transaction successful. Wallet updated.";
            } else {
                echo "Transaction failed or incomplete.";
            }
        } else {
            echo "Transaction not found or already completed.";
        }
    } else {
        echo "Failed to verify transaction.";
    }
} else {
    echo "Invalid request.";
}
?>
