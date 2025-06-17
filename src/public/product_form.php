<?php
include "../includes/navbar.php";
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/auth.php';

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /public/login.php");
    exit();
}

// Fetch the current user's ID
$sellerId = $_SESSION['user_id']; // Use 'user_id' as per your session structure
$error = "";

// Fetch categories for the dropdown
$cats = $db->query("SELECT id, name FROM categories")->fetchAll(PDO::FETCH_ASSOC);

$product = null;
if (!empty($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $sellerId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        header("Location: /public/seller_dashboard.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $amount = floatval($_POST['amount']);
    $category = intval($_POST['category']);

    $imagePath = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = __DIR__ . "/../assets/images/products/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true); // Create the directory if it doesn't exist
        }
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $filename);
        $imagePath = $filename;
    }

    try {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            // Update existing product
            $id = intval($_POST['id']);
            $sql = "UPDATE products SET name = ?, description = ?, amount = ?, category = ?" .
                   ($imagePath ? ", image_path = ?" : "") . " WHERE id = ? AND user_id = ?";
            $stmt = $db->prepare($sql);
            $params = [$name, $description, $amount, $category];
            if ($imagePath) $params[] = $imagePath;
            $params[] = $id;
            $params[] = $sellerId;
            $stmt->execute($params);
        } else {
            // Insert new product
            $stmt = $db->prepare("INSERT INTO products (user_id, name, description, amount, category, image_path) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$sellerId, $name, $description, $amount, $category, $imagePath]);
        }
        header("Location: /public/seller_dashboard.php");
        exit();
    } catch (Exception $e) {
        $error = "An error occurred: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $product ? 'Edit Product' : 'Add Product' ?> - BuyItt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/public.css?v=1.0" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h1><?= $product ? 'Edit Product' : 'Add Product' ?></h1>
                <a href="/public/seller_dashboard.php" class="btn btn-primary mb-3">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $product['id'] ?? '' ?>">

                <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" id="name" value="<?= htmlspecialchars($product['name'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" class="form-control" id="description" rows="4" required><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="amount" class="form-label">Amount</label>
                    <input type="number" name="amount" class="form-control" id="amount" value="<?= htmlspecialchars($product['amount'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="category" class="form-label">Category</label>
                    <select name="category" class="form-select" id="category" required>
                        <?php foreach ($cats as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($product && $product['category'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Product Image</label>
                    <input type="file" name="image" class="form-control" id="image">
                    <?php if (!empty($product['image_path'])): ?>
                        <div class="mt-2">
                            <img src="../assets/images/products/<?= htmlspecialchars($product['image_path']) ?>" alt="Product Image" class="img-thumbnail" style="max-width: 150px;">
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary w-100"><?= $product ? 'Update Product' : 'Add Product' ?></button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>