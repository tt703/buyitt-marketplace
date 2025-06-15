<?php
// filepath: c:\Users\tman1\OneDrive\Documents\buyitt-marketplace\src\admin\manage_users.php
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
        // Create a new user
        $stmt = $db->prepare("INSERT INTO users (name, email, role, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $_POST['role'],
            password_hash($_POST['password'], PASSWORD_DEFAULT)
        ]);
    } elseif ($action === 'update') {
        // Update an existing user
        $stmt = $db->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE user_id = ?");
        $stmt->execute([
            $_POST['name'],
            $_POST['email'],
            $_POST['role'],
            $_POST['id']
        ]);
    } elseif ($action === 'delete') {
        // Delete a user
        $stmt = $db->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$_POST['id']]);
    }
}

// Fetch all users
$stmt = $db->query("SELECT user_id AS id, name, email, role, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Manage Users</h1>

<!-- Create User Form -->
<form method="POST" class="row g-3 mb-4">
    <input type="hidden" name="action" value="create">
    <div class="col-md-3">
        <input type="text" name="name" class="form-control" placeholder="Name" required>
    </div>
    <div class="col-md-3">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
    </div>
    <div class="col-md-2">
        <select name="role" class="form-select" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
    </div>
    <div class="col-md-3">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
    </div>
    <div class="col-md-12">
        <button type="submit" class="btn btn-primary">Create User</button>
    </div>
</form>

<!-- Users Table -->
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td><?= htmlspecialchars($user['created_at']) ?></td>
                <td>
                    <!-- Update User Form -->
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="form-control mb-2" required>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control mb-2" required>
                        <select name="role" class="form-select mb-2" required>
                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                        <button type="submit" class="btn btn-warning btn-sm">Update</button>
                    </form>

                    <!-- Delete User Form -->
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>