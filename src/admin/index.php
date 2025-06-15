<?php
// filepath: c:\Users\tman1\OneDrive\Documents\buyitt-marketplace\src\admin\index.php
require __DIR__ . "/../includes/config.php";
require __DIR__ . "/../includes/auth.php";
include "../includes/navbar.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BuyItt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<a href="logout.php"button="logout"></a> 
<body>
    <div class="container mt-5">
        <div class="text-center mb-4">
            <h1 class="text-orange">Admin Dashboard</h1>
            <p class="text-muted">Welcome, <?= htmlspecialchars($user['name']) ?>! Manage the platform from here.</p>
        </div>

        <!-- Category Slider -->
        <div class="d-flex overflow-auto py-2 mb-4 category-slider">
            <button class="btn btn-outline-orange me-2 category-btn" data-page="manage_categories.php">Manage Categories</button>
            <button class="btn btn-outline-orange me-2 category-btn" data-page="manage_products.php">Manage Products</button>
            <button class="btn btn-outline-orange me-2 category-btn" data-page="manage_users.php">Manage Users</button>
            <button class="btn btn-outline-orange me-2 category-btn" data-page="manage_orders.php">Manage Orders</button>
            <button class="btn btn-outline-orange me-2 category-btn" data-page="manage_transaction.php">Manage Transactions</button>
        </div>

        <!-- Page Container -->
        <div id="page-container" class="card shadow-sm p-4">
            <p class="text-center text-muted">Select a category to manage from the slider above.</p>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
    const categoryButtons = document.querySelectorAll('.category-btn');
    const pageContainer = document.getElementById('page-container');

    // Function to load a page into the container
    const loadPage = (page) => {
        // Show a loading spinner while the page is being fetched
        pageContainer.innerHTML = `
            <div class="text-center my-5">
                <div class="spinner-border text-orange" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;

        fetch(page)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Failed to load ${page}: ${response.statusText}`);
                }
                return response.text();
            })
            .then(html => {
                pageContainer.innerHTML = html;
            })
            .catch(error => {
                pageContainer.innerHTML = `
                    <div class="alert alert-danger text-center">
                        <strong>Error:</strong> ${error.message}
                    </div>
                `;
            });
    };

    // Attach event listeners to category buttons
    categoryButtons.forEach(button => {
        button.addEventListener('click', () => {
            const page = button.dataset.page;

            // Highlight the selected button
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            // Load the selected page
            loadPage(page);
        });
    });

    // Automatically load the first page on initial load
    if (categoryButtons.length > 0) {
        categoryButtons[0].click();
    }
});
</script>
<style>
    /* Base Styling */
body {
    background-color: #f9f9f9;
    color: #333;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    padding: 1rem;
}

/* Theme Colors */
.text-orange {
    color: #ff6600;
}

.btn-orange {
    background-color: #ff6600;
    color: #fff;
    border: none;
}

.btn-orange:hover {
    background-color: #e65c00;
    color: #fff;
}

/* Headers */
h1, h2, h3 {
    color: #ff6f00;
    font-weight: 600;
    margin-bottom: 1rem;
}

/* Buttons */
.btn-primary {
    background-color: #ff6f00;
    border-color: #ff6f00;
    color: white;
}
.btn-outline-orange {
  color: orange;
  border-color: orange;
}

.btn-outline-orange:hover {
  background-color: orange;
  color: white;
}

.text-orange {
  color: orange;
}

.btn-primary:hover,
.btn-primary:focus {
    background-color: #e65c00;
    border-color: #e65c00;
}

.btn-secondary {
    background-color: #fff;
    color: #ff6f00;
    border: 2px solid #ff6f00;
}

.btn-secondary:hover {
    background-color: #ff6f00;
    color: white;
}

/* Danger button (Delete) */
.btn-danger {
    background-color: #e53935;
    border-color: #e53935;
}

.btn-danger:hover {
    background-color: #c62828;
    border-color: #c62828;
}

/* Card Styling */
.card {
    border: none;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.card-title {
    font-size: 1.25rem;
    font-weight: bold;
}

.card-text {
    font-size: 0.9rem;
    color: #666;
}

/* Utility Classes */
.text-muted {
    color: #888 !important;
}

/* Table Styling */
.table {
    border: 1px solid #dee2e6;
}

.table thead {
    background-color: #ff6f00;
    color: white;
}

.table-striped > tbody > tr:nth-child(odd) {
    background-color: #fff5e6;
}

/* Form Inputs */
input[type="text"],
input[type="number"],
input[type="email"],
textarea,
select {
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 0.4rem 0.6rem;
    font-size: 0.95rem;
}

/* Image in table */
img {
    border-radius: 4px;
    border: 1px solid #eee;
}

/* Admin Navigation Links */
.list-group-item a {
    text-decoration: none;
    color: #ff6f00;
    font-weight: 500;
}

.list-group-item a:hover {
    color: #e65c00;
}

/* Forms inside tables */
form.d-inline input,
form.d-inline textarea,
form.d-inline select {
    margin-bottom: 0.5rem;
}

/* Category Slider */
.category-slider {
    border-bottom: 2px solid #ff6600;
    padding-bottom: 10px;
}

.category-btn {
    border: 1px solid #ff6600;
    color: #ff6600;
    background-color: #fff;
    border-radius: 4px;
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.category-btn:hover {
    background-color: #ff6600;
    color: #fff;
}

.category-btn.active {
    background-color: #ff6600;
    color: #fff;
}

/* Page Container */
#page-container {
    min-height: 300px;
    background-color: #fff;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Responsive Design */
@media (max-width: 768px) {
    body {
        padding: 0.5rem;
    }

    h1 {
        font-size: 1.5rem;
    }

    .table {
        font-size: 0.85rem;
    }

    .category-btn {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
    }
}
</style>
</body>
</html>