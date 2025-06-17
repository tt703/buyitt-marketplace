<?php
// filepath: src/public/fetch_order_details.php

require __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');

// turn on errors for debugging (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$orderId = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);
if (!$orderId) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid or missing Order ID.'
    ]);
    exit;
}

try {
    $sql = "
        SELECT 
            oi.product_id,
            oi.price_at_purchase,
            p.name AS product_name,
            p.image_path AS product_image,
            p.user_id AS seller_id,
            u.name AS seller_name,
            u.email AS seller_email
        FROM order_items oi
        INNER JOIN products p ON oi.product_id = p.id
        INNER JOIN users    u ON p.user_id    = u.user_id
        WHERE oi.order_id = ?
    ";
    $stmt = $db->prepare($sql);
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($items) {
        echo json_encode([
            'success' => true,
            'details' => $items
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No items found for this order.'
        ]);
    }
} catch (PDOException $e) {
    error_log("fetch_order_details error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Server error while fetching order details.'
    ]);
}
