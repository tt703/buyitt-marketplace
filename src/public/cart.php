<?php
include "../includes/navbar.php";
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/auth.php';

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Display the message if it exists
if (isset($_SESSION['message'])) {
    echo "<div class='alert alert-info'>" . htmlspecialchars($_SESSION['message']) . "</div>";
    unset($_SESSION['message']); // Clear the message after displaying it
}

// Get the current user
$user = optionalCurrentUser();
$userId = $user['user_id'] ?? null;

// Handle removals (only for logged-in users)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$userId) {
        die("Error: You must be logged in to remove items from your cart.");
    }

    try {
        if (isset($_POST['remove'])) {
            $removeId = intval($_POST['remove']);
            $stmt = $db->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
            $stmt->execute([$removeId, $userId]);
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        die("An error occurred while updating your cart. Please try again later.");
    }
}

// Fetch cart items
$items = [];

if ($userId) {
    // Logged-in user
    $stmt = $db->prepare("
        SELECT ci.id AS cart_id, p.id AS product_id, p.name, p.amount, p.image_path
        FROM cart_items ci
        JOIN products p ON p.id = ci.product_id
        WHERE ci.user_id = ?
    ");
    $stmt->execute([$userId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Guest user
    if (!isset($_SESSION['guest_cart']) || !is_array($_SESSION['guest_cart'])) {
        $_SESSION['guest_cart'] = [];
    }
    $guestCart = $_SESSION['guest_cart'];

    foreach ($guestCart as $productId => $quantity) {
        $stmt = $db->prepare("SELECT id AS product_id, name, amount, image_path FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $items[] = array_merge($product, [
                'cart_id' => $productId, // Fake ID for guest session
                'quantity' => $quantity
            ]);
        }
    }
}

// Calculate total
$total = array_reduce($items, fn($sum, $i) => $sum + ((float)($i['amount'] ?? 0) * ($i['quantity'] ?? 1)), 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart - BuyItt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/public.css?v=1.0" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h1 class="mb-4">Your Cart</h1>
        <?php if (empty($items)): ?>
            <p>Your cart is empty. <a href="index.php">Shop Now</a>.</p>
        <?php else: ?>
            <form method="POST">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $i): ?>
                            <tr>
                                <td>
                                    <img src="../assets/images/products/<?= htmlspecialchars($i['image_path'] ?? '') ?>"
                                         width="50" class="me-2">
                                    <?= htmlspecialchars($i['name'] ?? '') ?>
                                </td>
                                <td>R<?= number_format((float)($i['amount'] ?? 0), 2) ?></td>
                                <td><?= htmlspecialchars($i['quantity'] ?? 1) ?></td>
                                <td>
                                    <?php if ($userId): ?>
                                        <button name="remove" value="<?= (int)$i['cart_id'] ?>"
                                                class="btn btn-sm btn-outline-danger">Remove</button>
                                    <?php else: ?>
                                        <span class="text-muted">Login to manage</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="text-end" colspan="2">Total:</th>
                            <th colspan="2">R<?= number_format((float)$total, 2) ?></th>
                        </tr>
                    </tfoot>
                </table>

                <?php if ($userId): ?>
                    <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">Login to Checkout</a>
                <?php endif; ?>
            </form>
        <?php endif; ?>
    </div>
    <!-- Bottom Navigation -->
    <?php include __DIR__ . '/../includes/nav.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.js"></script>
    <script src="../assets/js/public.js?v=1.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>