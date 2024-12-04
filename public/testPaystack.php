<?php
$url = "https://api.paystack.co/transaction/initialize";
$paystackSecretKey = "sk_live_0f2efa47723501c662cb7780747d44072e98a566";

$data = [
    'amount' => 10000, // Example: ₦100
    'email' => 'test@example.com',
    'callback_url' => 'http://localhost:8080/public/src/controllers/callback.php'
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
$response = file_get_contents($url, false, $context);

echo $response;
?>
