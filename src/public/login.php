<?php
// filepath: c:\Users\tman1\OneDrive\Documents\buyitt-marketplace\src\public\login.php
require __DIR__ . '/../includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (empty($password)) {
        $error = "Password cannot be empty.";
    } else {
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: ../admin/index.php");
            } else {
                header("Location: ../public/index.php");
            }
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login - BuyItt</title>
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

        .login-box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 400px;
            width: 100%;
            z-index: 1;
        }

        .login-box h2 {
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

        .register-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            color: #ff7f00;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
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
            "bi-plant-fill", "bi-lightbulb-fill", "bi-tools", "bi-people-fill",
            "bi-geo-alt-fill", "bi-briefcase-fill", "bi-bell-fill", "bi-calendar-fill",
            "bi-chat-left-text-fill", "bi-envelope-fill", "bi-gear-fill", "bi-heart-fill",
        ];
        for ($i = 0; $i < 147; $i++): ?>
            <div class="icon"><i class="bi <?= $icons[$i % count($icons)] ?>"></i></div>
        <?php endfor; ?>
    </div>

    <!-- Login Card -->
    <div class="login-box">
        <h2>Login to BuyItt</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="you@example.com" required/>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required/>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
        </form>
        <div class="register-link">
            <p>Don't have an account? <a href="register.php">Register here</a>.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
