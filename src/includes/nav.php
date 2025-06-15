<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$loggedIn = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? '';
?>
<nav class="bottom-nav">
    <a href="index.php" class="nav-item" data-require-login="false">
        <i class="bi bi-house-fill"></i>
        <span>Home</span>
    </a>
    <a href="cart.php" class="nav-item" data-require-login="false">
        <i class="bi bi-cart-fill"></i>
        <span>Cart</span>
    </a>
    <a href="chats.php" class="nav-item" data-require-login="false">
        <i class="bi bi-chat-dots-fill"></i>
        <span>Chats</span>
    </a>
    <?php if ($loggedIn && $role === 'seller'):?>
        <a href="seller_dashboard.php" class="nav-item" data-require-login="false">
        <i class="bi bi-card-checklist"></i>
        <span>Dashboard</span>
        </a>
    <?php else: ?>
        <a href="orders.php" class="nav-item" data-require-login="false">
        <i class="bi bi-bag-fill"></i>
        <span>Orders</span>
        </a>
    <?php endif;?>
    <a href="profile.php" class="nav-item" data-require-login="false">
        <i class="bi bi-person-fill"></i>
        <span>Profile</span>
    </a>
</nav>