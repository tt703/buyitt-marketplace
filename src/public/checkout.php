<?php
// filepath: c:\Users\tman1\OneDrive\Documents\buyitt-marketplace\src\public\checkout.php

require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/auth.php';

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fetch the logged-in user's data directly from the session
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    // Redirect to the cart page if the user is not logged in
    $_SESSION['message'] = "You must log in to proceed to checkout.";
    header('Location: cart.php');
    exit();
}

// Fetch cart items & total
$stmt = $db->prepare("
    SELECT ci.id AS cart_item_id, p.id AS product_id, p.name, p.amount, p.image_path
    FROM cart_items ci
    JOIN products p ON p.id = ci.product_id
    WHERE ci.user_id = ?
");
$stmt->execute([$userId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total
$total = array_reduce($items, fn($sum, $i) => $sum + $i['amount'], 0);

if (empty($items)) {
    // Redirect to the cart page if the cart is empty
    $_SESSION['message'] = "Your cart is empty. Add items to proceed to checkout.";
    header('Location: cart.php');
    exit();
}

// Create a transaction record
try {
    $yocoReference = uniqid("txn_");
    $stmt = $db->prepare("INSERT INTO transactions (user_id, paystack_reference, total_amount, status) VALUES (?, ?, ?, 'pending')");
    $stmt->execute([$userId, $yocoReference, $total]);

    // Redirect to the payment page
    header("Location: /public/yoco_payment.php?reference=$yocoReference");
    exit();
} catch (PDOException $e) {
    // Log the error and redirect to the cart page with an error message
    error_log("Database error: " . $e->getMessage());
    $_SESSION['message'] = "An error occurred while processing your checkout. Please try again later.";
    header('Location: cart.php');
    exit();
}