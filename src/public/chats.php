<?php

require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = currentUser();
$sellerId = $_SESSION['user_id']; // Current logged-in user (seller or buyer)
$withUser = intval($_GET['with_user'] ?? 0); // The user you're chatting with
#$productId = intval($_GET['product_id'] ?? 0); // Optional product context

// Predefined message from product page or action
$preMessage = $_GET['message'] ?? null;
if ($preMessage && $withUser && $productId) {
    $stmt = $db->prepare("INSERT INTO messages (product_id, from_user, to_user, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$productId, $sellerId, $withUser, $preMessage]);
}

// Handle new messages
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $toUser = intval($_POST['to_user']);
    $prodId = intval($_POST['product_id']);
    $message = trim($_POST['message'] ?? '');

    if ($toUser && $prodId && $message !== '') {
        $stmt = $db->prepare("INSERT INTO messages (product_id, from_user, to_user, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$prodId, $sellerId, $toUser, $message]);
    }

    // Redirect to keep conversation going
    header("Location: chats.php?with_user={$toUser}&product_id={$prodId}");
    exit();
}

include "../includes/navbar.php";

// Fetch conversations
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
$partnersStmt->execute([':me' => $sellerId]);
$partners = $partnersStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch conversation thread if selected
$messages = [];
if ($withUser && $productId) {
    $threadStmt = $db->prepare("
        SELECT m.*, u_from.name AS from_name, u_to.name AS to_name 
        FROM messages m 
        JOIN users u_from ON u_from.user_id = m.from_user
        JOIN users u_to ON u_to.user_id = m.to_user 
        WHERE 
            ((from_user = :me AND to_user = :other) OR 
             (from_user = :other AND to_user = :me))
            AND m.product_id = :pid
        ORDER BY m.created_at ASC
    ");
    $threadStmt->execute([
        ':me' => $sellerId,
        ':other' => $withUser,
        ':pid' => $productId
    ]);
    $messages = $threadStmt->fetchAll(PDO::FETCH_ASSOC);

    // Mark messages as read
    $updateStmt = $db->prepare("
        UPDATE messages 
        SET is_read = 1 
        WHERE from_user = :other AND to_user = :me AND product_id = :pid
    ");
    $updateStmt->execute([
        ':other' => $withUser,
        ':me' => $sellerId,
        ':pid' => $productId
    ]);
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
        .partner-list {
            max-height: 80vh;
            overflow-y: auto;
        }
        .chat-window {
            max-height: 65vh;
            overflow-y: auto;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }
        .msg-me {
            text-align: right;
            margin-bottom: 10px;
        }
        .msg-you {
            text-align: left;
            margin-bottom: 10px;
        }
        .msg-me div,
        .msg-you div {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 12px;
            max-width: 75%;
        }
        .msg-me div {
            background-color: #d1e7dd;
        }
        .msg-you div {
            background-color: #e2e3e5;
        }
    </style>
</head>
<body class="p-4">
<div class="row">
    <!-- Sidebar: List of conversations -->
    <div class="col-md-4">
        <h5>Your Chats</h5>
        <ul class="list-group partner-list">
            <?php foreach ($partners as $p): ?>
                <a href="chats.php?with_user=<?= $p['partner_id'] ?>&product_id=<?= $productId ?>"
                   class="list-group-item list-group-item-action <?= $p['partner_id'] === $withUser ? 'active' : '' ?>">
                    <?= htmlspecialchars($p['partner_name']) ?><br>
                    <small class="text-muted"><?= $p['last_message_at'] ?></small>
                </a>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Chat area -->
    <div class="col-md-8">
        <?php if ($withUser && $messages): ?>
            <h5>
                Conversation with 
                <?= htmlspecialchars(array_values(array_filter($partners, fn($x) => $x['partner_id'] === $withUser))[0]['partner_name'] ?? 'User') ?>
            </h5>

            <div class="chat-window mb-3">
                <?php foreach ($messages as $m): ?>
                    <div class="<?= $m['from_user'] === $sellerId ? 'msg-me' : 'msg-you' ?>">
                        <small class="text-muted">
                            <?= $m['from_user'] === $sellerId ? 'You' : htmlspecialchars($m['from_name']) ?>
                            (<?= $m['created_at'] ?>):
                        </small>
                        <div><?= nl2br(htmlspecialchars($m['message'])) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Message form -->
            <form method="POST">
                <input type="hidden" name="to_user" value="<?= $withUser ?>">
                <input type="hidden" name="product_id" value="<?= $productId ?>">
                <div class="mb-2">
                    <textarea name="message" class="form-control" rows="3" required></textarea>
                </div>
                <button class="btn btn-primary">Send</button>
            </form>
        <?php else: ?>
            <p>Select a chat on the left to view or start messages.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Bottom navigation -->
<?php include __DIR__ . "/../includes/nav.php"; ?>
<script src="../assets/js/public.js?v=1.0"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
