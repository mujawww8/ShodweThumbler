<?php
/**
 * Admin — Manage Product (Add, Edit, Delete)
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/xml-functions.php';
require_once __DIR__ . '/../includes/helpers.php';
requireAdmin();

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        $id = generateId('TMB-', 'products.xml');
        $data = [
            'id' => $id,
            'name' => $_POST['name'] ?? '',
            'category' => $_POST['category'] ?? 'Custom Tumbler',
            'price' => $_POST['price'] ?? 0,
            'stock' => $_POST['stock'] ?? 0,
            'status' => $_POST['status'] ?? 'Aktif',
            'color' => $_POST['color'] ?? '',
            'type' => $_POST['type'] ?? 'Non-custom',
            'capacity' => $_POST['capacity'] ?? '500ml',
            'image' => $_POST['image'] ?? 'tumbler_cream.png',
            'description' => $_POST['description'] ?? '',
            'tags' => $_POST['tags'] ?? '',
            'custom_name' => $_POST['custom_name'] ?? '0'
        ];
        
        if (addProduct($data)) {
            setFlashMessage('success', 'Produk "' . $data['name'] . '" berhasil ditambahkan!');
        } else {
            setFlashMessage('error', 'Gagal menambahkan produk.');
        }
        break;
    
    case 'edit':
        $id = $_POST['id'] ?? '';
        $data = [
            'name' => $_POST['name'] ?? '',
            'category' => $_POST['category'] ?? '',
            'price' => $_POST['price'] ?? 0,
            'stock' => $_POST['stock'] ?? 0,
            'status' => $_POST['status'] ?? 'Aktif',
            'color' => $_POST['color'] ?? '',
            'type' => $_POST['type'] ?? 'Non-custom',
            'capacity' => $_POST['capacity'] ?? '500ml',
            'description' => $_POST['description'] ?? '',
            'tags' => $_POST['tags'] ?? '',
            'custom_name' => $_POST['custom_name'] ?? '0'
        ];
        
        if (updateProduct($id, $data)) {
            setFlashMessage('success', 'Produk berhasil diperbarui!');
        } else {
            setFlashMessage('error', 'Gagal memperbarui produk.');
        }
        break;
    
    case 'delete':
        $id = $_POST['id'] ?? '';
        if (deleteProduct($id)) {
            setFlashMessage('success', 'Produk berhasil dihapus.');
        } else {
            setFlashMessage('error', 'Gagal menghapus produk.');
        }
        break;
    
    default:
        setFlashMessage('error', 'Aksi tidak valid.');
        break;
}

header('Location: /ShodweThumbler/admin/produk.php');
exit;
