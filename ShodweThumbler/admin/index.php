<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/xml-functions.php';
requireAdmin();

$orders = getAllOrders();
$products = getAllProducts();
$users = getAllUsers('customer');

// Calculate stats
$totalRevenue = 0;
$totalOrders = count($orders);
$newCustomers = count($users);
$recentOrders = array_slice($orders, 0, 5); // Just take first 5 for dashboard

foreach ($orders as $order) {
    if ($order['status'] !== 'Dibatalkan' && $order['payment_status'] === 'Lunas') {
        $totalRevenue += (int)$order['total'];
    }
}
?>

<?php include '../components/admin-header.php'; ?>

<script>
    // Update admin header title
    document.querySelector('.admin-header > div').innerHTML = '<h1 style="font-size: 1.8rem; margin-bottom: 0;">Dashboard Overview</h1><p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Welcome back, Admin. Here is your store summary.</p>';
</script>

<div class="grid grid-cols-4 gap-4 mb-4">
    <div class="stat-card">
        <div class="flex justify-between items-start mb-2">
            <div class="stat-card-title">Total Pendapatan</div>
            <div class="btn-icon" style="background: var(--status-success-bg); color: var(--status-success); width: 32px; height: 32px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            </div>
        </div>
        <div class="stat-card-value"><?= formatRupiah($totalRevenue) ?></div>
        <div style="font-size: 0.8rem; color: var(--status-success); display: flex; align-items: center; gap: 0.25rem;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline><polyline points="16 7 22 7 22 13"></polyline></svg>
            +12.5% dari bulan lalu
        </div>
    </div>
    
    <div class="stat-card">
        <div class="flex justify-between items-start mb-2">
            <div class="stat-card-title">Total Pesanan</div>
            <div class="btn-icon" style="background: var(--primary-light); color: var(--primary-color); width: 32px; height: 32px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
            </div>
        </div>
        <div class="stat-card-value"><?= number_format($totalOrders) ?></div>
        <div style="font-size: 0.8rem; color: var(--status-success); display: flex; align-items: center; gap: 0.25rem;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline><polyline points="16 7 22 7 22 13"></polyline></svg>
            +8.2% dari bulan lalu
        </div>
    </div>
    
    <div class="stat-card">
        <div class="flex justify-between items-start mb-2">
            <div class="stat-card-title">Pelanggan Baru</div>
            <div class="btn-icon" style="background: var(--status-info-bg); color: var(--status-info); width: 32px; height: 32px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
        </div>
        <div class="stat-card-value"><?= number_format($newCustomers) ?></div>
        <div style="font-size: 0.8rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.25rem;">
            Sepanjang waktu
        </div>
    </div>
    
    <div class="stat-card">
        <div class="flex justify-between items-start mb-2">
            <div class="stat-card-title">Produk Aktif</div>
            <div class="btn-icon" style="background: var(--status-warning-bg); color: var(--status-warning); width: 32px; height: 32px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
            </div>
        </div>
        <div class="stat-card-value"><?= number_format(count($products)) ?></div>
        <div style="font-size: 0.8rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.25rem;">
            Total produk di toko
        </div>
    </div>
</div>

<div class="grid grid-cols-3 gap-4">
    <div class="data-table-container" style="grid-column: span 2; margin-top: 0;">
        <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.1rem; margin: 0;">Pesanan Terbaru</h3>
            <a href="pesanan.php" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.8rem;">Lihat Semua</a>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Pelanggan</th>
                    <th>Produk</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recentOrders as $order): ?>
                <tr>
                    <td style="font-weight: 500;"><?= htmlspecialchars($order['id']) ?></td>
                    <td>
                        <div style="font-weight: 500;"><?= htmlspecialchars($order['customer_name']) ?></div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);"><?= htmlspecialchars($order['date']) ?></div>
                    </td>
                    <td>
                        <div style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 0.9rem;">
                            <?= htmlspecialchars($order['product']) ?>
                        </div>
                    </td>
                    <td style="font-weight: 500;"><?= formatRupiah($order['total']) ?></td>
                    <td>
                        <span class="badge <?= getStatusClass($order['status']) ?>"><?= htmlspecialchars($order['status']) ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="data-table-container" style="margin-top: 0;">
        <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
            <h3 style="font-size: 1.1rem; margin: 0;">Produk Terlaris</h3>
        </div>
        <div style="padding: 1rem;">
            <?php 
            $bestSellers = array_slice($products, 0, 4);
            foreach($bestSellers as $product): 
            ?>
            <div class="flex gap-2 items-center mb-3 pb-3" style="border-bottom: 1px solid var(--border-color);">
                <div style="width: 50px; height: 50px; border-radius: var(--border-radius-sm); background: var(--bg-secondary); overflow: hidden; flex-shrink: 0;">
                    <img src="../assets/images/<?= $product['image'] ?>" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="flex: 1; min-width: 0;">
                    <h4 style="font-size: 0.9rem; margin-bottom: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($product['name']) ?></h4>
                    <div class="flex justify-between items-center">
                        <span style="font-size: 0.8rem; color: var(--text-muted);"><?= $product['stock'] ?> terjual</span>
                        <span style="font-size: 0.85rem; font-weight: 500; color: var(--primary-color);"><?= formatRupiah($product['price']) ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <a href="produk.php" class="btn btn-outline w-full" style="padding: 0.5rem; font-size: 0.875rem;">Kelola Produk</a>
        </div>
    </div>
</div>

</main>
</div>
</body>
</html>
