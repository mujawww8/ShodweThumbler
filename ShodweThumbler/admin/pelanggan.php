<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/xml-functions.php';
requireAdmin();

$users = getAllUsers('customer');

$filter = $_GET['filter'] ?? 'semua';
$countSemua = count($users);
$countVip = count(array_filter($users, function($u) { return $u['total_spent'] >= 1000000 || $u['total_orders'] >= 5; }));

if ($filter === 'vip') {
    $users = array_values(array_filter($users, function($u) { return $u['total_spent'] >= 1000000 || $u['total_orders'] >= 5; }));
}
?>

<?php include '../components/admin-header.php'; ?>

<script>
    // Update admin header title
    document.querySelector('.admin-header > div').innerHTML = '<h1 style="font-size: 1.8rem; margin-bottom: 0;">Data Pelanggan</h1><p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Kelola data pelanggan dan lihat riwayat transaksi mereka.</p>';
</script>

<div class="flex justify-between items-center mb-4">
    <div class="flex gap-2">
        <a href="?filter=semua" class="btn <?= $filter === 'semua' ? 'btn-outline' : '' ?>" style="<?= $filter === 'semua' ? 'background: white;' : 'background: transparent; border: none; color: var(--text-muted);' ?>">Semua Pelanggan (<?= $countSemua ?>)</a>
        <a href="?filter=vip" class="btn <?= $filter === 'vip' ? 'btn-outline' : '' ?>" style="<?= $filter === 'vip' ? 'background: white;' : 'background: transparent; border: none; color: var(--text-muted);' ?>">VIP Member (<?= $countVip ?>)</a>
    </div>
    
    <div class="flex gap-2">
        <button class="btn btn-outline" style="background: white; padding: 0.5rem 1rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.5rem;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Export CSV
        </button>
    </div>
</div>

<div class="data-table-container" style="margin-top: 0;">
    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: var(--bg-main);">
        <div class="flex gap-2 items-center">
            <input type="checkbox" style="accent-color: var(--primary-color);">
            <button class="btn-icon" style="width: 32px; height: 32px; background: white; border: 1px solid var(--border-color);"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></button>
        </div>
        <div class="flex gap-2 items-center">
            <span style="font-size: 0.85rem; color: var(--text-muted);">Menampilkan 1-<?= count($users) ?> dari <?= count($users) ?></span>
            <div class="flex gap-1">
                <button class="btn-icon" style="width: 32px; height: 32px; background: white; border: 1px solid var(--border-color);" disabled><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg></button>
                <button class="btn-icon" style="width: 32px; height: 32px; background: white; border: 1px solid var(--border-color);" disabled><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg></button>
            </div>
        </div>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40px;"></th>
                <th>Pelanggan</th>
                <th>Kontak</th>
                <th>Total Belanja</th>
                <th>Pesanan</th>
                <th>Status</th>
                <th style="width: 80px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $user): ?>
            <tr>
                <td><input type="checkbox" style="accent-color: var(--primary-color);"></td>
                <td>
                    <div class="flex items-center gap-2">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-light); color: var(--primary-color); display: flex; items-center; justify-content: center; font-weight: bold; flex-shrink: 0;">
                            <?= substr(htmlspecialchars($user['name']), 0, 1) ?>
                        </div>
                        <div>
                            <div style="font-weight: 500; font-size: 0.95rem;"><?= htmlspecialchars($user['name']) ?></div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">ID: <?= htmlspecialchars($user['id']) ?></div>
                        </div>
                    </div>
                </td>
                <td>
                    <div style="font-size: 0.9rem;"><?= htmlspecialchars($user['email']) ?></div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);"><?= htmlspecialchars($user['phone']) ?></div>
                </td>
                <td style="font-weight: 500;"><?= formatRupiah($user['total_spent']) ?></td>
                <td><?= $user['total_orders'] ?> pesanan</td>
                <td>
                    <span class="badge <?= getStatusClass($user['status']) ?>"><?= htmlspecialchars($user['status']) ?></span>
                </td>
                <td>
                    <button class="btn-icon" style="width: 32px; height: 32px; background: white; border: 1px solid var(--border-color);" title="Lihat Profil">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</main>
</div>
</body>
</html>
