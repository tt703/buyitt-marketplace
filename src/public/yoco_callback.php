<?php
require __DIR__ . '/../includes/config.php';

//start the session to access user information
if(session_status() ===PHP_SESSION_NONE){
    session_start();
}

$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? null;
$reference = $data['reference'] ?? null;

if (!$token || !$reference) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit();
}

// Verify the payment with Yoco
$yocoSecretKey = 'sk_test_7a9b4a92nm3zKl49900409889891';
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://online.yoco.com/v1/charges/",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "X-Auth-Secret-Key: $yocoSecretKey",
        "Content-Type: application/json"
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode([
        'token' => $token,
        'amountInCents' => intval($data['amountInCents']),
        'currency' => 'ZAR'
    ])
]);
$response = curl_exec($curl);
curl_close($curl);

if (!$response) {
    echo json_encode(['success' => false, 'message' => 'Unable to verify payment.']);
    exit();
}

$resp = json_decode($response, true);

if (!isset($resp['status']) || $resp['status'] !== 'successful') {
    echo json_encode(['success' => false, 'message' => 'Payment failed.']);
    exit();
}

// Fetch the transaction details
$stmt = $db->prepare("SELECT * FROM transactions WHERE paystack_reference = ?");
$stmt->execute([$reference]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    echo json_encode(['success' => false, 'message' => 'Transaction not found.']);
    exit();
}

// Update the transaction status
$stmt = $db->prepare("UPDATE transactions SET status = 'success' WHERE paystack_reference = ?");
$stmt->execute([$reference]);

// Create an order
$stmt = $db->prepare("INSERT INTO orders (buyer_id, total_amount, status) VALUES (?, ?, 'completed')");
$stmt->execute([$transaction['user_id'], $transaction['total_amount']]);
$orderId = $db->lastInsertId();

// Link the order to the transaction
$stmt = $db->prepare("UPDATE transactions SET order_id = ? WHERE id = ?");
$stmt->execute([$orderId, $transaction['id']]);

// Return a JSON response with the redirect URL
echo json_encode(['success' => true, 'redirect' => '/public/orders.php']);
exit();