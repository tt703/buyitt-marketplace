<?php
// filepath: c:\Users\tman1\OneDrive\Documents\buyitt-marketplace\src\includes\navbar.php
include __DIR__ . "/config.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get the current page for active link highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="/public/index.php">BuyItt</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['user_id'])): ?>
          <!-- Display user name and logout link -->
          <li class="nav-item">
            <span class="navbar-text me-3">
              Hello, <?= htmlspecialchars($_SESSION['name'] ?? $_SESSION['email']) ?>
            </span>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $currentPage === 'logout.php' ? 'active' : '' ?>" href="/public/logout.php">Logout</a>
          </li>
        <?php else: ?>
          <!-- Links for guests -->
          <li class="nav-item">
            <a class="nav-link <?= $currentPage === 'login.php' ? 'active' : '' ?>" href="/public/login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $currentPage === 'register.php' ? 'active' : '' ?>" href="/public/register.php">Register</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>