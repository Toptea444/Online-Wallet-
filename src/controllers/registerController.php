<?php
require_once '../../config/database.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $response['message'] = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email address.';
    } else {
        try {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert into users table
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
            $stmt->execute(['name' => $name, 'email' => $email, 'password' => $hashedPassword]);

            // Get the user ID
            $userId = $pdo->lastInsertId();

            // Assign virtual wallet
            $virtualAccount = 'VA-' . uniqid();
            $stmt = $pdo->prepare("INSERT INTO wallets (user_id, balance, virtual_account) VALUES (:user_id, 0.00, :virtual_account)");
            $stmt->execute(['user_id' => $userId, 'virtual_account' => $virtualAccount]);

            $response['success'] = true;
            $response['message'] = "Registration successful! Your virtual account number is: $virtualAccount";
        } catch (PDOException $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        }
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
