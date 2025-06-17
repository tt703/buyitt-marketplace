<?php
// Database configuration for BuyItt Marketplace (Railway + Local Support)

// Prefer Railway environment variables, fallback to local Docker defaults
$db_host = getenv('DB_HOST') ?: '127.0.0.1';
$db_name = getenv('DB_NAME') ?: 'buyitt';
$db_user = getenv('DB_USER') ?: 'buyitt_user';
$db_pass = getenv('DB_PASS') ?: 'secret';

$dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";

try {
    $db = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    // For Railway or production, log instead of displaying
    if (getenv('RAILWAY_ENVIRONMENT')) {
        error_log('DB Error: ' . $e->getMessage());
        echo "Internal server error.";
    } else {
        // Local dev
        echo "Database connection failed: " . $e->getMessage();
    }
    exit;
}
