<?php
require __DIR__ . '/../includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? null;
$reference = $data['reference'] ?? null;

if (!$token || !$reference) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit();
}

// Yoco payment verification
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

// Find the matching transaction
$stmt = $db->prepare("SELECT * FROM transactions WHERE paystack_reference = ?");
$stmt->execute([$reference]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    echo json_encode(['success' => false, 'message' => 'Transaction not found.']);
    exit();
}

// Update transaction status
$stmt = $db->prepare("UPDATE transactions SET status = 'success' WHERE paystack_reference = ?");
$stmt->execute([$reference]);

// Create order and link reference
$stmt = $db->prepare("INSERT INTO orders (buyer_id, total_amount, status, paystack_reference) VALUES (?, ?, 'completed', ?)");
$stmt->execute([$transaction['user_id'], $transaction['total_amount'], $reference]);
$orderId = $db->lastInsertId();

// Pull cart items for user to use in order_items
$stmt = $db->prepare("SELECT * FROM cart_items WHERE user_id = ?");
$stmt->execute([$transaction['user_id']]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, price_at_purchase) VALUES (?, ?, ?)");

foreach ($cartItems as $item) {
    $productId = $item['product_id'];

    // Fetch the product price from the products table
    $productStmt = $db->prepare("SELECT amount FROM products WHERE id = ?");
    $productStmt->execute([$productId]);
    $product = $productStmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        continue; // Skip if product not found
    }

    $price = $product['amount'];

    $stmt->execute([$orderId, $productId, $price]);
}


// Clear cart
$stmt = $db->prepare("DELETE FROM cart_items WHERE user_id = ?");
$stmt->execute([$transaction['user_id']]);

// Link order to transaction
$stmt = $db->prepare("UPDATE transactions SET order_id = ? WHERE id = ?");
$stmt->execute([$orderId, $transaction['id']]);

echo json_encode(['success' => true, 'redirect' => '/public/orders.php']);
exit();
