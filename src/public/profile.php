<?php
// filepath: profile.php

require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/auth.php';
include "../includes/navbar.php";

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get user ID from session
$userId = $_SESSION['user_id'] ?? null;
$success = "";
$error = "";

if (!$userId) {
    $error = "You must be logged in to view this page.";
} else {
    // Fetch user data
    $stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $error = "User not found.";
    }
}

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] === "POST" && $user) {
    // Sanitize input
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address1 = trim($_POST['address1'] ?? '');
    $address2 = trim($_POST['address2'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');

    // Validate
    if ($name === "" || $phone === "" || $address1 === "" || $city === "" || $province === "" || $postal_code === "") {
        $error = "Please fill in all required fields.";
    } else {
        try {
            // Update
            $stmt = $db->prepare("UPDATE users SET name = ?, phone = ?, address1 = ?, address2 = ?, city = ?, province = ?, postal_code = ? WHERE user_id = ?");
            $stmt->execute([$name, $phone, $address1, $address2, $city, $province, $postal_code, $userId]);

            // Refresh user
            $stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $success = "Profile updated successfully.";
        } catch (PDOException $e) {
            $error = "Update failed: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Profile - BuyItt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/public.css?v=1.0" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body>
<div class="container mt-5">
    <h2>My Profile</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($user)): ?>
        <form method="POST" autocomplete="off">
            <input class="form-control mb-2" name="name" value="<?= htmlspecialchars($user['name']) ?>" placeholder="Full Name" required>
            <input class="form-control mb-2" name="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
            <input class="form-control mb-2" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" placeholder="Phone Number" required>
            <input class="form-control mb-2" name="address1" value="<?= htmlspecialchars($user['address1']) ?>" placeholder="Address Line 1" required>
            <input class="form-control mb-2" name="address2" value="<?= htmlspecialchars($user['address2']) ?>" placeholder="Address Line 2">
            <input class="form-control mb-2" name="city" value="<?= htmlspecialchars($user['city']) ?>" placeholder="City" required>
            <input class="form-control mb-2" name="province" value="<?= htmlspecialchars($user['province']) ?>" placeholder="Province" required>
            <input class="form-control mb-2" name="postal_code" value="<?= htmlspecialchars($user['postal_code']) ?>" placeholder="Postal Code" required>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    <?php endif; ?>
</div>
<?php include __DIR__ . "/../includes/nav.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.js"></script>
<script src="../assets/js/public.js?v=1.0"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
