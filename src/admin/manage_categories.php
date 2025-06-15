<?php
// filepath: c:\Users\tman1\OneDrive\Documents\buyitt-marketplace\src\admin\manage_categories.php
include("../includes/config.php");
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit;
}

// Fetch user details from the database
$stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the user is an admin
if (!$user || $user['role'] !== 'admin') {
    header("Location: ../public/login.php");
    exit;
}

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_name'])) {
        $stmt = $db->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$_POST['add_name']]);
    } elseif (isset($_POST['edit_id'], $_POST['edit_name'])) {
        $stmt = $db->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->execute([$_POST['edit_name'], $_POST['edit_id']]);
    }
    header("Location: manage_categories.php");
    exit;
}

if (isset($_GET['delete_id'])) {
    $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    header("Location: manage_categories.php");
    exit;
}

// Fetch categories
$categories = $db->query("SELECT * FROM categories ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Manage Categories</h1>
<form method="POST" class="row g-2 mb-4">
    <div class="col-auto">
        <input type="text" name="add_name" class="form-control" placeholder="Category Name" required>
    </div>
    <div class="col-auto">
        <button class="btn btn-primary">Add Category</button>
    </div>
</form>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categories as $cat): ?>
            <tr>
                <td><?= htmlspecialchars($cat['id']) ?></td>
                <td>
                    <form method="POST" class="d-flex">
                        <input type="hidden" name="edit_id" value="<?= $cat['id'] ?>">
                        <input name="edit_name" class="form-control form-control-sm me-2" value="<?= htmlspecialchars($cat['name']) ?>" required>
                        <button class="btn btn-sm btn-primary me-1">Save</button>
                        <a href="?delete_id=<?= $cat['id'] ?>" onclick="return confirm('Request to delete this category?')" class="btn btn-sm btn-danger">Delete</a>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>