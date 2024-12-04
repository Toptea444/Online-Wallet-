<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$userId = $_SESSION['user_id'];
$amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
$accountNumber = $_POST['account_number'] ?? '';
$bankName = $_POST['bank_name'] ?? '';
$accountType = $_POST['account_type'] ?? '';

// Server-side validation
if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid withdrawal amount. Please enter a positive value.']);
    exit();
}

try {
    $pdo->beginTransaction();

    // Fetch user balance
    $stmt = $pdo->prepare("SELECT balance FROM wallets WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$wallet) {
        throw new Exception('Wallet not found.');
    }

    $balance = (float)$wallet['balance'];

    // Check if withdrawal amount exceeds balance
    if ($amount > $balance) {
        throw new Exception('Insufficient balance.');
    }

    // Deduct amount from user's wallet
    $stmt = $pdo->prepare("UPDATE wallets SET balance = balance - :amount WHERE user_id = :user_id");
    $stmt->execute(['amount' => $amount, 'user_id' => $userId]);

    // Insert withdrawal request
    $stmt = $pdo->prepare("
        INSERT INTO withdraw_requests (user_id, amount, account_number, bank_name, account_type, status) 
        VALUES (:user_id, :amount, :account_number, :bank_name, :account_type, 'pending')
    ");
    $stmt->execute([
        'user_id' => $userId,
        'amount' => $amount,
        'account_number' => $accountNumber,
        'bank_name' => $bankName,
        'account_type' => $accountType,
    ]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Withdrawal request submitted successfully.']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
