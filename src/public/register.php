<?php
require __DIR__ . '/../includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
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

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $db->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare("INSERT INTO users (name, email, role, phone, address1, address2, city, province, postal_code, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $role, $phone, $address1, $address2, $city, $province, $postal_code, $hashed_password]);

            // Redirect to login page after successful registration
            $_SESSION['message'] = "Registration successful! Please log in.";
            header("Location: login.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Register - BuyItt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
    <link href="../assets/css/public.css?v=1.0" rel="stylesheet"/>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #fff7f0, #ffe7d1);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .icon-layer {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-content: center;
            z-index: -1;
            pointer-events: none;
            padding: 0;
            gap: 30px;
        }

        .icon {
            font-size: 3rem;
            color: orange;
            opacity: 0.15;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-15px);
            }
        }

        .icon:nth-child(odd) {
            animation-delay: 2s;
        }

        .icon:nth-child(even) {
            animation-delay: 4s;
        }

        .register-box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 600px;
            width: 100%;
            z-index: 1;
        }

        .register-box h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }

        .btn-primary {
            background-color: #ff7f00;
            border: none;
        }

        .btn-primary:hover {
            background-color: #e26c00;
        }

        .form-control:focus {
            border-color: #ff7f00;
            box-shadow: 0 0 0 0.2rem rgba(255, 127, 0, 0.25);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: #ff7f00;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 12px;
        }
    </style>
</head>
<body>
    <!-- Background Icons -->
    <div class="icon-layer">
        <?php
        $icons = [
            "bi-house-fill", "bi-car-front-fill", "bi-bag-fill", "bi-phone-fill", 
            "bi-laptop-fill", "bi-camera-fill", "bi-headphones", "bi-watch", "bi-shirt",
            "bi-book-fill", "bi-gamepad-fill", "bi-music-note-beamed", "bi-palette-fill",
            "bi-plant-fill", "bi-lightbulb-fill", "bi-tools", 
        ];
        for ($i = 0; $i < 152; $i++): ?>
            <div class="icon"><i class="bi <?= $icons[$i % count($icons)] ?>"></i></div>
        <?php endfor; ?>
    </div>

    <!-- Register Card -->
    <div class="register-box">
        <h2>Create Your BuyItt Account</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-2"><input class="form-control" name="name" placeholder="Full Name" required></div>
            <div class="mb-2">
                <select name="role" class="form-select" required>
                    <option value="">Select Role</option>
                    <option value="buyer">Buyer</option>
                    <option value="seller">Seller</option>
                </select>
            </div>
            <div class="mb-2"><input class="form-control" name="email" type="email" placeholder="Email Address" required></div>
            <div class="mb-2"><input class="form-control" name="phone" type="tel" placeholder="Phone Number" required></div>
            <div class="mb-2"><input class="form-control" name="address1" placeholder="Address Line 1" required></div>
            <div class="mb-2"><input class="form-control" name="address2" placeholder="Address Line 2"></div>
            <div class="mb-2"><input class="form-control" name="city" placeholder="City" required></div>
            <div class="mb-2"><input class="form-control" name="province" placeholder="Province" required></div>
            <div class="mb-2"><input class="form-control" name="postal_code" placeholder="Postal Code" required></div>
            <div class="mb-2"><input class="form-control" name="password" type="password" placeholder="Password" required></div>
            <div class="mb-3"><input class="form-control" name="confirm_password" type="password" placeholder="Confirm Password" required></div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        <div class="login-link">
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>