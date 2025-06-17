<?php

// Database configuration for BuyItt Marketplace

$db_host = getenv('DB_HOST') ?: 'db';
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
    // In production, log the error instead of displaying it
    echo "Database connection error.";
    // error_log($e->getMessage());
    exit;
}