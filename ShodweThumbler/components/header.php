<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shodwe Tumbler Hub</title>
    <link rel="stylesheet" href="/ShodweThumbler/assets/css/style.css">
</head>
<body>

<header class="header">
    <div class="container navbar">
        <a href="/ShodweThumbler/index.php" class="logo">
            <div class="logo-icon">S</div>
            Shodwe.
        </a>
        
        <nav class="nav-links">
            <a href="/ShodweThumbler/index.php" class="<?= isActivePage('index') ?>">Beranda</a>
            <a href="/ShodweThumbler/toko.php" class="<?= isActivePage('toko') ?>">Toko</a>
            <a href="/ShodweThumbler/tentang-kami.php" class="<?= isActivePage('tentang-kami') ?>">Tentang Kami</a>
            <a href="/ShodweThumbler/kontak.php" class="<?= isActivePage('kontak') ?>">Kontak</a>
        </nav>
        
        <div class="nav-actions">
            <a href="#" class="btn-icon" data-search-toggle title="Cari Produk">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </a>
            
            <?php if (isLoggedIn()): ?>
                <?php if (isAdmin()): ?>
                    <a href="/ShodweThumbler/admin/index.php" class="btn-icon" title="Admin Dashboard">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    </a>
                <?php else: ?>
                    <a href="/ShodweThumbler/track-order.php" class="btn-icon" title="Track Order">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    </a>
                <?php endif; ?>
                <a href="/ShodweThumbler/logout.php" class="btn-icon" title="Logout">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                </a>
            <?php else: ?>
                <a href="/ShodweThumbler/login.php" class="btn-icon" title="Login">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </a>
            <?php endif; ?>
            
            <a href="#" class="btn-icon" style="position: relative;" data-open-cart title="Keranjang">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                <span class="cart-badge" style="position: absolute; top: -5px; right: -5px; background: var(--primary-color); color: white; font-size: 10px; border-radius: 50%; width: 16px; height: 16px; display: none; align-items: center; justify-content: center;">0</span>
            </a>
        </div>
    </div>
</header>

<!-- Search Overlay -->
<div class="search-overlay">
    <div class="search-box">
        <input type="text" placeholder="Cari tumbler, warna, kategori..." autofocus>
    </div>
</div>

<!-- Cart Overlay -->
<div class="cart-overlay"></div>

<!-- Cart Slide Panel -->
<div class="cart-panel">
    <div class="cart-panel-header">
        <h3>🛒 Keranjang Belanja</h3>
        <button onclick="Cart.togglePanel()" style="background:none;border:none;cursor:pointer;color:var(--text-muted);padding:0.25rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    <div class="cart-panel-body">
        <!-- Cart items rendered by JS -->
    </div>
    <div class="cart-panel-footer">
        <!-- Cart footer rendered by JS -->
    </div>
</div>
