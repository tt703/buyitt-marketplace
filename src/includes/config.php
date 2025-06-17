<?php
$url = getenv("DATABASE_URL");

if ($url) {
    $parts = parse_url($url);
    $host = $parts['host'];
    $port = $parts['port'];
    $user = $parts['user'];
    $pass = $parts['pass'];
    $db   = ltrim($parts['path'], '/');
} else {
    // Local fallback
    $host = 'localhost';
    $port = 3306;
    $user = 'root';
    $pass = 'rootpassword';
    $db   = 'buyitt';
}

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo "Database connection failed: " . $e->getMessage();
    exit;
}
?>
