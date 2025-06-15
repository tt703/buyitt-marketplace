<?php
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/auth.php';
header('Content-Type: application/json');

// Must be logged in
requireLogin();

// Get the current user
$user = currentUser();
$pid = intval($_POST['product_id'] ?? 0);
$to = intval($_POST['to_user'] ?? 0);
$msg = trim($_POST['message'] ?? '');

if (!$pid || !$to || !$msg) {
    echo json_encode(['error' => 'Invalid input']);
    exit();
}

try {
    // Insert message into the database
    $stmt = $db->prepare("INSERT INTO messages (product_id, from_user, to_user, message)
        VALUES (?, ?, ?, ?)");
    $stmt->execute([$pid, $user['user_id'], $to, $msg]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['error' => 'Failed to send message. Please try again later.']);
    exit();
}