<?php
// Display PHP errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Include the database configuration
require_once '../../config/database.php';

// Initialize response
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        $response['message'] = 'You need to log in first.';
    } else {
        $amount = $_POST['amount'] ?? 0;

        // Validate deposit amount
        if ($amount <= 0) {
            $response['message'] = 'Deposit amount must be greater than zero.';
        } else {
            try {
                // Paystack API details
                $paystackSecretKey = "your paystack secret key "; // Replace with your secret key

                // Get logged-in user details
                $userId = $_SESSION['user_id'];
                $userEmail = $_SESSION['user_email'] ?? null; // Ensure this is set

                if (!$userEmail) {
                    // Fetch email from database if not set in session
                    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
                    $stmt->execute([$userId]);
                    $user = $stmt->fetch();
                    $userEmail = $user['email'] ?? null;

                    if (!$userEmail) {
                        $response['message'] = 'User email not found.';
                        echo json_encode($response);
                        exit();
                    }
                }

                // Save transaction in the database (pending status)
                $reference = uniqid('ref_'); // Generate a unique reference
                $stmt = $pdo->prepare("
                    INSERT INTO transactions (user_id, amount, type, status, paystack_reference)
                    VALUES (?, ?, 'deposit', 'pending', ?)
                ");
                $stmt->execute([$userId, $amount, $reference]);

                // Prepare Paystack API request
                $url = "https://api.paystack.co/transaction/initialize";
                $data = [
                    'amount' => $amount * 100, // Paystack expects amount in kobo
                    'email' => $userEmail,
                    'reference' => $reference,
                    'callback_url' => 'https://fintechsite.infinityfreeapp.com/project/public/callback.php',
                ];

                $options = [
                    'http' => [
                        'header'  => "Authorization: Bearer $paystackSecretKey\r\n" .
                                     "Content-Type: application/json\r\n",
                        'method'  => 'POST',
                        'content' => json_encode($data),
                    ],
                ];
                $context  = stream_context_create($options);
                $paystackResponse = file_get_contents($url, false, $context);

                if ($paystackResponse === FALSE) {
                    $response['message'] = 'Error with Paystack API.';
                } else {
                    $responseData = json_decode($paystackResponse, true);
                    if ($responseData['status'] === true) {
                        // Redirect user to Paystack payment page
                        header('Location: ' . $responseData['data']['authorization_url']);
                        exit();
                    } else {
                        $response['message'] = 'Error initializing Paystack transaction.';
                    }
                }
            } catch (Exception $e) {
                $response['message'] = 'Error: ' . $e->getMessage();
            }
        }
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
