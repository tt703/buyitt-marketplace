<?php
include '../includes/navbar.php';
require_once '../includes/auth.php';
require_once '../includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure only sellers or admins access
if ($_SESSION['role'] !== 'seller' && $_SESSION['role'] !== 'admin') {
    header('Location: ../public/index.php'); exit;
}

$sellerId = $_SESSION['user_id'];

// Handle product deletion
if (isset($_GET['delete_product'])) {
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ? AND user_id = ?");
    $stmt->execute([$_GET['delete_product'], $sellerId]);
    header('Location: index.php'); exit;
}

// Fetch seller products
$stmt = $db->prepare(
    query: "SELECT p.id AS product_id ,p.name, p.amount, c.name AS category_name, p.image_path, p.created_at 
     FROM products p
     JOIN categories c ON p.category = c.id
     WHERE p.user_id = ?
     ORDER BY p.created_at DESC"
);
$stmt->execute([$sellerId]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch seller orders (orders containing seller's products)
$stmt = $db->prepare(
    "SELECT o.id, u.name AS buyer_name, o.total_amount, o.status, o.created_at
     FROM orders o
     JOIN order_items oi ON o.id = oi.order_id
     JOIN products p ON oi.product_id = p.id
     JOIN users u ON o.buyer_id = u.user_id
     WHERE p.user_id = ?
     GROUP BY o.id
     ORDER BY o.created_at DESC"
);
$stmt->execute([$sellerId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Seller Dashboard - BuyItt</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/public.css?v=1.0"  rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">


  <style>
    body { background: #f4f6f9; }
    .tab-content { background: #fff; padding: 20px; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .table img { max-width: 50px; height: auto; }
  </style>
</head>
<body class="p-4">
  <h1 class="mb-4">Seller Dashboard</h1>

  <ul class="nav nav-tabs mb-4" id="dashboardTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab">Products</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">Orders</button>
    </li>
  </ul>

  <div class="tab-content" id="dashboardTabsContent">
    <div class="tab-pane fade show active" id="products" role="tabpanel">
      <a href="product_form.php" class="btn btn-primary mb-3">+ Add New Product</a>
      <table class="table table-striped">
        <thead>
          <tr><th>ID</th><th>Title</th><th>Price</th><th>Category</th><th>Image</th><th>Date Added</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($products as $p): ?>
          <tr>
            <td><?= $p['product_id'] ?></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td>R<?= number_format($p['amount'],2) ?></td>
            <td><?= htmlspecialchars($p['category_name']) ?></td>
            <td><?php if ($p['image_path']): ?><img src="../uploads/<?= htmlspecialchars($p['image_path']) ?>" alt=""/><?php endif; ?></td>
            <td><?= htmlspecialchars($p['created_at']) ?></td>
            <td>
              <a href="product_form.php?id=<?= $p['product_id'] ?>" class="btn btn-sm btn-info">Edit</a>
              <a href="?delete_product=<?= $p['product_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="tab-pane fade" id="orders" role="tabpanel">
      <?php if (empty($orders)): ?>
        <p>No orders found.</p>
      <?php else: ?>
        <table class="table">
          <thead>
            <tr><th>Order ID</th><th>Buyer</th><th>Total</th><th>Status</th><th>Created At</th></tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $o): ?>
            <tr>
              <td><?= htmlspecialchars($o['id']) ?></td>
              <td><?= htmlspecialchars($o['buyer_name']) ?></td>
              <td>R<?= number_format($o['total_amount'],2) ?></td>
              <td><?= htmlspecialchars($o['status']) ?></td>
              <td><?= htmlspecialchars($o['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

<!--bottom bav -->
<?php include __DIR__ .  "/../includes/nav.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.js"></script>
<script src="../assets/js/public.js?v=1.0"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
