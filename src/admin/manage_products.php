<?php
// filepath: c:\Users\tman1\OneDrive\Documents\buyitt-marketplace\src\admin\manage_products.php
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

    if ($action === 'create') {
        // Create a new product
        $stmt = $db->prepare("INSERT INTO products (name, description, amount, category, user_id, image_path) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['description'],
            $_POST['amount'],
            $_POST['category'],
            $_POST['user_id'],
            $_POST['image_path']
        ]);
    } elseif ($action === 'update') {
        // Update an existing product
        $stmt = $db->prepare("UPDATE products SET name = ?, description = ?, amount = ?, category = ?, user_id = ?, image_path = ? WHERE id = ?");
        $stmt->execute([
            $_POST['name'],
            $_POST['description'],
            $_POST['amount'],
            $_POST['category'],
            $_POST['user_id'],
            $_POST['image_path'],
            $_POST['id']
        ]);
    } elseif ($action === 'delete') {
        // Delete a product
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$_POST['id']]);
    }
}

// Fetch all products
$stmt = $db->query("
    SELECT p.id, p.name, p.description, p.amount, c.name AS category_name, u.name AS seller_name, p.image_path, p.created_at
    FROM products p
    JOIN categories c ON p.category = c.id
    JOIN users u ON p.user_id = u.user_id
    ORDER BY p.created_at DESC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all categories for the dropdown
$categories = $db->query("SELECT id, name FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all sellers for the dropdown
$sellers = $db->query("SELECT user_id AS id, name FROM users WHERE role = 'seller'")->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Manage Products</h1>

<!-- Create Product Form -->
<form method="POST" class="row g-3 mb-4">
    <input type="hidden" name="action" value="create">
    <div class="col-md-3">
        <input type="text" name="name" class="form-control" placeholder="Product Name" required>
    </div>
    <div class="col-md-3">
        <textarea name="description" class="form-control" placeholder="Description" required></textarea>
    </div>
    <div class="col-md-2">
        <input type="number" step="0.01" name="amount" class="form-control" placeholder="Price" required>
    </div>
    <div class="col-md-2">
        <select name="category" class="form-select" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="user_id" class="form-select" required>
            <option value="">Select Seller</option>
            <?php foreach ($sellers as $seller): ?>
                <option value="<?= htmlspecialchars($seller['id']) ?>"><?= htmlspecialchars($seller['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <input type="text" name="image_path" class="form-control" placeholder="Image Path" required>
    </div>
    <div class="col-md-12">
        <button type="submit" class="btn btn-primary">Create Product</button>
    </div>
</form>

<!-- Products Table -->
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Category</th>
            <th>Seller</th>
            <th>Image</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= htmlspecialchars($product['id']) ?></td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['description']) ?></td>
                <td>R<?= number_format($product['amount'], 2) ?></td>
                <td><?= htmlspecialchars($product['category_name']) ?></td>
                <td><?= htmlspecialchars($product['seller_name']) ?></td>
                <td><img src="<?= htmlspecialchars($product['image_path']) ?>" alt="Product Image" style="width: 50px; height: 50px;"></td>
                <td><?= htmlspecialchars($product['created_at']) ?></td>
                <td>
                    <!-- Update Product Form -->
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">
                        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" class="form-control mb-2" required>
                        <textarea name="description" class="form-control mb-2" required><?= htmlspecialchars($product['description']) ?></textarea>
                        <input type="number" step="0.01" name="amount" value="<?= htmlspecialchars($product['amount']) ?>" class="form-control mb-2" required>
                        <select name="category" class="form-select mb-2" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['id']) ?>" <?= $product['category_name'] === $category['name'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select name="user_id" class="form-select mb-2" required>
                            <?php foreach ($sellers as $seller): ?>
                                <option value="<?= htmlspecialchars($seller['id']) ?>" <?= $product['seller_name'] === $seller['name'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($seller['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="image_path" value="<?= htmlspecialchars($product['image_path']) ?>" class="form-control mb-2" required>
                        <button type="submit" class="btn btn-warning btn-sm">Update</button>
                    </form>

                    <!-- Delete Product Form -->
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>