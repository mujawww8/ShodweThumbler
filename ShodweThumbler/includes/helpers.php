<?php
/**
 * Helper functions for Shodwe Tumbler Hub
 */

/**
 * Sanitize user input
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

/**
 * Validate email format
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate phone number
 */
function validatePhone($phone) {
    return preg_match('/^(\+62|62|0)[\s-]?[0-9]{3,4}[\s-]?[0-9]{3,4}[\s-]?[0-9]{0,4}$/', str_replace([' ', '-'], '', $phone));
}

/**
 * Get current page name for navigation highlighting
 */
function getCurrentPage() {
    $page = basename($_SERVER['PHP_SELF'], '.php');
    return $page;
}

/**
 * Check if current page is active
 */
function isActivePage($pageName) {
    return getCurrentPage() === $pageName ? 'active' : '';
}

/**
 * Flash message system
 */
function setFlashMessage($type, $message) {
    if (!isset($_SESSION)) session_start();
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (!isset($_SESSION)) session_start();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Generate star rating HTML
 */
function renderStars($rating) {
    $html = '';
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    
    for ($i = 0; $i < $fullStars; $i++) {
        $html .= '<span class="star filled">★</span>';
    }
    if ($halfStar) {
        $html .= '<span class="star half">★</span>';
    }
    for ($i = $fullStars + ($halfStar ? 1 : 0); $i < 5; $i++) {
        $html .= '<span class="star">☆</span>';
    }
    return $html;
}

/**
 * Truncate text
 */
function truncateText($text, $maxLength = 100) {
    if (strlen($text) <= $maxLength) return $text;
    return substr($text, 0, $maxLength) . '...';
}

/**
 * Get status badge class
 */
function getStatusClass($status) {
    $statusMap = [
        'Pending' => 'status-pending',
        'Diproses' => 'status-processing',
        'Dikirim' => 'status-shipped',
        'Selesai' => 'status-completed',
        'Dibatalkan' => 'status-cancelled',
        'Aktif' => 'status-active',
        'Nonaktif' => 'status-inactive',
        'VIP' => 'status-vip',
        'Lunas' => 'status-paid',
        'Belum Bayar' => 'status-unpaid',
        'Batal' => 'status-cancelled',
        'Berhasil' => 'status-completed',
        'Menunggu Verifikasi' => 'status-pending',
        'Gagal / Expired' => 'status-cancelled',
        'Dikembalikan (Refund)' => 'status-refund',
    ];
    return $statusMap[$status] ?? 'status-default';
}
