<?php


// Parse the Railway-provided MySQL URL
$db_url = getenv('MYSQL_URL');
if ($db_url) {
    $db_parts = parse_url($db_url);

    $db_host = $db_parts['host'];
    $db_name = ltrim($db_parts['path'], '/');
    $db_user = $db_parts['user'];
    $db_pass = $db_parts['pass'];
} else {
    // Fallback for local development
    $db_host = 'localhost';
    $db_name = 'buyitt';
    $db_user = 'root';
    $db_pass = '';
}

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