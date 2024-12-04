<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    die('You need to log in first.');
}

// Get the form data
$userId = $_SESSION['user_id'];
$amount = $_POST['amount'] ?? 0;
$accountNumber = $_POST['account_number'] ?? '';
$bankName = $_POST['bank_name'] ?? '';
$accountType = $_POST['account_type'] ?? '';

// Validate input
if ($amount <= 0 || empty($accountNumber) || empty($bankName) || empty($accountType)) {
    die('Invalid input');
}

// Fetch user wallet
$stmt = $pdo->prepare("SELECT balance FROM wallets WHERE user_id = :user_id");
$stmt->execute(['user_id' => $userId]);
$userWallet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userWallet) {
    die('User wallet not found.');
}

if ($userWallet['balance'] < $amount) {
    die('Insufficient balance.');
}

// Fetch the user's full name from the users table
$stmt = $pdo->prepare("SELECT name FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('User not found.');
}

$fullName = $user['name'];

// Insert withdrawal request (could be pending status)
$stmt = $pdo->prepare("
    INSERT INTO withdrawals (user_id, amount, account_number, bank_name, account_type, status, name)
    VALUES (:user_id, :amount, :account_number, :bank_name, :account_type, 'pending', :name)
");
$stmt->execute([
    'user_id' => $userId,
    'amount' => $amount,
    'account_number' => $accountNumber,
    'bank_name' => $bankName,
    'account_type' => $accountType,
    'name' => $fullName
]);

// Paystack API call using cURL
$paystackSecretKey = 'your paystack secret key';
$paystackUrl = 'https://api.paystack.co/transferrecipient';

// First, create a transfer recipient
$recipientData = [
    'type' => 'nuban', // for Nigerian bank accounts
    'name' => $fullName,
    'account_number' => $accountNumber,
    'bank_code' => '999992', // Opay 
    'currency' => 'NGN'
];

$ch = curl_init($paystackUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $paystackSecretKey,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($recipientData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$recipientResponse = curl_exec($ch);
$recipientResult = json_decode($recipientResponse, true);

if (!$recipientResult['status']) {
    die('Failed to create transfer recipient: ' . $recipientResult['message']);
}

$recipientCode = $recipientResult['data']['recipient_code'];

// Now initiate the transfer
$transferUrl = 'https://api.paystack.co/transfer';
$transferData = [
    'source' => 'balance', // Transfer from Paystack balance
    'amount' => $amount * 100, // Amount in kobo
    'recipient' => $recipientCode,
    'reason' => 'Withdrawal for ' . $fullName
];

$ch = curl_init($transferUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $paystackSecretKey,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transferData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$transferResponse = curl_exec($ch);
$transferResult = json_decode($transferResponse, true);

if ($transferResult['status']) {
    // Update status in DB to completed or transfer successful
    $stmt = $pdo->prepare("UPDATE withdrawals SET status = 'completed' WHERE user_id = :user_id AND status = 'pending'");
    $stmt->execute(['user_id' => $userId]);

    // Deduct amount from the user's balance
    $stmt = $pdo->prepare("UPDATE wallets SET balance = balance - :amount WHERE user_id = :user_id");
    $stmt->execute(['amount' => $amount, 'user_id' => $userId]);

    echo json_encode(['success' => true, 'message' => 'Withdrawal successful']);
} else {
    echo json_encode(['success' => false, 'message' => $transferResult['message'] ?? 'Error with Paystack transfer']);
}

curl_close($ch);
?>
