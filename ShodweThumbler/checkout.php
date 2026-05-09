<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/xml-functions.php';
requireLogin();

$user = getCurrentUser();

// Handle checkout form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartData = json_decode($_POST['cart_data'] ?? '[]', true);
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $zip = $_POST['zip'] ?? '';
    $courier = $_POST['courier'] ?? 'JNE Express';
    $paymentMethod = $_POST['payment_method'] ?? 'Bank Transfer';
    
    if (empty($cartData) || empty($address)) {
        $error = 'Data pesanan tidak lengkap.';
    } else {
        $totalQuantity = 0;
        $totalPrice = 0;
        $productNames = [];
        
        foreach ($cartData as $item) {
            $totalQuantity += $item['quantity'];
            $totalPrice += ($item['price'] * $item['quantity']);
            $productNames[] = $item['name'];
        }
        
        $productId = count($cartData) > 1 ? 'MULTI' : ($cartData[0]['id'] ?? 'TMB-001');
        $productName = count($cartData) > 1 ? 'Multiple Items' : $cartData[0]['name'];
        
        $orderData = [
            'customer_name' => $user['name'],
            'customer_email' => $user['email'],
            'product' => $productName,
            'product_id' => $productId,
            'quantity' => $totalQuantity,
            'total' => $totalPrice,
            'status' => 'Pending',
            'payment_status' => 'Belum Bayar',
            'shipping_address' => $address . ', ' . $city . ', ' . $zip,
            'courier' => $courier,
            'payment_method' => $paymentMethod,
            'tracking_number' => ''
        ];
        
        $orderId = addOrder($orderData);
        if ($orderId) {
            header('Location: /ShodweThumbler/pembayaran-berhasil.php?id=' . urlencode($orderId));
            exit;
        } else {
            $error = 'Gagal membuat pesanan.';
        }
    }
}
?>

<?php include 'components/header.php'; ?>

<section style="background-color: var(--bg-main); padding: 3rem 0; min-height: calc(100vh - 200px);">
    <div class="container">
        <h1 style="font-size: 2rem; margin-bottom: 2rem;">Checkout</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" style="margin-bottom:1.5rem;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <div class="grid grid-cols-3 gap-4">
            <!-- Checkout Form -->
            <div style="grid-column: span 2;">
                <form method="POST" id="checkoutForm">
                    <input type="hidden" name="cart_data" id="cartDataInput">
                    
                    <div style="background: white; border-radius: var(--border-radius-lg); border: 1px solid var(--border-color); padding: 2rem; margin-bottom: 1.5rem;">
                        <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem;">Informasi Pengiriman</h3>
                        
                        <div class="grid grid-cols-2 gap-2">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" readonly style="background:var(--bg-main);">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly style="background:var(--bg-main);">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Alamat Lengkap *</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Jl. Sudirman No. 45, Tower C, Lantai 12" required></textarea>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2">
                            <div class="form-group">
                                <label class="form-label">Kota *</label>
                                <input type="text" name="city" class="form-control" placeholder="Jakarta Selatan" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Kode Pos *</label>
                                <input type="text" name="zip" class="form-control" placeholder="12190" required>
                            </div>
                        </div>
                    </div>
                    
                    <div style="background: white; border-radius: var(--border-radius-lg); border: 1px solid var(--border-color); padding: 2rem;">
                        <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem;">Metode Pengiriman</h3>
                        
                        <div style="display:flex;flex-direction:column;gap:0.75rem;">
                            <label style="display:flex;align-items:center;gap:1rem;padding:1rem;border:2px solid var(--primary-color);border-radius:var(--border-radius-md);cursor:pointer;background:var(--primary-light);">
                                <input type="radio" name="courier" value="JNE Express" checked style="accent-color:var(--primary-color);">
                                <div style="flex:1;">
                                    <div style="font-weight:500;">JNE Express</div>
                                    <div style="font-size:0.8rem;color:var(--text-muted);">Estimasi 1-2 hari kerja</div>
                                </div>
                                <span style="font-weight:600;">Rp 25.000</span>
                            </label>
                            
                            <label style="display:flex;align-items:center;gap:1rem;padding:1rem;border:1px solid var(--border-color);border-radius:var(--border-radius-md);cursor:pointer;">
                                <input type="radio" name="courier" value="SiCepat" style="accent-color:var(--primary-color);">
                                <div style="flex:1;">
                                    <div style="font-weight:500;">SiCepat</div>
                                    <div style="font-size:0.8rem;color:var(--text-muted);">Estimasi 2-3 hari kerja</div>
                                </div>
                                <span style="font-weight:600;">Rp 18.000</span>
                            </label>
                            
                            <label style="display:flex;align-items:center;gap:1rem;padding:1rem;border:1px solid var(--border-color);border-radius:var(--border-radius-md);cursor:pointer;">
                                <input type="radio" name="courier" value="AnterAja" style="accent-color:var(--primary-color);">
                                <div style="flex:1;">
                                    <div style="font-weight:500;">AnterAja</div>
                                    <div style="font-size:0.8rem;color:var(--text-muted);">Estimasi 3-5 hari kerja</div>
                                </div>
                                <span style="font-weight:600;">Rp 12.000</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Metode Pembayaran -->
                    <div style="background: white; border-radius: var(--border-radius-lg); border: 1px solid var(--border-color); padding: 2rem; margin-top: 1.5rem;">
                        <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem;">Metode Pembayaran</h3>
                        
                        <div style="display:flex;flex-direction:column;gap:0.75rem;">
                            <label style="display:flex;align-items:center;gap:1rem;padding:1rem;border:2px solid var(--primary-color);border-radius:var(--border-radius-md);cursor:pointer;background:var(--primary-light);">
                                <input type="radio" name="payment_method" value="Bank Transfer" checked style="accent-color:var(--primary-color);">
                                <div style="flex:1;">
                                    <div style="font-weight:500;">Transfer Bank (Manual)</div>
                                    <div style="font-size:0.8rem;color:var(--text-muted);">BCA, Mandiri, BNI, BRI</div>
                                </div>
                            </label>
                            
                            <label style="display:flex;align-items:center;gap:1rem;padding:1rem;border:1px solid var(--border-color);border-radius:var(--border-radius-md);cursor:pointer;">
                                <input type="radio" name="payment_method" value="COD" style="accent-color:var(--primary-color);">
                                <div style="flex:1;">
                                    <div style="font-weight:500;">Cash on Delivery (COD)</div>
                                    <div style="font-size:0.8rem;color:var(--text-muted);">Bayar tunai saat pesanan tiba di tujuan</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-full" id="placeOrderBtn" style="padding:1rem;font-size:1.1rem;margin-top:1.5rem;" disabled>
                        Buat Pesanan
                    </button>
                </form>
            </div>
            
            <!-- Order Summary -->
            <div>
                <div style="background: white; border-radius: var(--border-radius-lg); border: 1px solid var(--border-color); padding: 1.5rem; position: sticky; top: 2rem;">
                    <h3 style="font-size: 1.15rem; margin-bottom: 1.25rem;">Ringkasan Pesanan</h3>
                    
                    <div id="checkoutItems">
                        <!-- Populated by JS from localStorage -->
                        <div class="cart-empty" style="padding:2rem 0;">
                            <p style="font-size:0.9rem;">Keranjang kosong</p>
                            <a href="/ShodweThumbler/toko.php" class="btn btn-outline" style="margin-top:0.5rem;padding:0.5rem 1rem;font-size:0.85rem;">Belanja</a>
                        </div>
                    </div>
                    
                    <div id="checkoutSummary" style="display:none;">
                        <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; margin-top: 1rem;">
                            <div class="flex justify-between mb-2">
                                <span style="color: var(--text-muted);">Subtotal</span>
                                <span id="coSubtotal" style="font-weight: 500;"></span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span style="color: var(--text-muted);">Ongkos Kirim</span>
                                <span id="coShipping" style="font-weight: 500;">Rp 25.000</span>
                            </div>
                        </div>
                        
                        <div class="flex justify-between" style="padding-top: 1rem; border-top: 2px solid var(--primary-color); margin-top: 0.5rem;">
                            <span style="font-weight: 600; font-size: 1.05rem;">Total</span>
                            <span id="coTotal" style="font-weight: 700; font-size: 1.25rem; color: var(--primary-color);"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cartRaw = localStorage.getItem('shodwe_cart');
    let items = [];
    try { items = JSON.parse(cartRaw) || []; } catch(e) { items = []; }
    
    const container = document.getElementById('checkoutItems');
    const summary = document.getElementById('checkoutSummary');
    const cartInput = document.getElementById('cartDataInput');
    const placeBtn = document.getElementById('placeOrderBtn');
    
    function fmt(n) {
        return 'Rp ' + parseInt(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    
    if (items.length === 0) return;
    
    cartInput.value = JSON.stringify(items);
    placeBtn.disabled = false;
    
    let html = '';
    let subtotal = 0;
    items.forEach(item => {
        const lineTotal = item.price * item.quantity;
        subtotal += lineTotal;
        html += `
        <div class="flex gap-2" style="margin-bottom:1rem;padding-bottom:1rem;border-bottom:1px solid var(--border-color);">
            <div style="width:60px;height:60px;background:var(--bg-secondary);border-radius:var(--border-radius-sm);overflow:hidden;flex-shrink:0;">
                <img src="/ShodweThumbler/assets/images/${item.image}" style="width:100%;height:100%;object-fit:cover;">
            </div>
            <div style="flex:1;">
                <h4 style="font-size:0.9rem;margin-bottom:0.25rem;">${item.name}</h4>
                ${item.color ? `<span style="font-size:0.75rem;color:var(--text-muted);">${item.color}</span>` : ''}
                <div class="flex justify-between items-center" style="margin-top:0.25rem;">
                    <span style="font-size:0.8rem;color:var(--text-muted);">${item.quantity}x ${fmt(item.price)}</span>
                    <span style="font-weight:600;font-size:0.9rem;">${fmt(lineTotal)}</span>
                </div>
            </div>
        </div>`;
    });
    
    container.innerHTML = html;
    summary.style.display = 'block';
    
    const shippingCost = 25000;
    document.getElementById('coSubtotal').textContent = fmt(subtotal);
    document.getElementById('coShipping').textContent = fmt(shippingCost);
    document.getElementById('coTotal').textContent = fmt(subtotal + shippingCost);
    
    // Update shipping cost when courier changes
    document.querySelectorAll('input[name="courier"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const costs = {'JNE Express': 25000, 'SiCepat': 18000, 'AnterAja': 12000};
            const cost = costs[this.value] || 25000;
            document.getElementById('coShipping').textContent = fmt(cost);
            document.getElementById('coTotal').textContent = fmt(subtotal + cost);
            
            // Update border styling
            document.querySelectorAll('input[name="courier"]').forEach(r => {
                const label = r.closest('label');
                if (r.checked) {
                    label.style.borderColor = 'var(--primary-color)';
                    label.style.borderWidth = '2px';
                    label.style.background = 'var(--primary-light)';
                } else {
                    label.style.borderColor = 'var(--border-color)';
                    label.style.borderWidth = '1px';
                    label.style.background = 'transparent';
                }
            });
        });
    });
    
    // Update border styling for payment method
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('input[name="payment_method"]').forEach(r => {
                const label = r.closest('label');
                if (r.checked) {
                    label.style.borderColor = 'var(--primary-color)';
                    label.style.borderWidth = '2px';
                    label.style.background = 'var(--primary-light)';
                } else {
                    label.style.borderColor = 'var(--border-color)';
                    label.style.borderWidth = '1px';
                    label.style.background = 'transparent';
                }
            });
        });
    });
    
    // Clear cart on successful order placement
    document.getElementById('checkoutForm').addEventListener('submit', function() {
        localStorage.removeItem('shodwe_cart');
    });
});
</script>
