<?php
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/auth.php';

// Get the current user (optional)
$user = optionalCurrentUser();
$userId = $user['user_id'] ?? null; // Use null if not logged in

// Get product ID from the request
$prodId = intval($_GET['id'] ?? 0);

if ($prodId < 1) {
    // Invalid product ID, redirect to the cart page
    $_SESSION['message'] = "Invalid product.";
    header('Location: cart.php');
    exit();
}

try {
    if ($userId) {
        // Logged-in user: Check if the product is already in the cart
        $stmt = $db->prepare("SELECT id FROM cart_items WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $prodId]);
        $existingCartItem = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingCartItem) {
            // Product already exists in the cart
            $_SESSION['message'] = "Product is already in your cart.";
        } else {
            // Add item to the database cart
            $stmt = $db->prepare("INSERT INTO cart_items(user_id, product_id, quantity) VALUES (?, ?, 1)");
            $stmt->execute([$userId, $prodId]);
            $_SESSION['message'] = "Product added to your cart.";
        }
    } else {
        // Guest user: Store cart data in the session
        $_SESSION['guest_cart'] = $_SESSION['guest_cart'] ?? [];
        if (isset($_SESSION['guest_cart'][$prodId])) {
            // Product already exists in the guest cart
            $_SESSION['message'] = "Product is already in your cart.";
        } else {
            $_SESSION['guest_cart'][$prodId] = 1; // Default quantity is 1 for guest users
            $_SESSION['message'] = "Product added to your cart.";
        }
    }
} catch (PDOException $e) {
    // Log the error and set an error message
    error_log("Error adding product to cart: " . $e->getMessage());
    $_SESSION['message'] = "An error occurred while adding the product to your cart. Please try again.";
}

// Redirect to the cart page
header('Location: cart.php');
exit();