<?php
// filepath: c:\Users\tman1\OneDrive\Documents\buyitt-marketplace\src\admin\manage_transaction.php
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

// Fetch all transactions
$stmt = $db->prepare("
    SELECT t.*, u.email 
    FROM transactions t 
    JOIN users u ON t.user_id = u.user_id 
    ORDER BY t.created_at DESC
");
$stmt->execute();
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Manage Transactions</h1>

<!-- Transactions Table -->
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Reference</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($transactions as $txn): ?>
            <tr>
                <td><?= htmlspecialchars($txn['id']) ?></td>
                <td><?= htmlspecialchars($txn['email']) ?></td>
                <td><?= htmlspecialchars($txn['paystack_reference']) ?></td>
                <td>R<?= number_format($txn['total_amount'], 2) ?></td>
                <td><?= htmlspecialchars($txn['status']) ?></td>
                <td><?= htmlspecialchars($txn['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>