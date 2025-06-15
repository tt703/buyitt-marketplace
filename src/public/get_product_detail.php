<?php
require __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['error' => 'Invalid product ID']);
    exit();
}
$stmt = $db->prepare("SELECT p.*,c.name AS category_name, u.name AS seller_name, u.user_id AS seller_id
FROM products p JOIN categories c ON p.category = c.id JOIN users u ON p.user_id = u.user_id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$product){
    echo json_encode(['error' => 'Product not found']);
    exit();
}

//Fetch 4 similar products(same category)
$sim = $db->prepare("SELECT p.id, p.name, p.amount AS price, p.image_path FROM products p
WHERE p.category = ? AND p.id != ? LIMIT 4");
$sim->execute([$product['category'], $id]);
$similar = $sim->fetchAll(PDO::FETCH_ASSOC);

//Build image URL, similar URLs
$product['image_url'] = '/assets/images/products/'.$product['image_path'];
foreach($similar as &$s){
    $s['image_url'] = '/assets/images/products/'.$s['image_path'];
}
unset($s);
echo json_encode([
    'product' => $product,
    'similar' => $similar
]);