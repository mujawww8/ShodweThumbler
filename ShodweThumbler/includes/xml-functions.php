<?php
/**
 * XML CRUD Functions for Shodwe Tumbler Hub
 * Provides Create, Read, Update, Delete operations for XML databases
 */

// Base path for data files
define('DATA_PATH', __DIR__ . '/../data/');

/**
 * Load XML file and return SimpleXMLElement
 */
function loadXML($filename) {
    $filepath = DATA_PATH . $filename;
    if (!file_exists($filepath)) {
        return false;
    }
    return simplexml_load_file($filepath);
}

/**
 * Save SimpleXMLElement to XML file
 */
function saveXML($xml, $filename) {
    $filepath = DATA_PATH . $filename;
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    return $dom->save($filepath);
}

// ==================== PRODUCTS ====================

function getAllProducts() {
    $xml = loadXML('products.xml');
    if (!$xml) return [];
    $products = [];
    foreach ($xml->product as $product) {
        $products[] = xmlToArray($product);
    }
    return $products;
}

function getProductById($id) {
    $xml = loadXML('products.xml');
    if (!$xml) return null;
    foreach ($xml->product as $product) {
        if ((string)$product->id === $id) {
            return xmlToArray($product);
        }
    }
    return null;
}

function getProductsByCategory($category) {
    $products = getAllProducts();
    if ($category === 'All' || $category === '') return $products;
    return array_filter($products, function($p) use ($category) {
        return $p['category'] === $category;
    });
}

function getProductsByFilter($filters = []) {
    $products = getAllProducts();
    
    if (!empty($filters['category']) && $filters['category'] !== 'All') {
        $products = array_filter($products, function($p) use ($filters) {
            return $p['category'] === $filters['category'];
        });
    }
    if (!empty($filters['color'])) {
        $products = array_filter($products, function($p) use ($filters) {
            return $p['color'] === $filters['color'];
        });
    }
    if (!empty($filters['type'])) {
        $products = array_filter($products, function($p) use ($filters) {
            return $p['type'] === $filters['type'];
        });
    }
    if (!empty($filters['min_price'])) {
        $products = array_filter($products, function($p) use ($filters) {
            return (int)$p['price'] >= (int)$filters['min_price'];
        });
    }
    if (!empty($filters['max_price'])) {
        $products = array_filter($products, function($p) use ($filters) {
            return (int)$p['price'] <= (int)$filters['max_price'];
        });
    }
    if (!empty($filters['search'])) {
        $search = strtolower($filters['search']);
        $products = array_filter($products, function($p) use ($search) {
            return strpos(strtolower($p['name']), $search) !== false ||
                   strpos(strtolower($p['description']), $search) !== false;
        });
    }
    
    return array_values($products);
}

function addProduct($data) {
    $xml = loadXML('products.xml');
    if (!$xml) {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><products></products>');
    }
    
    $product = $xml->addChild('product');
    $product->addChild('id', $data['id']);
    $product->addChild('name', htmlspecialchars($data['name']));
    $product->addChild('category', htmlspecialchars($data['category']));
    $product->addChild('price', $data['price']);
    $product->addChild('stock', $data['stock']);
    $product->addChild('status', $data['status'] ?? 'Aktif');
    $product->addChild('color', htmlspecialchars($data['color'] ?? ''));
    $product->addChild('type', $data['type'] ?? 'Non-custom');
    $product->addChild('capacity', $data['capacity'] ?? '500ml');
    $product->addChild('rating', '0');
    $product->addChild('reviews', '0');
    $product->addChild('image', $data['image'] ?? 'tumbler_cream.png');
    $product->addChild('description', htmlspecialchars($data['description'] ?? ''));
    $product->addChild('tags', htmlspecialchars($data['tags'] ?? ''));
    $product->addChild('custom_name', $data['custom_name'] ?? '0');
    $product->addChild('created_at', date('Y-m-d'));
    
    return saveXML($xml, 'products.xml');
}

function updateProduct($id, $data) {
    $xml = loadXML('products.xml');
    if (!$xml) return false;
    
    foreach ($xml->product as $product) {
        if ((string)$product->id === $id) {
            foreach ($data as $key => $value) {
                if ($key !== 'id' && isset($product->$key)) {
                    $product->$key = htmlspecialchars($value);
                }
            }
            return saveXML($xml, 'products.xml');
        }
    }
    return false;
}

function deleteProduct($id) {
    $xml = loadXML('products.xml');
    if (!$xml) return false;
    
    $index = 0;
    foreach ($xml->product as $product) {
        if ((string)$product->id === $id) {
            unset($xml->product[$index]);
            return saveXML($xml, 'products.xml');
        }
        $index++;
    }
    return false;
}

// ==================== USERS ====================

function getAllUsers($role = null) {
    $xml = loadXML('users.xml');
    if (!$xml) return [];
    $users = [];
    foreach ($xml->user as $user) {
        $u = xmlToArray($user);
        if ($role === null || $u['role'] === $role) {
            $users[] = $u;
        }
    }
    return $users;
}

function getUserByEmail($email) {
    $xml = loadXML('users.xml');
    if (!$xml) return null;
    foreach ($xml->user as $user) {
        if ((string)$user->email === $email) {
            return xmlToArray($user);
        }
    }
    return null;
}

function getUserById($id) {
    $xml = loadXML('users.xml');
    if (!$xml) return null;
    foreach ($xml->user as $user) {
        if ((string)$user->id === $id) {
            return xmlToArray($user);
        }
    }
    return null;
}

function addUser($data) {
    $xml = loadXML('users.xml');
    if (!$xml) {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><users></users>');
    }
    
    // Check if email already exists
    foreach ($xml->user as $user) {
        if ((string)$user->email === $data['email']) {
            return false;
        }
    }
    
    $count = count($xml->user) + 1;
    $user = $xml->addChild('user');
    $user->addChild('id', 'USR-' . str_pad($count, 3, '0', STR_PAD_LEFT));
    $user->addChild('name', htmlspecialchars($data['name']));
    $user->addChild('email', htmlspecialchars($data['email']));
    $user->addChild('phone', htmlspecialchars($data['phone'] ?? ''));
    $user->addChild('password', $data['password']);
    $user->addChild('role', 'customer');
    $user->addChild('status', 'Aktif');
    $user->addChild('total_spent', '0');
    $user->addChild('total_orders', '0');
    $user->addChild('membership', 'Aktif');
    $user->addChild('joined', date('Y-m-d'));
    $user->addChild('avatar', 'default');
    
    return saveXML($xml, 'users.xml');
}

function updateUser($id, $data) {
    $xml = loadXML('users.xml');
    if (!$xml) return false;
    
    foreach ($xml->user as $user) {
        if ((string)$user->id === $id) {
            foreach ($data as $key => $value) {
                if ($key !== 'id' && isset($user->$key)) {
                    $user->$key = htmlspecialchars($value);
                }
            }
            return saveXML($xml, 'users.xml');
        }
    }
    return false;
}

function deleteUser($id) {
    $xml = loadXML('users.xml');
    if (!$xml) return false;
    
    $index = 0;
    foreach ($xml->user as $user) {
        if ((string)$user->id === $id) {
            unset($xml->user[$index]);
            return saveXML($xml, 'users.xml');
        }
        $index++;
    }
    return false;
}

// ==================== ORDERS ====================

function getAllOrders() {
    $xml = loadXML('orders.xml');
    if (!$xml) return [];
    $orders = [];
    foreach ($xml->order as $order) {
        $orders[] = xmlToArray($order);
    }
    return $orders;
}

function getOrderById($id) {
    $xml = loadXML('orders.xml');
    if (!$xml) return null;
    foreach ($xml->order as $order) {
        if ((string)$order->id === $id) {
            return xmlToArray($order);
        }
    }
    return null;
}

function addOrder($data) {
    $xml = loadXML('orders.xml');
    if (!$xml) {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><orders></orders>');
    }
    
    // Generate unique order ID
    $orderId = 'ORD-' . rand(10000, 99999);
    
    $order = $xml->addChild('order');
    $order->addChild('id', $orderId);
    foreach ($data as $key => $value) {
        if ($key !== 'id') {
            $order->addChild($key, htmlspecialchars($value));
        }
    }
    $order->addChild('date', date('Y-m-d'));
    
    if (saveXML($xml, 'orders.xml')) {
        return $orderId;
    }
    return false;
}

function updateOrder($id, $data) {
    $xml = loadXML('orders.xml');
    if (!$xml) return false;
    
    foreach ($xml->order as $order) {
        if ((string)$order->id === $id) {
            foreach ($data as $key => $value) {
                if ($key !== 'id' && isset($order->$key)) {
                    $order->$key = htmlspecialchars($value);
                }
            }
            return saveXML($xml, 'orders.xml');
        }
    }
    return false;
}

function deleteOrder($id) {
    $xml = loadXML('orders.xml');
    if (!$xml) return false;
    
    $index = 0;
    foreach ($xml->order as $order) {
        if ((string)$order->id === $id) {
            unset($xml->order[$index]);
            return saveXML($xml, 'orders.xml');
        }
        $index++;
    }
    return false;
}

// ==================== PAYMENTS ====================

function getAllPayments() {
    $xml = loadXML('payments.xml');
    if (!$xml) return [];
    $payments = [];
    foreach ($xml->payment as $payment) {
        $payments[] = xmlToArray($payment);
    }
    return $payments;
}

function updatePaymentStatus($id, $status) {
    $xml = loadXML('payments.xml');
    if (!$xml) return false;
    
    foreach ($xml->payment as $payment) {
        if ((string)$payment->id === $id) {
            $payment->status = $status;
            return saveXML($xml, 'payments.xml');
        }
    }
    return false;
}

// ==================== CONTACTS ====================

function getAllContacts() {
    $xml = loadXML('contacts.xml');
    if (!$xml) return [];
    $contacts = [];
    foreach ($xml->contact as $contact) {
        $contacts[] = xmlToArray($contact);
    }
    return $contacts;
}

function addContact($data) {
    $xml = loadXML('contacts.xml');
    if (!$xml) {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><contacts></contacts>');
    }
    
    $count = count($xml->contact) + 1;
    $contact = $xml->addChild('contact');
    $contact->addChild('id', 'CTN-' . str_pad($count, 3, '0', STR_PAD_LEFT));
    $contact->addChild('name', htmlspecialchars($data['name']));
    $contact->addChild('email', htmlspecialchars($data['email']));
    $contact->addChild('subject', htmlspecialchars($data['subject']));
    $contact->addChild('message', htmlspecialchars($data['message']));
    $contact->addChild('date', date('Y-m-d'));
    $contact->addChild('status', 'Unread');
    
    return saveXML($xml, 'contacts.xml');
}

// ==================== REVIEWS ====================

function getAllReviews() {
    $xml = loadXML('reviews.xml');
    if (!$xml) return [];
    $reviews = [];
    foreach ($xml->review as $review) {
        $reviews[] = xmlToArray($review);
    }
    return $reviews;
}

// ==================== HELPERS ====================

function xmlToArray($xmlElement) {
    $array = [];
    foreach ($xmlElement->children() as $key => $value) {
        $array[$key] = (string)$value;
    }
    return $array;
}

function generateId($prefix, $filename) {
    $xml = loadXML($filename);
    if (!$xml) return $prefix . '001';
    $count = count($xml->children()) + 1;
    return $prefix . str_pad($count, 3, '0', STR_PAD_LEFT);
}

function formatRupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}

// ==================== SETTINGS ====================

function getSettings() {
    $filepath = DATA_PATH . 'settings.xml';
    if (!file_exists($filepath)) return [];
    $xml = simplexml_load_file($filepath);
    if (!$xml) return [];
    return $xml;
}

function updateSettings($section, $data) {
    $filepath = DATA_PATH . 'settings.xml';
    if (!file_exists($filepath)) return false;
    $xml = simplexml_load_file($filepath);
    if (!$xml) return false;

    if ($section === 'general') {
        foreach ($data as $key => $value) {
            if (isset($xml->general->$key)) {
                $xml->general->$key = htmlspecialchars($value);
            }
        }
    }

    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml->asXML());
    return $dom->save($filepath);
}

// ==================== PRODUCT COLORS ====================

function getProductColors() {
    $products = getAllProducts();
    $colors = [];
    foreach ($products as $p) {
        if (!empty($p['color']) && !in_array($p['color'], $colors)) {
            $colors[] = $p['color'];
        }
    }
    return $colors;
}

// ==================== DASHBOARD IMAGES ====================

function getAllDashboardImages() {
    $xml = loadXML('dashboard_images.xml');
    if (!$xml) return [];
    $images = [];
    foreach ($xml->image as $image) {
        $images[] = xmlToArray($image);
    }
    return $images;
}

function getDashboardImageById($id) {
    $xml = loadXML('dashboard_images.xml');
    if (!$xml) return null;
    foreach ($xml->image as $image) {
        if ((string)$image->id === $id) {
            return xmlToArray($image);
        }
    }
    return null;
}

function getDashboardImagesByPage($page) {
    $images = getAllDashboardImages();
    return array_filter($images, function($img) use ($page) {
        return $img['page'] === $page;
    });
}

function getDashboardImage($page, $section) {
    $images = getAllDashboardImages();
    foreach ($images as $img) {
        if ($img['page'] === $page && $img['section'] === $section && $img['status'] === 'Aktif') {
            return $img;
        }
    }
    return null;
}

function addDashboardImage($data) {
    $xml = loadXML('dashboard_images.xml');
    if (!$xml) {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><dashboard_images></dashboard_images>');
    }
    
    // Generate unique ID
    $maxNum = 0;
    foreach ($xml->image as $img) {
        $num = (int)str_replace('IMG-', '', (string)$img->id);
        if ($num > $maxNum) $maxNum = $num;
    }
    $newId = 'IMG-' . str_pad($maxNum + 1, 3, '0', STR_PAD_LEFT);
    
    $image = $xml->addChild('image');
    $image->addChild('id', $newId);
    $image->addChild('section', htmlspecialchars($data['section']));
    $image->addChild('page', htmlspecialchars($data['page']));
    $image->addChild('title', htmlspecialchars($data['title']));
    $image->addChild('description', htmlspecialchars($data['description'] ?? ''));
    $image->addChild('filename', htmlspecialchars($data['filename']));
    $image->addChild('alt_text', htmlspecialchars($data['alt_text'] ?? ''));
    $image->addChild('status', $data['status'] ?? 'Aktif');
    $image->addChild('updated_at', date('Y-m-d'));
    
    return saveXML($xml, 'dashboard_images.xml') ? $newId : false;
}

function updateDashboardImage($id, $data) {
    $xml = loadXML('dashboard_images.xml');
    if (!$xml) return false;
    
    foreach ($xml->image as $image) {
        if ((string)$image->id === $id) {
            foreach ($data as $key => $value) {
                if ($key !== 'id' && isset($image->$key)) {
                    $image->$key = htmlspecialchars($value);
                }
            }
            $image->updated_at = date('Y-m-d');
            return saveXML($xml, 'dashboard_images.xml');
        }
    }
    return false;
}

function deleteDashboardImage($id) {
    $xml = loadXML('dashboard_images.xml');
    if (!$xml) return false;
    
    $index = 0;
    foreach ($xml->image as $image) {
        if ((string)$image->id === $id) {
            // Get filename to delete file
            $filename = (string)$image->filename;
            unset($xml->image[$index]);
            if (saveXML($xml, 'dashboard_images.xml')) {
                // Delete uploaded file if it exists in uploads folder
                $uploadPath = __DIR__ . '/../assets/uploads/' . $filename;
                if (file_exists($uploadPath)) {
                    unlink($uploadPath);
                }
                return true;
            }
            return false;
        }
        $index++;
    }
    return false;
}

