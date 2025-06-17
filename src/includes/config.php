<?php

// Database configuration for BuyItt Marketplace

$db_host = getenv('DB_HOST') ?: 'localhost';
$db_port = getenv('DB_PORT') ?: '3306'; // Default to 3306 if not set
$db_name = getenv('DB_NAME') ?: 'buyitt';
$db_user = getenv('DB_USER') ?: 'buyitt_user';
$db_pass = getenv('DB_PASS') ?: 'secret';

// DSN must include port for Railway
$dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4";

try {
    $db = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    echo "Database connection error.";
    // In production, log error instead of displaying
    // error_log($e->getMessage());
    exit;
}
