<?php
session_start();
include("../includes/config.php");

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $phone = trim($_POST['phone']);
    $address1 = trim($_POST['address1']);
    $address2 = trim($_POST['address2']);
    $city = trim($_POST['city']);
    $province = trim($_POST['province']);
    $postal_code = trim($_POST['postal_code']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Password confirmation
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = $db->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare("INSERT INTO users (name, email, role, phone, address1, address2, city, province, postal_code, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $role, $phone, $address1, $address2, $city, $province, $postal_code, $hashed_password]);
            $_SESSION['user'] = ['name' => $name, 'role' => $role];
            if ($user['role'] == 'admin') {
            header("Location: ../admin/index.php");
        } else {
            header("Location: ../public/index.php");
        }
            exit();
        }
        
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Register - BuyItt</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include("../includes/navbar.php"); ?>
        <div class="container mt-5">
            <h2>Register</h2>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST">
                <input class="form-control mb-2" name="name" placeholder="Full Name" required>
                <select name="role" class="form-control mb-2" required>
                    <option value="buyer">Buyer</option>
                    <option value="seller">Seller</option>
                </select>
                <input class="form-control mb-2" name="email" placeholder="Email" type="email" required>
                <input class="form-control mb-2" name="phone" placeholder="Phone Number" type="tel" required>
                <input class="form-control mb-2" name="address1" placeholder="Address Line 1" required>
                <input class="form-control mb-2" name="address2" placeholder="Address Line 2">
                <input class="form-control mb-2" name="city" placeholder="City" required>
                <input class="form-control mb-2" name="province" placeholder="Province" required>
                <input class="form-control mb-2" name="postal_code" placeholder="Postal Code" required>
                <input class="form-control mb-2" name="password" placeholder="Password" type="password" required>
                <input class="form-control mb-2" name="confirm_password" placeholder="Confirm Password" type="password" required>
                <button type="submit" class="btn btn-primary">Register</button>
                <p class="mt-3">Already have an account? <a href="login.php">Login here</a></p>
            </form>
        </div>
    </body>
</html>