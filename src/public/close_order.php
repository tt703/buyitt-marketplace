<?php
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = intval($_POST['order_id']);
    $buyerId = intval($_POST['buyer_id']);
    $sellerId = $_SESSION['user_id'];

    try {
        // Mark the order as delivered and send a message to the buyer
        markOrderAsDelivered($db, $orderId, $sellerId, $buyerId);

        // Redirect back to the seller dashboard
        header('Location: seller_dashboard.php');
        exit();
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        die("An error occurred: " . htmlspecialchars($e->getMessage()));
    }
}

/**
 * Marks an order as delivered and sends a message to the buyer for confirmation.
 *
 * @param PDO $db The database connection.
 * @param int $orderId The ID of the order.
 * @param int $sellerId The ID of the seller.
 * @param int $buyerId The ID of the buyer.
 */
function markOrderAsDelivered($db, $orderId, $sellerId, $buyerId) {
    // Update the order status to "Delivered"
    $stmt = $db->prepare("
        UPDATE orders 
        SET status = 'Delivered' 
        WHERE id = ? 
        AND id IN (
            SELECT oi.order_id 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            WHERE p.user_id = ?
        )
    ");
    $stmt->execute([$orderId, $sellerId]);

    // Fetch the product_id associated with the order
    $stmt = $db->prepare("
        SELECT oi.product_id 
        FROM order_items oi 
        WHERE oi.order_id = ?
        LIMIT 1
    ");
    $stmt->execute([$orderId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        throw new Exception("No product found for the given order ID.");
    }

    $productId = $product['product_id'];

    // Send a message to the buyer to verify the delivery
    $message = "Your order #{$orderId} has been marked as delivered. Please confirm if the delivery was successful. By answering 1 for delivered and 2 for not delivered.";
    $stmt = $db->prepare("INSERT INTO messages (product_id, from_user, to_user, message, created_at, is_read) VALUES (?, ?, ?, ?, NOW(), 0)");
    $stmt->execute([$productId, $sellerId, $buyerId, $message]);
}