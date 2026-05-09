<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/xml-functions.php';
requireLogin();

$user = getCurrentUser();

// Get order ID from URL if provided, otherwise get the latest order for this user
$order_id = $_GET['id'] ?? '';
$order = null;

if ($order_id) {
    $order = getOrderById($order_id);
    // Ensure the order belongs to the current user (security check)
    if ($order && $order['customer_email'] !== $user['email']) {
        $order = null;
    }
} else {
    // Get all orders for this user and pick the latest one
    $allOrders = getAllOrders();
    $userOrders = array_filter($allOrders, function($o) use ($user) {
        return $o['customer_email'] === $user['email'];
    });
    if (!empty($userOrders)) {
        $order = end($userOrders); // Get the last element
    }
}

// Redirect to shop if no orders found
if (!$order) {
    header('Location: /ShodweThumbler/toko.php');
    exit;
}

// Determine timeline states
$isCod = isset($order['payment_method']) && $order['payment_method'] === 'COD';
$isPaid = $order['payment_status'] === 'Lunas' || $isCod;
$isProcessing = in_array($order['status'], ['Diproses', 'Dikirim', 'Selesai']);
$isShipped = in_array($order['status'], ['Dikirim', 'Selesai']);
$isCompleted = $order['status'] === 'Selesai';

function getStepClass($isActive, $isCurrent) {
    if ($isCurrent) {
        return 'background: white; color: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; border: 2px solid var(--primary-color); box-shadow: 0 0 0 4px white, 0 0 0 5px var(--border-color);';
    } elseif ($isActive) {
        return 'background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; border: 4px solid white; box-shadow: 0 0 0 1px var(--border-color);';
    } else {
        return 'background: white; color: var(--text-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; border: 4px solid white; box-shadow: 0 0 0 1px var(--border-color);';
    }
}

// Calculate progress bar width
$progressWidth = '12%'; // default (step 1)
if ($isCompleted) $progressWidth = '100%';
elseif ($isShipped) $progressWidth = '75%';
elseif ($isProcessing) $progressWidth = '50%';
elseif ($isPaid) $progressWidth = '25%';

?>

<?php include 'components/header.php'; ?>

<section style="background-color: var(--bg-main); padding: 3rem 0; min-height: calc(100vh - 400px);">
    <div class="container">
        
        <div class="flex justify-between items-center mb-4">
            <h1 style="font-size: 2rem; margin-bottom: 0;">Lacak Pesanan</h1>
            <a href="index.php" class="btn btn-outline">Belanja Lagi</a>
        </div>
        
        <div class="grid grid-cols-3 gap-4">
            <!-- Order Info & Tracker -->
            <div style="grid-column: span 2;">
                <div style="background: white; border-radius: var(--border-radius-lg); border: 1px solid var(--border-color); overflow: hidden; margin-bottom: 2rem;">
                    
                    <!-- Header -->
                    <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: var(--bg-secondary);">
                        <div>
                            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.25rem;">Order ID</p>
                            <h3 style="font-size: 1.1rem; margin: 0;"><?= htmlspecialchars($order['id']) ?></h3>
                        </div>
                        <div>
                            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.25rem;">Tanggal Pesanan</p>
                            <p style="font-weight: 500; font-size: 0.95rem; margin: 0;"><?= htmlspecialchars($order['date']) ?></p>
                        </div>
                        <div class="text-right">
                            <span class="badge <?= getStatusClass($order['status']) ?>" style="font-size: 0.85rem; padding: 0.35rem 1rem;"><?= htmlspecialchars($order['status']) ?></span>
                        </div>
                    </div>
                    
                    <!-- Tracker -->
                    <div style="padding: 3rem 2rem;">
                        <div class="tracker" style="position: relative; display: flex; justify-content: space-between;">
                            <!-- Progress Bar Background -->
                            <div style="position: absolute; top: 15px; left: 10%; right: 10%; height: 2px; background: var(--border-color); z-index: 1;"></div>
                            <!-- Progress Bar Fill -->
                            <div style="position: absolute; top: 15px; left: 10%; width: <?= $progressWidth ?>; height: 2px; background: var(--primary-color); z-index: 2; transition: width 0.5s ease;"></div>
                            
                            <!-- Step 1 -->
                            <div style="position: relative; z-index: 3; display: flex; flex-direction: column; align-items: center; width: 20%;">
                                <div style="width: 32px; height: 32px; <?= getStepClass(true, !$isPaid) ?>">
                                    <?php if(!$isPaid): ?>
                                        <div style="width: 10px; height: 10px; background: var(--primary-color); border-radius: 50%;"></div>
                                    <?php else: ?>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <?php endif; ?>
                                </div>
                                <div style="text-align: center;">
                                    <h5 style="font-size: 0.9rem; margin-bottom: 0.25rem; color: <?= !$isPaid ? 'var(--text-main)' : 'var(--text-muted)' ?>;">Pesanan Dibuat</h5>
                                </div>
                            </div>
                            
                            <!-- Step 2 -->
                            <div style="position: relative; z-index: 3; display: flex; flex-direction: column; align-items: center; width: 20%;">
                                <div style="width: 32px; height: 32px; <?= getStepClass($isPaid, $isPaid && !$isProcessing) ?>">
                                    <?php if($isPaid && !$isProcessing): ?>
                                        <div style="width: 10px; height: 10px; background: var(--primary-color); border-radius: 50%;"></div>
                                    <?php elseif($isProcessing): ?>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <?php else: ?>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                                    <?php endif; ?>
                                </div>
                                <div style="text-align: center;">
                                    <h5 style="font-size: 0.9rem; margin-bottom: 0.25rem; color: <?= ($isPaid && !$isProcessing) ? 'var(--text-main)' : 'var(--text-muted)' ?>;"><?= $isCod ? 'Menunggu Pembayaran (COD)' : 'Pembayaran Berhasil' ?></h5>
                                </div>
                            </div>
                            
                            <!-- Step 3 -->
                            <div style="position: relative; z-index: 3; display: flex; flex-direction: column; align-items: center; width: 20%;">
                                <div style="width: 32px; height: 32px; <?= getStepClass($isProcessing, $isProcessing && !$isShipped) ?>">
                                    <?php if($isProcessing && !$isShipped): ?>
                                        <div style="width: 10px; height: 10px; background: var(--primary-color); border-radius: 50%;"></div>
                                    <?php elseif($isShipped): ?>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <?php else: ?>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    <?php endif; ?>
                                </div>
                                <div style="text-align: center;">
                                    <h5 style="font-size: 0.9rem; margin-bottom: 0.25rem; color: <?= ($isProcessing && !$isShipped) ? 'var(--text-main)' : 'var(--text-muted)' ?>;">Sedang Diproses</h5>
                                    <?php if($isProcessing && !$isShipped): ?>
                                        <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0;">Sedang disiapkan</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Step 4 -->
                            <div style="position: relative; z-index: 3; display: flex; flex-direction: column; align-items: center; width: 20%;">
                                <div style="width: 32px; height: 32px; <?= getStepClass($isShipped, $isShipped && !$isCompleted) ?>">
                                    <?php if($isShipped && !$isCompleted): ?>
                                        <div style="width: 10px; height: 10px; background: var(--primary-color); border-radius: 50%;"></div>
                                    <?php elseif($isCompleted): ?>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <?php else: ?>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                                    <?php endif; ?>
                                </div>
                                <div style="text-align: center;">
                                    <h5 style="font-size: 0.9rem; margin-bottom: 0.25rem; color: <?= ($isShipped && !$isCompleted) ? 'var(--text-main)' : 'var(--text-muted)' ?>;">Dikirim</h5>
                                </div>
                            </div>
                            
                            <!-- Step 5 -->
                            <div style="position: relative; z-index: 3; display: flex; flex-direction: column; align-items: center; width: 20%;">
                                <div style="width: 32px; height: 32px; <?= getStepClass($isCompleted, $isCompleted) ?>">
                                    <?php if($isCompleted): ?>
                                        <div style="width: 10px; height: 10px; background: var(--primary-color); border-radius: 50%;"></div>
                                    <?php else: ?>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    <?php endif; ?>
                                </div>
                                <div style="text-align: center;">
                                    <h5 style="font-size: 0.9rem; margin-bottom: 0.25rem; color: <?= $isCompleted ? 'var(--text-main)' : 'var(--text-muted)' ?>;">Selesai</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Timeline Details -->
                <div style="background: white; border-radius: var(--border-radius-lg); border: 1px solid var(--border-color); padding: 2rem;">
                    <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem;">Riwayat Status</h3>
                    
                    <div style="position: relative; padding-left: 2rem;">
                        <div style="position: absolute; left: 7px; top: 10px; bottom: 0; width: 2px; background: var(--border-color);"></div>
                        
                        <?php if($isCompleted): ?>
                        <div style="position: relative; margin-bottom: 1.5rem;">
                            <div style="position: absolute; left: -2rem; top: 4px; width: 16px; height: 16px; border-radius: 50%; background: var(--primary-color); border: 3px solid white; box-shadow: 0 0 0 1px var(--primary-color);"></div>
                            <div class="flex justify-between items-start">
                                <div>
                                    <h5 style="font-size: 1rem; margin-bottom: 0.25rem;">Pesanan Selesai</h5>
                                    <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;">Pesanan telah diterima dengan baik.</p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if($isShipped): ?>
                        <div style="position: relative; margin-bottom: 1.5rem;">
                            <div style="position: absolute; left: -2rem; top: 4px; width: 16px; height: 16px; border-radius: 50%; background: <?= $isCompleted ? 'var(--border-color)' : 'var(--primary-color)' ?>; border: 3px solid white; <?= !$isCompleted ? 'box-shadow: 0 0 0 1px var(--primary-color);' : '' ?>"></div>
                            <div class="flex justify-between items-start">
                                <div>
                                    <h5 style="font-size: 1rem; margin-bottom: 0.25rem;">Pesanan Sedang Dikirim</h5>
                                    <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;">Pesanan Anda sedang dalam perjalanan oleh kurir.</p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if($isProcessing): ?>
                        <div style="position: relative; margin-bottom: 1.5rem;">
                            <div style="position: absolute; left: -2rem; top: 4px; width: 16px; height: 16px; border-radius: 50%; background: <?= $isShipped ? 'var(--border-color)' : 'var(--primary-color)' ?>; border: 3px solid white; <?= !$isShipped ? 'box-shadow: 0 0 0 1px var(--primary-color);' : '' ?>"></div>
                            <div class="flex justify-between items-start">
                                <div>
                                    <h5 style="font-size: 1rem; margin-bottom: 0.25rem;">Pesanan Sedang Diproses</h5>
                                    <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;">Seller sedang menyiapkan pesanan Anda.</p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if($isPaid): ?>
                        <div style="position: relative; margin-bottom: 1.5rem;">
                            <div style="position: absolute; left: -2rem; top: 4px; width: 16px; height: 16px; border-radius: 50%; background: <?= $isProcessing ? 'var(--border-color)' : 'var(--primary-color)' ?>; border: 3px solid white; <?= !$isProcessing ? 'box-shadow: 0 0 0 1px var(--primary-color);' : '' ?>"></div>
                            <div class="flex justify-between items-start">
                                <div>
                                    <h5 style="font-size: 1rem; margin-bottom: 0.25rem;"><?= $isCod ? 'Menunggu Pembayaran' : 'Pembayaran Berhasil' ?></h5>
                                    <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;"><?= $isCod ? 'Pembayaran akan dilakukan secara tunai kepada kurir saat pesanan tiba.' : 'Pembayaran Anda telah dikonfirmasi.' ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div style="position: relative;">
                            <div style="position: absolute; left: -2rem; top: 4px; width: 16px; height: 16px; border-radius: 50%; background: <?= $isPaid ? 'var(--border-color)' : 'var(--primary-color)' ?>; border: 3px solid white; <?= !$isPaid ? 'box-shadow: 0 0 0 1px var(--primary-color);' : '' ?>"></div>
                            <div class="flex justify-between items-start">
                                <div>
                                    <h5 style="font-size: 1rem; margin-bottom: 0.25rem;">Pesanan Dibuat</h5>
                                    <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;">Pesanan berhasil dibuat.</p>
                                </div>
                                <span style="font-size: 0.85rem; color: var(--text-muted); white-space: nowrap;"><?= htmlspecialchars($order['date']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Delivery Info & Order Details -->
            <div>
                <div style="background: white; border-radius: var(--border-radius-lg); border: 1px solid var(--border-color); padding: 1.5rem; margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.1rem; margin-bottom: 1.25rem;">Informasi Pengiriman</h3>
                    
                    <div class="flex items-start gap-2 mb-3">
                        <div style="color: var(--text-muted); margin-top: 0.25rem;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        </div>
                        <div>
                            <p style="font-weight: 500; font-size: 0.95rem; margin-bottom: 0.25rem;">Alamat Tujuan</p>
                            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; margin: 0;">
                                <?= nl2br(htmlspecialchars($order['shipping_address'] ?? '')) ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-2">
                        <div style="color: var(--text-muted); margin-top: 0.25rem;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                        </div>
                        <div>
                            <p style="font-weight: 500; font-size: 0.95rem; margin-bottom: 0.25rem;">Kurir Pengiriman</p>
                            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.25rem;"><?= htmlspecialchars($order['courier'] ?? '-') ?></p>
                            <?php if(!empty($order['tracking_number'])): ?>
                            <div class="flex items-center gap-1">
                                <span style="font-size: 0.85rem; background: var(--bg-secondary); padding: 0.25rem 0.5rem; border-radius: var(--border-radius-sm); font-family: monospace;"><?= htmlspecialchars($order['tracking_number']) ?></span>
                                <button class="btn-icon" style="width: 24px; height: 24px; background: none;" data-copy="<?= htmlspecialchars($order['tracking_number']) ?>">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div style="background: white; border-radius: var(--border-radius-lg); border: 1px solid var(--border-color); padding: 1.5rem;">
                    <h3 style="font-size: 1.1rem; margin-bottom: 1.25rem;">Detail Produk</h3>
                    
                    <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem;">
                        <div class="flex gap-2">
                            <div style="flex: 1;">
                                <h4 style="font-size: 0.9rem; margin-bottom: 0.25rem;"><?= htmlspecialchars($order['product'] ?? '') ?></h4>
                                <div class="flex justify-between items-center mt-1">
                                    <span style="font-size: 0.8rem; color: var(--text-muted);">Total Quantity: <?= htmlspecialchars($order['quantity'] ?? '1') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; margin-top: auto;">
                        <div class="flex justify-between items-center mb-2">
                            <span style="font-weight: 500; font-size: 0.95rem;">Metode Pembayaran</span>
                            <span style="font-weight: 600; font-size: 0.95rem; color: <?= $isCod ? 'var(--primary-color)' : 'inherit' ?>;"><?= htmlspecialchars($order['payment_method'] ?? 'Bank Transfer') ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span style="font-weight: 500; font-size: 0.95rem;">Total Pembayaran</span>
                            <span style="font-weight: 700; color: var(--primary-color); font-size: 1.1rem;"><?= formatRupiah($order['total'] ?? 0) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</section>

<?php include 'components/footer.php'; ?>
