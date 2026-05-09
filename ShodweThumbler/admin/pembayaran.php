<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/xml-functions.php';
requireAdmin();

$flash = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $paymentId = $_POST['payment_id'] ?? '';
    $orderId = $_POST['order_id'] ?? '';
    
    if ($action === 'terima' && $paymentId && $orderId) {
        if (updatePaymentStatus($paymentId, 'Berhasil')) {
            updateOrder($orderId, ['payment_status' => 'Lunas']);
            $flash = ['type' => 'success', 'message' => 'Pembayaran berhasil diverifikasi (Diterima).'];
        } else {
            $flash = ['type' => 'danger', 'message' => 'Terjadi kesalahan saat menerima pembayaran.'];
        }
    } elseif ($action === 'tolak' && $paymentId) {
        if (updatePaymentStatus($paymentId, 'Gagal / Expired')) {
            // Keep order payment status as Belum Bayar so they can retry, or update to Gagal.
            updateOrder($orderId, ['payment_status' => 'Batal']);
            $flash = ['type' => 'success', 'message' => 'Pembayaran telah ditolak.'];
        } else {
            $flash = ['type' => 'danger', 'message' => 'Terjadi kesalahan saat menolak pembayaran.'];
        }
    }
}

$payments = getAllPayments();

$filter = $_GET['filter'] ?? 'semua';
$countSemua = count($payments);
$countMenunggu = count(array_filter($payments, function($p) { return $p['status'] == 'Menunggu Verifikasi'; }));

if ($filter === 'menunggu') {
    $payments = array_values(array_filter($payments, function($p) { return $p['status'] == 'Menunggu Verifikasi'; }));
}
?>

<?php include '../components/admin-header.php'; ?>

<script>
    // Update admin header title
    document.querySelector('.admin-header > div').innerHTML = '<h1 style="font-size: 1.8rem; margin-bottom: 0;">Data Pembayaran</h1><p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Verifikasi dan kelola transaksi pembayaran pelanggan.</p>';
</script>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>" style="margin-bottom:1.5rem;"><?= htmlspecialchars($flash['message']) ?></div>
<?php endif; ?>

<div class="flex justify-between items-center mb-4">
    <div class="flex gap-2">
        <a href="?filter=semua" class="btn <?= $filter === 'semua' ? 'btn-outline' : '' ?>" style="<?= $filter === 'semua' ? 'background: white;' : 'background: transparent; border: none; color: var(--text-muted);' ?>">Semua Pembayaran (<?= $countSemua ?>)</a>
        <a href="?filter=menunggu" class="btn <?= $filter === 'menunggu' ? 'btn-outline' : '' ?>" style="<?= $filter === 'menunggu' ? 'background: white;' : 'background: transparent; border: none; color: var(--text-muted);' ?>">Menunggu Verifikasi (<?= $countMenunggu ?>)</a>
    </div>
    
    <div class="flex gap-2">
        <div class="form-group" style="margin-bottom: 0;">
            <select class="form-control" style="padding: 0.5rem 1rem;">
                <option value="">Semua Metode</option>
                <option value="BCA Transfer">BCA Transfer</option>
                <option value="GoPay">GoPay</option>
                <option value="OVO">OVO</option>
                <option value="Mandiri Virtual">Mandiri Virtual</option>
                <option value="COD">COD</option>
            </select>
        </div>
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
            <span style="font-size: 0.85rem; color: var(--text-muted);">Menampilkan 1-<?= count($payments) ?> dari <?= count($payments) ?></span>
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
                <th>ID Pembayaran</th>
                <th>Waktu</th>
                <th>Order ID</th>
                <th>Pelanggan</th>
                <th>Metode</th>
                <th>Total</th>
                <th>Status</th>
                <th style="width: 120px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($payments as $payment): ?>
            <tr>
                <td><input type="checkbox" style="accent-color: var(--primary-color);"></td>
                <td style="font-weight: 500; font-size: 0.9rem;"><?= htmlspecialchars($payment['id']) ?></td>
                <td>
                    <div style="font-size: 0.9rem;"><?= htmlspecialchars($payment['date']) ?></div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);"><?= htmlspecialchars($payment['time']) ?></div>
                </td>
                <td style="font-size: 0.9rem; color: var(--primary-color); font-weight: 500;"><?= htmlspecialchars($payment['order_id']) ?></td>
                <td>
                    <div style="font-weight: 500; font-size: 0.9rem;"><?= htmlspecialchars($payment['customer_name']) ?></div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);"><?= htmlspecialchars($payment['customer_email']) ?></div>
                </td>
                <td style="font-size: 0.9rem;"><?= htmlspecialchars($payment['method']) ?></td>
                <td style="font-weight: 500;"><?= formatRupiah($payment['total']) ?></td>
                <td>
                    <span class="badge <?= getStatusClass($payment['status']) ?>"><?= htmlspecialchars($payment['status']) ?></span>
                </td>
                <td>
                    <?php if($payment['status'] == 'Menunggu Verifikasi'): ?>
                        <div class="flex gap-1">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="terima">
                                <input type="hidden" name="payment_id" value="<?= htmlspecialchars($payment['id']) ?>">
                                <input type="hidden" name="order_id" value="<?= htmlspecialchars($payment['order_id']) ?>">
                                <button type="submit" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" onclick="return confirm('Terima pembayaran ini?')">Terima</button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="tolak">
                                <input type="hidden" name="payment_id" value="<?= htmlspecialchars($payment['id']) ?>">
                                <input type="hidden" name="order_id" value="<?= htmlspecialchars($payment['order_id']) ?>">
                                <button type="submit" class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: var(--status-danger); border-color: var(--status-danger);" onclick="return confirm('Tolak pembayaran ini?')">Tolak</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <button class="btn-icon" style="width: 32px; height: 32px; background: white; border: 1px solid var(--border-color);" title="Lihat Bukti">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        </button>
                    <?php endif; ?>
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
