<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/xml-functions.php';
require_once __DIR__ . '/../includes/helpers.php';
requireAdmin();

$orders = getAllOrders();
$flash = getFlashMessage();

$filter = $_GET['filter'] ?? 'semua';
$countSemua = count($orders);
$countMenunggu = count(array_filter($orders, function($o) { return $o['payment_status'] == 'Belum Bayar'; }));
$countDiproses = count(array_filter($orders, function($o) { return $o['status'] == 'Pending' && $o['payment_status'] == 'Lunas'; }));

if ($filter === 'menunggu') {
    $orders = array_values(array_filter($orders, function($o) { return $o['payment_status'] == 'Belum Bayar'; }));
} elseif ($filter === 'diproses') {
    $orders = array_values(array_filter($orders, function($o) { return $o['status'] == 'Pending' && $o['payment_status'] == 'Lunas'; }));
}
?>

<?php include '../components/admin-header.php'; ?>

<script>
    document.querySelector('.admin-header > div').innerHTML = '<h1 style="font-size: 1.8rem; margin-bottom: 0;">Manajemen Pesanan</h1><p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Pantau dan kelola semua pesanan pelanggan.</p>';
</script>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>" style="margin-bottom:1.5rem;"><?= htmlspecialchars($flash['message']) ?></div>
<?php endif; ?>

<div class="flex justify-between items-center mb-4">
    <div class="flex gap-2">
        <a href="?filter=semua" class="btn <?= $filter === 'semua' ? 'btn-outline' : '' ?>" style="<?= $filter === 'semua' ? 'background: white;' : 'background: transparent; border: none; color: var(--text-muted);' ?>">Semua Pesanan (<?= $countSemua ?>)</a>
        <a href="?filter=menunggu" class="btn <?= $filter === 'menunggu' ? 'btn-outline' : '' ?>" style="<?= $filter === 'menunggu' ? 'background: white;' : 'background: transparent; border: none; color: var(--text-muted);' ?>">Menunggu Pembayaran (<?= $countMenunggu ?>)</a>
        <a href="?filter=diproses" class="btn <?= $filter === 'diproses' ? 'btn-outline' : '' ?>" style="<?= $filter === 'diproses' ? 'background: white;' : 'background: transparent; border: none; color: var(--text-muted);' ?>">Perlu Diproses (<?= $countDiproses ?>)</a>
    </div>
    
    <div class="flex gap-2">
        <button class="btn btn-outline" style="background: white; padding: 0.5rem 1rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.5rem;"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Print Label
        </button>
    </div>
</div>

<div class="data-table-container" style="margin-top: 0;">
    <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: var(--bg-main);">
        <div class="flex gap-2 items-center">
            <input type="checkbox" style="accent-color: var(--primary-color);">
        </div>
        <span style="font-size: 0.85rem; color: var(--text-muted);">Menampilkan 1-<?= count($orders) ?> dari <?= count($orders) ?></span>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40px;"></th>
                <th>Order ID</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Total</th>
                <th>Pembayaran</th>
                <th>Status</th>
                <th style="width: 120px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($orders as $order): ?>
            <tr>
                <td><input type="checkbox" style="accent-color: var(--primary-color);"></td>
                <td style="font-weight: 500;"><?= htmlspecialchars($order['id']) ?></td>
                <td style="font-size: 0.9rem;"><?= htmlspecialchars($order['date']) ?></td>
                <td>
                    <div style="font-weight: 500; font-size: 0.95rem;"><?= htmlspecialchars($order['customer_name']) ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?= htmlspecialchars($order['product']) ?></div>
                </td>
                <td style="font-weight: 500;"><?= formatRupiah($order['total']) ?></td>
                <td>
                    <span class="badge <?= getStatusClass($order['payment_status']) ?>" style="background-color: transparent; border: 1px solid currentColor;"><?= htmlspecialchars($order['payment_status']) ?></span>
                </td>
                <td>
                    <span class="badge <?= getStatusClass($order['status']) ?>"><?= htmlspecialchars($order['status']) ?></span>
                </td>
                <td>
                    <div class="flex gap-1">
                        <button class="btn-icon" style="width: 32px; height: 32px; background: white; border: 1px solid var(--border-color);" title="Lihat Detail" onclick="viewOrder(<?= htmlspecialchars(json_encode($order)) ?>)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                        <?php if ($order['status'] !== 'Selesai' && $order['status'] !== 'Dibatalkan'): ?>
                        <button class="btn-icon" style="width: 32px; height: 32px; background: var(--primary-light); border: 1px solid var(--primary-color); color: var(--primary-color);" title="Update Status" onclick="updateOrderStatus(<?= htmlspecialchars(json_encode($order)) ?>)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                        </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Order Detail Modal -->
<div class="modal-overlay" id="orderDetailModal">
    <div class="modal-content" style="max-width:650px;">
        <button class="modal-close"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        <div style="padding:2rem;">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 style="font-size:1.4rem;margin-bottom:0.25rem;">Detail Pesanan</h2>
                    <span id="od-id" style="font-family:monospace;color:var(--primary-color);font-weight:500;"></span>
                </div>
                <span class="badge" id="od-status-badge"></span>
            </div>
            
            <div style="background:var(--bg-main);border-radius:var(--border-radius-md);padding:1.5rem;margin-bottom:1.5rem;">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">Pelanggan</p>
                        <p id="od-customer" style="font-weight:500;margin:0;"></p>
                    </div>
                    <div>
                        <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">Email</p>
                        <p id="od-email" style="margin:0;font-size:0.9rem;"></p>
                    </div>
                    <div>
                        <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">Tanggal</p>
                        <p id="od-date" style="margin:0;font-size:0.9rem;"></p>
                    </div>
                    <div>
                        <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">Kurir</p>
                        <p id="od-courier" style="margin:0;font-size:0.9rem;"></p>
                    </div>
                    <div>
                        <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">Metode Bayar</p>
                        <p id="od-payment-method" style="margin:0;font-size:0.9rem;font-weight:600;"></p>
                    </div>
                    <div style="grid-column:span 2;">
                        <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">Alamat Pengiriman</p>
                        <p id="od-address" style="margin:0;font-size:0.9rem;"></p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-between items-center" style="background:var(--bg-main);border-radius:var(--border-radius-md);padding:1.25rem 1.5rem;margin-bottom:1.5rem;">
                <div>
                    <p style="font-size:0.8rem;color:var(--text-muted);margin:0;">Produk</p>
                    <p id="od-product" style="font-weight:500;margin:0.25rem 0 0;"></p>
                </div>
                <div style="text-align:right;">
                    <p style="font-size:0.8rem;color:var(--text-muted);margin:0;">Total</p>
                    <p id="od-total" style="font-weight:700;color:var(--primary-color);font-size:1.2rem;margin:0.25rem 0 0;"></p>
                </div>
            </div>
            
            <div id="od-tracking" style="background:var(--bg-main);border-radius:var(--border-radius-md);padding:1.25rem 1.5rem;margin-bottom:1rem;display:none;">
                <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.25rem;">Nomor Resi</p>
                <p id="od-resi" style="font-family:monospace;font-weight:500;margin:0;font-size:1rem;"></p>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal-overlay modal-sm" id="updateStatusModal">
    <div class="modal-content" style="max-width:450px;">
        <button class="modal-close"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        <div style="padding:2rem;">
            <h2 style="font-size:1.3rem;margin-bottom:0.25rem;">Update Status Pesanan</h2>
            <p id="us-order-info" style="color:var(--text-muted);font-size:0.9rem;margin-bottom:1.5rem;"></p>
            
            <form action="update-order.php" method="POST">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="order_id" id="us-order-id">
                
                <div class="form-group">
                    <label class="form-label">Status Baru</label>
                    <select name="status" id="us-status" class="form-control" onchange="toggleTrackingField(this.value)">
                        <option value="Pending">Pending</option>
                        <option value="Diproses">Diproses</option>
                        <option value="Dikirim">Dikirim</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Dibatalkan">Dibatalkan</option>
                    </select>
                </div>
                
                <div class="form-group" id="trackingField" style="display:none;">
                    <label class="form-label">Nomor Resi Pengiriman</label>
                    <input type="text" name="tracking_number" id="us-tracking" class="form-control" placeholder="SH-XXXXXXXXXX">
                </div>
                
                <!-- Status Flow Visual -->
                <div style="background:var(--bg-main);border-radius:var(--border-radius-md);padding:1rem;margin-bottom:1.5rem;">
                    <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.75rem;">Alur Status:</p>
                    <div class="flex items-center gap-1" style="font-size:0.8rem;flex-wrap:wrap;">
                        <span style="padding:0.25rem 0.5rem;border-radius:var(--border-radius-full);background:var(--status-warning-bg);color:var(--status-warning);">Pending</span>
                        <span>→</span>
                        <span style="padding:0.25rem 0.5rem;border-radius:var(--border-radius-full);background:var(--status-warning-bg);color:var(--status-warning);">Diproses</span>
                        <span>→</span>
                        <span style="padding:0.25rem 0.5rem;border-radius:var(--border-radius-full);background:var(--status-info-bg);color:var(--status-info);">Dikirim</span>
                        <span>→</span>
                        <span style="padding:0.25rem 0.5rem;border-radius:var(--border-radius-full);background:var(--status-success-bg);color:var(--status-success);">Selesai</span>
                    </div>
                </div>
                
                <div class="flex gap-2 justify-between">
                    <button type="button" class="btn btn-outline" onclick="closeModal('updateStatusModal')" style="padding:0.75rem 1.5rem;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="padding:0.75rem 2rem;">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/ShodweThumbler/assets/js/main.js"></script>
<script>
function formatRupiah(num) {
    return 'Rp ' + parseInt(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function viewOrder(order) {
    document.getElementById('od-id').textContent = order.id;
    document.getElementById('od-customer').textContent = order.customer_name;
    document.getElementById('od-email').textContent = order.customer_email;
    document.getElementById('od-date').textContent = order.date;
    document.getElementById('od-product').textContent = order.product + ' (x' + order.quantity + ')';
    document.getElementById('od-total').textContent = formatRupiah(order.total);
    document.getElementById('od-courier').textContent = order.courier || '-';
    document.getElementById('od-payment-method').textContent = order.payment_method || 'Bank Transfer';
    document.getElementById('od-address').textContent = order.shipping_address || '-';
    
    const statusBadge = document.getElementById('od-status-badge');
    statusBadge.textContent = order.status;
    statusBadge.className = 'badge';
    const statusMap = {'Pending':'status-pending','Diproses':'status-processing','Dikirim':'status-shipped','Selesai':'status-completed','Dibatalkan':'status-cancelled'};
    statusBadge.classList.add(statusMap[order.status] || 'status-default');
    
    const trackingEl = document.getElementById('od-tracking');
    if (order.tracking_number) {
        trackingEl.style.display = 'block';
        document.getElementById('od-resi').textContent = order.tracking_number;
    } else {
        trackingEl.style.display = 'none';
    }
    
    openModal('orderDetailModal');
}

function updateOrderStatus(order) {
    document.getElementById('us-order-id').value = order.id;
    document.getElementById('us-order-info').textContent = order.id + ' — ' + order.customer_name;
    document.getElementById('us-status').value = order.status;
    toggleTrackingField(order.status);
    if (order.tracking_number) {
        document.getElementById('us-tracking').value = order.tracking_number;
    }
    openModal('updateStatusModal');
}

function toggleTrackingField(status) {
    document.getElementById('trackingField').style.display = status === 'Dikirim' ? 'block' : 'none';
}
</script>

</main>
</div>
</body>
</html>
