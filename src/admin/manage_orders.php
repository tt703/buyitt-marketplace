<?php
// filepath: c:\Users\tman1\OneDrive\Documents\buyitt-marketplace\src\admin\manage_orders.php
require __DIR__ . '/../includes/config.php';
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit;
}

$stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['role'] !== 'admin') {
    header("Location: ../public/login.php");
    exit;
}

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update') {
        // Update an existing order
        $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([
            $_POST['status'],
            $_POST['id']
        ]);
    } elseif ($action === 'delete') {
        // Delete an order
        $stmt = $db->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$_POST['id']]);
    }
}

// Fetch all orders
$stmt = $db->query("
    SELECT o.id, o.total_amount, o.status, o.created_at, u.name AS buyer_name, u.email AS buyer_email
    FROM orders o
    JOIN users u ON o.buyer_id = u.user_id
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Manage Orders</h1>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Buyer</th>
            <th>Email</th>
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
                <td><?= htmlspecialchars($order['buyer_name']) ?></td>
                <td><?= htmlspecialchars($order['buyer_email']) ?></td>
                <td>R<?= number_format($order['total_amount'], 2) ?></td>
                <td>
                    <form method="POST" class="d-flex">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($order['id']) ?>">
                        <select name="status" class="form-select form-select-sm me-2" required>
                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                    </form>
                </td>
                <td><?= htmlspecialchars($order['created_at']) ?></td>
                <td>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($order['id']) ?>">
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this order?');">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>