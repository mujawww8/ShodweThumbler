<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
requireAdmin();

function isDocAdminActive($doc) {
    return strpos($_SERVER['PHP_SELF'], $doc) !== false ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Shodwe Tumbler Hub</title>
    <link rel="stylesheet" href="/ShodweThumbler/assets/css/style.css">
</head>
<body>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <a href="/ShodweThumbler/index.php" class="logo">
                <div class="logo-icon">S</div>
                Shodwe Admin
            </a>
        </div>
        
        <nav class="admin-nav">
            <p style="padding: 0 1rem; font-size: 0.75rem; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem; margin-top: 1rem;">Main menu</p>
            
            <a href="/ShodweThumbler/admin/index.php" class="admin-nav-item <?= isDocAdminActive('admin/index') ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                Dashboard
            </a>
            
            <a href="/ShodweThumbler/admin/produk.php" class="admin-nav-item <?= isDocAdminActive('admin/produk') ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                Produk
            </a>
            
            <a href="/ShodweThumbler/admin/pesanan.php" class="admin-nav-item <?= isDocAdminActive('admin/pesanan') ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                Pesanan
            </a>
            
            <a href="/ShodweThumbler/admin/pelanggan.php" class="admin-nav-item <?= isDocAdminActive('admin/pelanggan') ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                Pelanggan
            </a>
            
            <a href="/ShodweThumbler/admin/pembayaran.php" class="admin-nav-item <?= isDocAdminActive('admin/pembayaran') ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2" ry="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                Pembayaran
            </a>
            
            <a href="/ShodweThumbler/admin/laporan.php" class="admin-nav-item <?= isDocAdminActive('admin/laporan') ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                Laporan
            </a>
            
            <a href="/ShodweThumbler/admin/gambar-dashboard.php" class="admin-nav-item <?= isDocAdminActive('admin/gambar-dashboard') ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                Gambar Dashboard
            </a>
            
            <p style="padding: 0 1rem; font-size: 0.75rem; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem; margin-top: 1rem;">System</p>
            
            <a href="/ShodweThumbler/admin/settings.php" class="admin-nav-item <?= isDocAdminActive('admin/settings') ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                Settings
            </a>
            
            <a href="/ShodweThumbler/logout.php" class="admin-nav-item mt-4">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Logout
            </a>
        </nav>
        
        <div style="margin-top: auto; padding: 2rem;">
            <div style="background: var(--bg-white); border: 1px solid var(--border-color); border-radius: var(--border-radius-md); padding: 1rem;">
                <h5 style="font-size: 0.875rem; margin-bottom: 0.5rem;">Need quick support?</h5>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 1rem;">Pantau order custom tumbler, chat pelanggan, dan performa toko dari satu dashboard.</p>
                <button class="btn btn-outline" style="width: 100%; padding: 0.5rem; font-size: 0.875rem;">Open help center</button>
            </div>
        </div>
    </aside>
    
    <main class="admin-content">
        <header class="admin-header">
            <div>
                <!-- Title injected by page -->
            </div>
            
            <div class="flex items-center gap-2">
                <div style="position: relative;">
                    <input type="text" placeholder="Cari..." class="form-control" style="width: 300px; padding-left: 2.5rem; border-radius: var(--border-radius-full);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                
                <a href="#" class="btn-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                </a>
                
                <div class="flex items-center gap-1" style="background: var(--bg-white); padding: 0.25rem; padding-right: 1rem; border-radius: var(--border-radius-full); border: 1px solid var(--border-color);">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary-light); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-weight: bold;">
                        A
                    </div>
                    <span style="font-weight: 500; font-size: 0.875rem;">Alya</span>
                </div>
            </div>
        </header>
