<?php

include "../includes/navbar.php";
// Include the database connection and authentication
require __DIR__ . "/../includes/config.php";
require __DIR__ . "/../includes/auth.php";

// Fetch category and limit from query parameters
$cat = $_GET['cat'] ?? null;
$limit = $_GET['limit'] ?? 10;

// Debugging logs (optional)
$sql = "SELECT * FROM products";
error_log("SQL: $sql");
error_log("Parameters: " . json_encode(value: ['cat' => $cat, 'limit' => $limit]));

// Fetch categories for the slider
$stmt = $db->query("SELECT id, name FROM categories ORDER BY created_at DESC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>BuyItt Marketplace</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/public.css?v=1.0"  rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

</head>

  <!-- Secondary Bar -->
  <div class="container mt-2">
    <div class="d-grid" style="grid-template-columns: 1fr 2fr 1fr; align-items: center;">
      <div>
        <h2 class="mb-0">BuyItt</h2>
      </div>
      <div>
        <input type="text" id="searchInput" class="form-control" placeholder="Search products...">
      </div>
      <div class="text-end">
        <a href="cart.php" class="btn btn-outline-primary">Cart</a>
      </div>
    </div>
  </div>

  <!-- Category Slider -->
  <div class="container mt-3" id="category-slider">
    <div class="d-flex overflow-auto py-2">
      <?php foreach ($categories as $cat): ?>
        <button class="btn btn-outline-secondary me-2 category-btn" data-cat-id="<?= $cat['id'] ?>">
          <?= htmlspecialchars($cat['name']) ?>
        </button>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Filter dropdown -->
  <div class="container mt-4 d-flex justify-content-between align-items-center">
    <div>
      <select id="sortFilter" class="form-select">
        <option value="default">Sort by</option>
        <option value="price-asc">Price: Low to High</option>
        <option value="price-desc">Price: High to Low</option>
        <option value="newest">Newest Arrivals</option>
      </select>
    </div>
    <div>
      <span id="showingCount">Showing 1-<span id="perPage">10</span> of <span id="totalCount">0</span></span>
    </div>
  </div>

  <!-- Product Grid -->
  <div class="container mt-3">
    <div class="row" id="productGrid">
      <!-- Product items will be dynamically inserted here -->
    </div>
  </div>

  <!-- Quick View Modal -->
  <div class="modal fade" id="quickViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="qv-title"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <!-- Product Image -->
            <div class="col-md-6 text-center">
              <img id="qv-image" src="" class="img-fluid" alt="">
            </div>
            <!-- Product Details and Chat -->
            <div class="col-md-6">
              <p><strong>Price:</strong> R<span id="qv-price"></span></p>
              <p><strong>Category:</strong> <span id="qv-category"></span></p>
              <p id="qv-desc"></p>
              <!-- Add to Cart button -->
              <button id="qv-add-cart" class="btn btn-primary mb-3">Add to Cart</button>
              <!-- Chat to seller button -->
              <h6>Chat with <span id="qv-seller"></span></h6>
              <a id="qv-chat-link" class="btn btn-outline-secondary" href="#">Go to Chat</a>
            </div>
          </div>
          <!-- Similar Products -->
          <hr>
          <h6>Similar Products</h6>
          <div class="row" id="qv-similar">
            <!-- Similar products will be dynamically inserted here -->
          </div>
        </div>
      </div>
    </div>
  </div>
<!-- Why Shop on BuyItt Section -->
<div class="container mt-5">
  <div class="row text-center">
    <!-- Wide Range of Products -->
    <div class="col-md-4 mb-4">
      <div class="card shadow-sm border-0 p-4 bg-white bg-opacity-75">
        <div class="icon mb-3">
          <i class="bi bi-box-seam text-orange fs-1"></i>
        </div>
        <h4 class="fw-bold">Wide Range of Products</h4>
        <p>Discover over <strong>200 products</strong> listed daily across multiple categories.</p>
      </div>
    </div>

    <!-- Trusted Sellers -->
    <div class="col-md-4 mb-4">
      <div class="card shadow-sm border-0 p-4 bg-white bg-opacity-75">
        <div class="icon mb-3">
          <i class="bi bi-shield-check text-orange fs-1"></i>
        </div>
        <h4 class="fw-bold">Trusted Sellers</h4>
        <p>Shop with confidence from verified sellers offering quality products.</p>
      </div>
    </div>

    <!-- Fresh Listings Daily -->
    <div class="col-md-4 mb-4">
      <div class="card shadow-sm border-0 p-4 bg-white bg-opacity-75">
        <div class="icon mb-3">
          <i class="bi bi-clock-history text-orange fs-1"></i>
        </div>
        <h4 class="fw-bold">Fresh Listings Daily</h4>
        <p>New products added every day to keep your shopping exciting and fresh.</p>
      </div>
    </div>
  </div>
</div>

<!-- Yoco Payment Section -->
<div class="container mt-5">
  <div class="card shadow-sm border-0 p-4 bg-light">
    <div class="row align-items-center">
      <!-- Yoco Image -->
      <div class="col-md-6 text-center">
        <img src="../assets/images/YOCO_Payment_Partner_Banner.png" alt="Yoco Payment" class="img-fluid rounded">
      </div>
      <!-- Yoco Payment Details -->
      <div class="col-md-6">
        <h4 class="fw-bold text-primary mt-3">Secure Payments with Yoco</h4>
        <p>All payments are securely processed through <strong>Yoco</strong>, ensuring fast and reliable transactions.</p>
        <ul class="list-unstyled">
          <li><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-1-square" viewBox="0 0 16 16">
  <path d="M9.283 4.002V12H7.971V5.338h-.065L6.072 6.656V5.385l1.899-1.383z"/>
  <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm15 0a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z"/>
</svg>    Safe and secure transactions</li>
          <li></i><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-2-square-fill" viewBox="0 0 16 16">
  <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm4.646 6.24v.07H5.375v-.064c0-1.213.879-2.402 2.637-2.402 1.582 0 2.613.949 2.613 2.215 0 1.002-.6 1.667-1.287 2.43l-.096.107-1.974 2.22v.077h3.498V12H5.422v-.832l2.97-3.293c.434-.475.903-1.008.903-1.705 0-.744-.557-1.236-1.313-1.236-.843 0-1.336.615-1.336 1.306"/>
</svg>    Easy refunds for hassle-free shopping</li>
          <li></i><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-3-square" viewBox="0 0 16 16">
  <path d="M7.918 8.414h-.879V7.342h.838c.78 0 1.348-.522 1.342-1.237 0-.709-.563-1.195-1.348-1.195-.79 0-1.312.498-1.348 1.055H5.275c.036-1.137.95-2.115 2.625-2.121 1.594-.012 2.608.885 2.637 2.062.023 1.137-.885 1.776-1.482 1.875v.07c.703.07 1.71.64 1.734 1.917.024 1.459-1.277 2.396-2.93 2.396-1.705 0-2.707-.967-2.754-2.144H6.33c.059.597.68 1.06 1.541 1.066.973.006 1.6-.563 1.588-1.354-.006-.779-.621-1.318-1.541-1.318"/>
  <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm15 0a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z"/>
</svg>    Trusted by thousands of users</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- Seller Motivation Section -->
<div class="container mt-5">
  <div class="card shadow-sm border-0 p-4">
    <div class="row align-items-center">
      <!-- Seller Motivation Image -->
      <div class="col-md-6 text-center">
        <img src="../assets/images/Become_seller.png" alt="Join BuyItt as a Seller" class="img-fluid rounded">
      </div>
      <!-- Seller Motivation Details -->
      <div class="col-md-6">
        <h4 class="fw-bold mt-3">Join BuyItt as a Seller</h4>
        <p>Grow your business by reaching thousands of customers every day. Our platform makes it easy to list your products and manage your inventory.</p>
        <li class="list-unstyled">
          <li><i class="bi bi-check-circle-fill text-orange"></i> Showcase your products to a wide audience</li>
          <li><i class="bi bi-check-circle-fill text-orange"></i> Manage your inventory effortlessly</li>
          <li><i class="bi bi-check-circle-fill text-orange"></i> Receive secure payments through Yoco</li>
      </li>
        <a href="/public/seller_register.php" class="btn btn-primary mt-3">Become a Seller</a>
      </div>
    </div>
  </div>
</div>

<!-- Flexible Delivery Options Section -->
<div class="container mt-5">
  <div class="card shadow-sm border-0 p-4">
    <div class="row align-items-center">
      <!-- Flexible Delivery Image -->
      <div class="col-md-6 text-center">
        <img src="../assets/images/delivery.png" alt="Flexible Delivery Options" class="img-fluid rounded">
      </div>
      <!-- Flexible Delivery Details -->
      <div class="col-md-6">
        <i class="bi bi-truck text-orange fs-1"></i>
        <h4 class="fw-bold mt-3">Flexible Delivery Options</h4>
        <p>Choose from our approved delivery providers or meet the seller directly for a convenient shopping experience.</p>
        <li class="list-unstyled">
          <li><i class="bi bi-check-circle-fill text-orange"></i> Trusted courier services like <strong>Courier Guy</strong></li>
          <li><i class="bi bi-check-circle-fill text-orange"></i> Meet the seller at their location</li>
          <li><i class="bi bi-check-circle-fill text-orange"></i> Seller-to-door delivery for convenience</li>
      </li>
        <a href="/public/delivery_options.php" class="btn btn-primary mt-3">Learn More</a>
      </div>
    </div>
  </div>
</div>
<!-- Footer -->
<footer class="buyitt-footer mt-5">
  <div class="container py-4">
    <div class="row">
      <div class="col-md-4 mb-3">
        <h5>BuyItt</h5>
        <p>Your trusted community marketplace to buy and sell everything!</p>
      </div>
      <div class="col-md-4 mb-3">
        <h6>Quick Links</h6>
        <ul class="list-unstyled">
          <li><a href="index.php">Home</a></li>
          <li><a href="categories.php">Categories</a></li>
          <li><a href="cart.php">Cart</a></li>
          <li><a href="contact.php">Contact Us</a></li>
        </ul>
      </div>
      <div class="col-md-4 mb-3">
        <h6>Follow Us</h6>
        <div class="social-icons">
          <a href="#"><i class="bi bi-facebook"></i></a>
          <a href="#"><i class="bi bi-instagram"></i></a>
          <a href="#"><i class="bi bi-twitter-x"></i></a>
        </div>
      </div>
    </div>
    <hr class="mt-3">
    <div class="text-center small text-muted">
      &copy; <?php echo date("Y"); ?> BuyItt Marketplace. All rights reserved.
    </div>
  </div>
</footer>

<!-- Include Bootstrap Icons if not already -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<!-- Footer CSS -->
<style>
  .buyitt-footer {
    background: #f8f9fa;
    color: #333;
    font-size: 0.95rem;
    border-top: 1px solid #ddd;
  }

  .buyitt-footer h5,
  .buyitt-footer h6 {
    font-weight: bold;
    margin-bottom: 0.75rem;
  }

  .buyitt-footer a {
    color: #ff6600;
    text-decoration: none;
  }

  .buyitt-footer a:hover {
    text-decoration: underline;
  }

  .buyitt-footer .social-icons a {
    font-size: 1.2rem;
    color: #333;
    margin-right: 10px;
    transition: color 0.3s;
  }

  .buyitt-footer .social-icons a:hover {
    color: #ff6600;
  }

  @media (max-width: 767px) {
    .buyitt-footer .row > div {
      text-align: center;
    }

    .buyitt-footer .social-icons {
      justify-content: center;
      display: flex;
    }
  }
</style>

<!--bottom bav -->
<?php include __DIR__ .  "/../includes/nav.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.js"></script>
<script src="../assets/js/public.js?v=1.0"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
 
</body>
</html>

