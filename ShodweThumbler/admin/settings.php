<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/xml-functions.php';
requireAdmin();

$settings = getSettings();
$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section = $_POST['section'] ?? '';
    
    if ($section === 'general') {
        $data = [
            'store_name' => $_POST['store_name'] ?? '',
            'tagline' => $_POST['tagline'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'address' => $_POST['address'] ?? '',
            'operating_hours' => $_POST['operating_hours'] ?? ''
        ];
        if (updateSettings('general', $data)) {
            $success = 'Pengaturan umum berhasil disimpan!';
            $settings = getSettings(); // Reload
        } else {
            $error = 'Gagal menyimpan pengaturan.';
        }
    }
}
?>

<?php include '../components/admin-header.php'; ?>

<script>
    document.querySelector('.admin-header > div').innerHTML = '<h1 style="font-size: 1.8rem; margin-bottom: 0;">System Settings</h1><p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Kelola pengaturan umum, pembayaran, dan pengiriman toko Anda.</p>';
</script>

<?php if ($success): ?>
    <div class="alert alert-success" style="margin-bottom:1.5rem;"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger" style="margin-bottom:1.5rem;"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Settings Tabs -->
<div class="settings-tabs">
    <button class="settings-tab active" data-tab="tab-general">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:0.5rem;vertical-align:-2px;"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
        Pengaturan Umum
    </button>
    <button class="settings-tab" data-tab="tab-payment">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:0.5rem;vertical-align:-2px;"><rect x="2" y="5" width="20" height="14" rx="2" ry="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
        Pembayaran
    </button>
    <button class="settings-tab" data-tab="tab-shipping">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:0.5rem;vertical-align:-2px;"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
        Pengiriman
    </button>
</div>

<!-- Tab 1: General Settings -->
<div class="settings-panel active" id="tab-general">
    <form action="settings.php" method="POST">
        <input type="hidden" name="section" value="general">
        
        <div class="settings-card">
            <h4>Informasi Toko</h4>
            <p>Informasi dasar yang ditampilkan di website dan komunikasi pelanggan.</p>
            
            <div class="grid grid-cols-2 gap-2">
                <div class="form-group">
                    <label class="form-label" for="store_name">Nama Toko</label>
                    <input type="text" id="store_name" name="store_name" class="form-control" value="<?= htmlspecialchars((string)$settings->general->store_name) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="tagline">Tagline</label>
                    <input type="text" id="tagline" name="tagline" class="form-control" value="<?= htmlspecialchars((string)$settings->general->tagline) ?>">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-2">
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars((string)$settings->general->email) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="phone">Telepon / WhatsApp</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars((string)$settings->general->phone) ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="address">Alamat Toko</label>
                <textarea id="address" name="address" class="form-control" rows="2"><?= htmlspecialchars((string)$settings->general->address) ?></textarea>
            </div>
            
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="operating_hours">Jam Operasional</label>
                <input type="text" id="operating_hours" name="operating_hours" class="form-control" value="<?= htmlspecialchars((string)$settings->general->operating_hours) ?>">
            </div>
        </div>
        
        <div class="flex justify-between items-center">
            <span style="font-size:0.85rem;color:var(--text-muted);">Perubahan akan langsung diterapkan di seluruh website.</span>
            <button type="submit" class="btn btn-primary" style="padding:0.75rem 2rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:0.5rem;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>

<!-- Tab 2: Payment Settings -->
<div class="settings-panel" id="tab-payment">
    <div class="settings-card">
        <h4>Transfer Bank</h4>
        <p>Rekening bank yang tersedia untuk pembayaran pelanggan.</p>
        
        <div style="display:flex;flex-direction:column;gap:0.75rem;">
            <?php if ($settings && isset($settings->payment->bank_transfer->bank)):
                foreach ($settings->payment->bank_transfer->bank as $bank): ?>
                <div class="flex items-center gap-2" style="background:var(--bg-main);padding:1rem;border-radius:var(--border-radius-md);">
                    <div style="width:60px;height:36px;background:var(--primary-light);border-radius:var(--border-radius-sm);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8rem;color:var(--primary-color);">
                        <?= htmlspecialchars((string)$bank['name']) ?>
                    </div>
                    <div style="flex:1;">
                        <div style="font-weight:500;font-size:0.95rem;"><?= htmlspecialchars((string)$bank['name']) ?></div>
                        <div style="font-family:monospace;font-size:0.9rem;color:var(--text-muted);"><?= htmlspecialchars((string)$bank) ?></div>
                    </div>
                    <span class="badge status-active">Aktif</span>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
    
    <div class="settings-card">
        <h4>E-Wallet</h4>
        <p>Dompet digital yang tersedia sebagai metode pembayaran.</p>
        
        <div style="display:flex;flex-direction:column;gap:0.75rem;">
            <?php if ($settings && isset($settings->payment->ewallet->wallet)):
                foreach ($settings->payment->ewallet->wallet as $wallet): ?>
                <div class="flex items-center gap-2" style="background:var(--bg-main);padding:1rem;border-radius:var(--border-radius-md);">
                    <div style="width:60px;height:36px;background:var(--status-success-bg);border-radius:var(--border-radius-sm);display:flex;align-items:center;justify-content:center;font-weight:600;font-size:0.75rem;color:var(--status-success);">
                        <?= htmlspecialchars((string)$wallet['name']) ?>
                    </div>
                    <div style="flex:1;">
                        <div style="font-weight:500;font-size:0.95rem;"><?= htmlspecialchars((string)$wallet['name']) ?></div>
                        <div style="font-size:0.85rem;color:var(--text-muted);"><?= htmlspecialchars((string)$wallet) ?></div>
                    </div>
                    <span class="badge status-active">Aktif</span>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
    
    <div class="settings-card">
        <h4>Cash on Delivery (COD)</h4>
        <p>Pembayaran di tempat saat barang diterima.</p>
        
        <div class="flex items-center gap-2" style="background:var(--bg-main);padding:1rem;border-radius:var(--border-radius-md);">
            <div style="flex:1;">
                <div style="font-weight:500;">COD Aktif</div>
                <div style="font-size:0.85rem;color:var(--text-muted);">Maksimal <?= isset($settings->payment->cod->max_amount) ? formatRupiah((int)$settings->payment->cod->max_amount) : 'Rp 1.000.000' ?></div>
            </div>
            <span class="badge status-active">Aktif</span>
        </div>
    </div>
</div>

<!-- Tab 3: Shipping Settings -->
<div class="settings-panel" id="tab-shipping">
    <div class="settings-card">
        <h4>Kurir Pengiriman</h4>
        <p>Pilih kurir yang tersedia untuk pengiriman pesanan.</p>
        
        <div style="display:flex;flex-direction:column;gap:0.75rem;">
            <?php if ($settings && isset($settings->shipping->courier)):
                foreach ($settings->shipping->courier as $courier): 
                    $enabled = ((string)$courier['enabled']) === '1';
            ?>
                <div class="flex items-center gap-2" style="background:var(--bg-main);padding:1rem;border-radius:var(--border-radius-md);">
                    <div style="width:40px;height:40px;background:<?= $enabled ? 'var(--primary-light)' : 'var(--bg-secondary)' ?>;border-radius:var(--border-radius-sm);display:flex;align-items:center;justify-content:center;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="<?= $enabled ? 'var(--primary-color)' : 'var(--text-muted)' ?>" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                    </div>
                    <div style="flex:1;">
                        <div style="font-weight:500;"><?= htmlspecialchars((string)$courier['name']) ?></div>
                    </div>
                    <span class="badge <?= $enabled ? 'status-active' : 'status-inactive' ?>"><?= $enabled ? 'Aktif' : 'Nonaktif' ?></span>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
    
    <div class="settings-card">
        <h4>Pengaturan Ongkos Kirim</h4>
        <p>Konfigurasi ongkos kirim default dan minimal gratis ongkir.</p>
        
        <div class="grid grid-cols-2 gap-2">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Ongkir Default</label>
                <div class="form-control" style="background:var(--bg-main);font-weight:500;">
                    <?= isset($settings->shipping->default_cost) ? formatRupiah((int)$settings->shipping->default_cost) : 'Rp 25.000' ?>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Minimum Gratis Ongkir</label>
                <div class="form-control" style="background:var(--bg-main);font-weight:500;">
                    <?= isset($settings->shipping->free_shipping_min) ? formatRupiah((int)$settings->shipping->free_shipping_min) : 'Rp 500.000' ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/ShodweThumbler/assets/js/main.js"></script>
</main>
</div>
</body>
</html>
