<?php

require __DIR__ . '/../includes/config.php';

$cat = $_GET['cat'] ?? null;
$sort = $_GET['sort'] ?? 'default';
$limit = intval($_GET['limit'] ?? 10);

$where = $cat ? "WHERE p.category = :cat" : "";
$order = match ($sort) {
    'price-asc' => 'ORDER BY p.amount ASC',
    'price-desc' => 'ORDER BY p.amount DESC',
    'newest' => 'ORDER BY p.created_at DESC',
    default => ''
};

// Debugging logs
error_log("Category: " . ($cat ?? 'None'));
error_log("Sort: $sort");
error_log("Limit: $limit");

$totalStmt = $db->prepare("SELECT COUNT(*) FROM products p $where");
if ($cat) $totalStmt->execute([':cat' => $cat]);
else $totalStmt->execute();
$total = $totalStmt->fetchColumn();

$sql = "SELECT p.id, p.name, p.amount AS price, p.description, p.image_path AS image_url, c.name AS category_name
        FROM products p
        JOIN categories c ON p.category = c.id
        $where $order
        LIMIT :limit";
$stmt = $db->prepare($sql);
if ($cat) $stmt->bindValue(':cat', $cat, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debugging logs
error_log("SQL Query: $sql");
error_log("Fetched Items: " . json_encode($items));

// Map image_path to full URL; adjust if needed
$items = array_map(fn($i) => [
    ...$i,
    'image_url' => '/assets/images/products/' . $i['image_url']
], $items);

header('Content-Type: application/json');
echo json_encode(['total' => (int)$total, 'items' => $items]);
?>