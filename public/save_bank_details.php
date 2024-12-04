<?php
session_start();
// Include your DB configuration
require_once '../config/database.php';

// Validate input and store bank details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $accountName = $_POST['account_name'];
    $accountNumber = $_POST['account_number'];
    $bankName = $_POST['bank_name'];

    // Insert bank details into the database
    $stmt = $pdo->prepare("INSERT INTO bank_details (user_id, account_name, account_number, bank_name) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $accountName, $accountNumber, $bankName]);

    echo 'Bank details saved successfully';
}
?>
