<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/xml-functions.php';
requireLogin();

$order_id = $_GET['id'] ?? 'ORD-00000';
$order = getOrderById($order_id);
if (!$order) {
    header('Location: /ShodweThumbler/index.php');
    exit;
}
$isCod = isset($order['payment_method']) && $order['payment_method'] === 'COD';
?>

<?php include 'components/header.php'; ?>

<section class="section-padding bg-main" style="min-height: 80vh; display: flex; align-items: center; justify-content: center;">
    <div class="container">
        <div style="max-width: 600px; margin: 0 auto; background: white; padding: 4rem 3rem; border-radius: var(--border-radius-lg); text-align: center; border: 1px solid var(--border-color); box-shadow: var(--shadow-md);">
            
            <div style="width: 80px; height: 80px; background: var(--status-success-bg); color: var(--status-success); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            
            <h1 style="font-size: 2.25rem; margin-bottom: 0.5rem;">Terima Kasih!</h1>
            <?php if($isCod): ?>
                <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 2rem;">Pesanan Anda berhasil dibuat dengan metode Cash on Delivery (COD).</p>
            <?php else: ?>
                <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 2rem;">Pesanan Anda telah berhasil dibuat dan sedang menunggu pembayaran.</p>
            <?php endif; ?>
            
            <div style="background: var(--bg-secondary); padding: 1.5rem; border-radius: var(--border-radius-md); margin-bottom: 2rem; text-align: left;">
                <div class="flex justify-between items-center mb-3 pb-3" style="border-bottom: 1px solid var(--border-color);">
                    <span style="color: var(--text-muted); font-size: 0.9rem;">Nomor Pesanan</span>
                    <span style="font-weight: 600; font-size: 1.1rem;"><?= htmlspecialchars($order_id) ?></span>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0.5rem;"><?= $isCod ? 'Total yang harus dibayar saat pesanan tiba:' : 'Silakan lakukan transfer sebesar:' ?></p>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--primary-color);"><?= formatRupiah($order['total']) ?></div>
                </div>
                
                <?php if(!$isCod): ?>
                <div>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0.5rem;">Ke Rekening Bank BCA:</p>
                    <div class="flex justify-between items-center bg-white p-2" style="padding: 1rem; border-radius: var(--border-radius-sm); border: 1px solid var(--border-color);">
                        <span style="font-weight: 600; font-size: 1.25rem; letter-spacing: 2px;">882 901 8821</span>
                        <button class="btn-icon" style="width: 32px; height: 32px;" title="Copy Account Number" data-copy="882 901 8821">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                        </button>
                    </div>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem;">a.n PT Shodwe Tumbler Indonesia</p>
                </div>
                <?php endif; ?>
            </div>
            
            <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 2rem;">
                <?php if($isCod): ?>
                    Siapkan uang tunai yang pas saat kurir mengantarkan pesanan Anda ke lokasi tujuan.<br>
                    Anda dapat melacak status pengiriman melalui tautan di bawah ini.
                <?php else: ?>
                    Instruksi pembayaran lengkap telah dikirimkan ke email Anda.<br>
                    Pesanan akan otomatis dibatalkan jika pembayaran tidak dilakukan dalam waktu 24 jam.
                <?php endif; ?>
            </p>
            
            <div class="flex gap-2 justify-center">
                <a href="track-order.php" class="btn btn-primary" style="padding: 1rem 2rem;">Lacak Pesanan</a>
                <a href="index.php" class="btn btn-outline" style="padding: 1rem 2rem;">Kembali ke Beranda</a>
            </div>
            
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>
