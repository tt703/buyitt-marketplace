<?php

// chats.php - A chat interface for users to communicate about products in context
include "../includes/navbar.php";
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure the user is logged in
$from_user = $_SESSION['user_id'] ?? null;
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    $error = "You must be logged in to view this page.";
} else {
    // Fetch user data
    $stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $error = "User not found.";
    }
}

// Helper function to validate product existence in the context of the chat
function validateProductInContext($db, $productId, $fromUser, $toUser) {
    $stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM products p
        LEFT JOIN order_items oi ON oi.product_id = p.id
        LEFT JOIN orders o ON o.id = oi.order_id
        LEFT JOIN messages m ON m.product_id = p.id
        WHERE p.id = :product_id 
        AND (
            (p.user_id = :to_user AND o.buyer_id = :from_user) OR 
            (m.from_user = :from_user AND m.to_user = :to_user AND m.product_id = :product_id)
        )
    ");
    $stmt->execute([
        ':product_id' => $productId,
        ':to_user' => $toUser,
        ':from_user' => $fromUser
    ]);
    return $stmt->fetchColumn() > 0;
}

// Helper function to insert a message
function insertMessage($db, $productId, $fromUser, $toUser, $message) {
    $stmt = $db->prepare("INSERT INTO messages (product_id, from_user, to_user, message, created_at, is_read) VALUES (?, ?, ?, ?, NOW(), 0)");
    $stmt->execute([$productId, $fromUser, $toUser, $message]);
}

// Handle POST request for sending messages
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to_user = intval($_POST['to_user'] ?? 0); // Recipient's ID
    $product_id = intval($_POST['product_id'] ?? 0); // Product ID
    $message = trim($_POST['message'] ?? ''); // Message content

    if ($from_user && $to_user && $message !== '') {
        try {
            // Validate product in the context of the chat
            if (!validateProductInContext($db, $product_id, $from_user, $to_user)) {
                throw new Exception("The selected product is not valid in the context of this chat.");
            }

            // Insert the message into the database
            insertMessage($db, $product_id, $from_user, $to_user, $message);

            // Redirect to the chat page
            header("Location: chats.php?with_user={$to_user}&product_id={$product_id}");
            exit();
        } catch (PDOException $e) {
            error_log("Error inserting message: " . $e->getMessage());
            $error = "An error occurred while sending the message. Please try again.";
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    } else {
        $error = "Error: Missing recipient, sender, or message content.";
    }
}

// Fetch conversations
try {
    $partnersStmt = $db->prepare("
        SELECT 
            CASE 
                WHEN from_user = :me THEN to_user 
                ELSE from_user 
            END AS partner_id, 
            u.name AS partner_name, 
            MAX(m.created_at) AS last_message_at 
        FROM messages m 
        JOIN users u ON u.user_id = (
            CASE 
                WHEN m.from_user = :me THEN m.to_user 
                ELSE m.from_user 
            END
        )
        WHERE from_user = :me OR to_user = :me 
        GROUP BY partner_id, u.name 
        ORDER BY last_message_at DESC
    ");
    $partnersStmt->execute([':me' => $from_user]);
    $partners = $partnersStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching conversations: " . $e->getMessage());
    $partners = [];
}

// Fetch conversation thread if selected
$withUser = intval($_GET['with_user'] ?? 0);
$productId = intval($_GET['product_id'] ?? 0);
$messages = [];

if ($withUser) {
    try {
        $threadStmt = $db->prepare("
            SELECT m.*, u_from.name AS from_name, u_to.name AS to_name, p.name AS product_name 
            FROM messages m 
            JOIN users u_from ON u_from.user_id = m.from_user
            JOIN users u_to ON u_to.user_id = m.to_user 
            LEFT JOIN products p ON p.id = m.product_id
            WHERE 
                (from_user = :me AND to_user = :other) OR 
                (from_user = :other AND to_user = :me)
            ORDER BY m.created_at ASC
        ");
        $threadStmt->execute([
            ':me' => $from_user,
            ':other' => $withUser
        ]);
        $messages = $threadStmt->fetchAll(PDO::FETCH_ASSOC);

        // Mark messages as read
        $updateStmt = $db->prepare("
            UPDATE messages 
            SET is_read = 1 
            WHERE from_user = :other AND to_user = :me
        ");
        $updateStmt->execute([
            ':other' => $withUser,
            ':me' => $from_user
        ]);
    } catch (PDOException $e) {
        error_log("Error fetching conversation thread: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chats - BuyItt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/public.css?v=1.0" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .partner-list { max-height: 80vh; overflow-y: auto; }
        .chat-window { max-height: 65vh; overflow-y: auto; background-color: #f8f9fa; padding: 10px; border-radius: 5px; }
        .msg-me { text-align: right; margin-bottom: 10px; }
        .msg-you { text-align: left; margin-bottom: 10px; }
        .msg-me div, .msg-you div { display: inline-block; padding: 8px 12px; border-radius: 12px; max-width: 75%; }
        .msg-me div { background-color: #d1e7dd; }
        .msg-you div { background-color: #e2e3e5; }
        .chat-input { position: sticky; bottom: 0; background-color: #fff; padding: 10px; border-top: 1px solid #ddd; }
        .chat-input form { display: flex; gap: 10px; align-items: center; }
        .chat-input textarea { flex-grow: 1; resize: none; }
        .chat-input button { white-space: nowrap; }
    </style>
</head>
<body class="p-4">
<div class="row">
    <div class="col-md-4">
        <h5>Your Chats</h5>
        <ul class="list-group partner-list">
            <?php foreach ($partners as $p): ?>
                <a href="chats.php?with_user=<?= $p['partner_id'] ?>"
                   class="list-group-item list-group-item-action <?= $p['partner_id'] === $withUser ? 'active' : '' ?>">
                    <?= htmlspecialchars($p['partner_name']) ?><br>
                    <small class="text-muted"><?= $p['last_message_at'] ?></small>
                </a>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="col-md-8">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($withUser && $messages): ?>
            <h5>Conversation with <?= htmlspecialchars($partners[array_search($withUser, array_column($partners, 'partner_id'))]['partner_name'] ?? 'User') ?></h5>
            <div class="chat-window mb-3">
                <?php foreach ($messages as $m): ?>
                    <div class="<?= $m['from_user'] === $from_user ? 'msg-me' : 'msg-you' ?>">
                        <small class="text-muted">
                            <?= $m['from_user'] === $from_user ? 'You' : htmlspecialchars($m['from_name']) ?>
                            (<?= $m['created_at'] ?>):
                            <?php if ($m['product_name']): ?>
                                <strong>[<?= htmlspecialchars($m['product_name']) ?>]</strong>
                            <?php endif; ?>
                        </small>
                        <div><?= nl2br(htmlspecialchars($m['message'])) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="chat-input">
                <form method="POST">
                    <input type="hidden" name="to_user" value="<?= $withUser ?>">
                    <div class="mb-2">
                        <label for="product_id" class="form-label visually-hidden">Product</label>
                        <select name="product_id" id="product_id" class="form-select">
                            <option value="0">General</option>
                            <?php
                            // Fetch products related to both from_user and to_user
                            $productsStmt = $db->prepare("
                                SELECT DISTINCT p.id, p.name 
                                FROM products p
                                LEFT JOIN order_items oi ON oi.product_id = p.id
                                LEFT JOIN orders o ON o.id = oi.order_id
                                LEFT JOIN messages m ON m.product_id = p.id
                                WHERE 
                                    (p.user_id = :to_user AND o.buyer_id = :from_user) OR 
                                    (m.from_user = :from_user AND m.to_user = :to_user AND m.product_id = p.id)
                            ");
                            $productsStmt->execute([':from_user' => $from_user, ':to_user' => $withUser]);
                            $products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($products as $product): ?>
                                <option value="<?= $product['id'] ?>" <?= $product['id'] == $productId ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($product['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <textarea name="message" class="form-control" rows="2" placeholder="Type your message..." required></textarea>
                    <button class="btn btn-primary">Send</button>
                </form>
            </div>
        <?php else: ?>
            <p>Select a chat on the left to view or start messages.</p>
        <?php endif; ?>
    </div>
</div>
<?php include __DIR__ .  "/../includes/nav.php"; ?>
<script src="../assets/js/public.js?v=1.0"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>