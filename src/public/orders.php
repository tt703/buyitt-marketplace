<?php
include "../includes/navbar.php";
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    // User is not logged in
    $error = "You must be logged in to view this page.";
} else {
    // Fetch user data
    $stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // User not found in the database
        $error = "User not found.";
    } else {
        // Fetch user orders
        $stmt = $db->prepare("SELECT * FROM orders WHERE buyer_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/public.css?v=1.0" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .modal-body table { width: 100%; }
    </style>
</head>
<body class="p-4">
    <h1>Your Orders</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (empty($orders)): ?>
        <p>You have no orders. <a href="index.php">Shop Now</a>.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['id']) ?></td>
                        <td>R<?= number_format($order['total_amount'], 2) ?></td>
                        <td><?= htmlspecialchars($order['status']) ?></td>
                        <td><?= htmlspecialchars($order['created_at']) ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#orderDetailsModal" 
                                    data-order-id="<?= htmlspecialchars($order['id']) ?>">
                                View Details
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Loading order details...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/nav.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const orderDetailsModal = document.getElementById('orderDetailsModal');

            orderDetailsModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                const orderId = button.getAttribute('data-order-id');

                const modalBody = orderDetailsModal.querySelector('.modal-body');
                modalBody.innerHTML = '<p>Loading...</p>';

                fetch(`/public/fetch_order_details.php?order_id=${orderId}`)
                    .then(res => res.json())
                    .then(data => {
                        console.log("Response Data:", data); // Log response data
                        const modalBody = orderDetailsModal.querySelector('.modal-body');
                        if (data.success) {
                            const details = data.details;
                            let html = `
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Image</th>
                                            <th>Price</th>
                                            <th>Seller</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            `;

                            details.forEach(item => {
                                html += `
                                    <tr>
                                        <td>${item.product_name}</td>
                                        <td><img src="/uploads/${item.product_image}" alt="Product Image" style="width: 70px;"></td>
                                        <td>R${parseFloat(item.price_at_purchase).toFixed(2)}</td>
                                        <td>${item.seller_name}</td>
                                        <td>
                                            <a href="/public/chats.php?with_user=${item.seller_id}" class="btn btn-sm btn-primary">
                                                Contact Seller
                                            </a>
                                        </td>
                                    </tr>
                                `;
                            });

                            html += `</tbody></table>`;
                            modalBody.innerHTML = html;
                        } else {
                            modalBody.innerHTML = `<p>${data.message}</p>`;
                        }
                    })
                    .catch(err => {
                        console.error("Error Fetching Order Details:", err);
                        modalBody.innerHTML = '<p>Failed to load order details.</p>';
                    });
            });
        });
    </script>
</body>
</html>