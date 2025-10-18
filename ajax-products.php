<?php
require_once 'config.php';

header('Content-Type: application/json');

$category_id = $_GET['category'];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 6;
$offset = ($page - 1) * $per_page;

$conn = getDBConnection();

// Get total count
$total_result = $conn->query("SELECT COUNT(*) as count FROM products WHERE category_id = $category_id");
$total = $total_result->fetch_assoc()['count'];

// Get products
$products_result = $conn->query("SELECT * FROM products WHERE category_id = $category_id LIMIT $offset, $per_page");

$products = [];
while ($product = $products_result->fetch_assoc()) {
    $products[] = $product;
}

echo json_encode([
    'products' => $products,
    'total' => $total,
    'page' => $page,
    'per_page' => $per_page
]);

$conn->close();
?>
