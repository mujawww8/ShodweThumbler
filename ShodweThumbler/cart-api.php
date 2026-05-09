<?php
/**
 * Cart API — Returns product data for client-side cart operations
 */
header('Content-Type: application/json');
require_once __DIR__ . '/includes/xml-functions.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_product':
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            echo json_encode(['error' => 'Product ID required']);
            exit;
        }
        $product = getProductById($id);
        if ($product) {
            echo json_encode(['success' => true, 'product' => $product]);
        } else {
            echo json_encode(['error' => 'Product not found']);
        }
        break;
    
    case 'get_all':
        $products = getAllProducts();
        echo json_encode(['success' => true, 'products' => $products]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
