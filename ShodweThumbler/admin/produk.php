<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/xml-functions.php';
require_once __DIR__ . '/../includes/helpers.php';
requireAdmin();

$products = getAllProducts();

$filter = $_GET['filter'] ?? 'semua';
$countSemua = count($products);
$countAktif = count(array_filter($products, function($p) { return $p['status'] == 'Aktif'; }));
$countNonaktif = count(array_filter($products, function($p) { return $p['status'] == 'Nonaktif'; }));

if ($filter === 'aktif') {
    $products = array_values(array_filter($products, function($p) { return $p['status'] == 'Aktif'; }));
} elseif ($filter === 'nonaktif') {
    $products = array_values(array_filter($products, function($p) { return $p['status'] == 'Nonaktif'; }));
}

// Flash messages
$flash = getFlashMessage();
?>

<?php include '../components/admin-header.php'; ?>

<script>
    // Update admin header title
    document.querySelector('.admin-header > div').innerHTML = '<h1 style="font-size: 1.8rem; margin-bottom: 0;">Manajemen Produk</h1><p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Kelola inventaris, tambah produk baru, dan atur ketersediaan.</p>';
</script>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>" style="margin-bottom:1.5rem;"><?= htmlspecialchars($flash['message']) ?></div>
<?php endif; ?>

<div class="flex justify-between items-center mb-4">
    <div class="flex gap-2">
        <a href="?filter=semua" class="btn <?= $filter === 'semua' ? 'btn-outline' : '' ?>" style="<?= $filter === 'semua' ? 'background: white;' : 'background: transparent; border: none; color: var(--text-muted);' ?>">Semua Produk (<?= $countSemua ?>)</a>
        <a href="?filter=aktif" class="btn <?= $filter === 'aktif' ? 'btn-outline' : '' ?>" style="<?= $filter === 'aktif' ? 'background: white;' : 'background: transparent; border: none; color: var(--text-muted);' ?>">Aktif (<?= $countAktif ?>)</a>
        <a href="?filter=nonaktif" class="btn <?= $filter === 'nonaktif' ? 'btn-outline' : '' ?>" style="<?= $filter === 'nonaktif' ? 'background: white;' : 'background: transparent; border: none; color: var(--text-muted);' ?>">Nonaktif (<?= $countNonaktif ?>)</a>
    </div>
    
    <div class="flex gap-2">
        <button class="btn btn-outline" style="background: white; padding: 0.5rem 1rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.5rem;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Export CSV
        </button>
        <button class="btn btn-primary" style="padding: 0.5rem 1rem;" onclick="openModal('addProductModal')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.5rem;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah Produk
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
            <span style="font-size: 0.85rem; color: var(--text-muted);">Menampilkan 1-<?= count($products) ?> dari <?= count($products) ?></span>
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
                <th>Produk</th>
                <th>SKU</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Status</th>
                <th style="width: 80px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($products as $product): ?>
            <tr>
                <td><input type="checkbox" style="accent-color: var(--primary-color);"></td>
                <td>
                    <div class="flex items-center gap-2">
                        <div style="width: 40px; height: 40px; border-radius: var(--border-radius-sm); background: var(--bg-secondary); overflow: hidden; flex-shrink: 0;">
                            <img src="../assets/images/<?= $product['image'] ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div>
                            <div style="font-weight: 500; font-size: 0.95rem;"><?= htmlspecialchars($product['name']) ?></div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);"><?= $product['type'] ?> • <?= $product['color'] ?></div>
                        </div>
                    </div>
                </td>
                <td style="font-family: monospace; font-size: 0.85rem;"><?= htmlspecialchars($product['id']) ?></td>
                <td><?= htmlspecialchars($product['category']) ?></td>
                <td style="font-weight: 500;"><?= formatRupiah($product['price']) ?></td>
                <td>
                    <span style="font-weight: 500; color: <?= $product['stock'] < 10 ? 'var(--status-danger)' : 'inherit' ?>"><?= $product['stock'] ?></span>
                </td>
                <td>
                    <span class="badge <?= getStatusClass($product['status']) ?>"><?= htmlspecialchars($product['status']) ?></span>
                </td>
                <td>
                    <div class="flex gap-1">
                        <button class="btn-icon" style="width: 32px; height: 32px; background: white; border: 1px solid var(--border-color);" title="Edit" onclick="editProduct(<?= htmlspecialchars(json_encode($product)) ?>)">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </button>
                        <button class="btn-icon" style="width: 32px; height: 32px; background: white; border: 1px solid var(--border-color); color: var(--status-danger);" title="Delete" onclick="confirmDeleteProduct('<?= $product['id'] ?>', '<?= htmlspecialchars($product['name']) ?>')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Product Modal -->
<div class="modal-overlay modal-md" id="addProductModal">
    <div class="modal-content" style="max-width:700px;">
        <button class="modal-close"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        <div style="padding:2rem;">
            <h2 style="font-size:1.5rem;margin-bottom:0.5rem;">Tambah Produk Baru</h2>
            <p style="color:var(--text-muted);font-size:0.9rem;margin-bottom:2rem;">Lengkapi detail produk di bawah ini.</p>
            
            <form action="manage-product.php" method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="grid grid-cols-2 gap-2">
                    <div class="form-group">
                        <label class="form-label">Nama Produk *</label>
                        <input type="text" name="name" class="form-control" placeholder="Classic Cream Tumbler" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select name="category" class="form-control">
                            <option value="Custom Tumbler">Custom Tumbler</option>
                            <option value="Best Seller">Best Seller</option>
                            <option value="New Arrival">New Arrival</option>
                            <option value="Accessories">Accessories</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-2">
                    <div class="form-group">
                        <label class="form-label">Harga (Rp) *</label>
                        <input type="number" name="price" class="form-control" placeholder="249000" required min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stok *</label>
                        <input type="number" name="stock" class="form-control" placeholder="50" required min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kapasitas</label>
                        <select name="capacity" class="form-control">
                            <option value="350ml">350ml</option>
                            <option value="500ml" selected>500ml</option>
                            <option value="750ml">750ml</option>
                            <option value="1000ml">1000ml</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-2">
                    <div class="form-group">
                        <label class="form-label">Tipe</label>
                        <select name="type" class="form-control">
                            <option value="Non-custom">Non-custom</option>
                            <option value="Custom">Custom (Engrave)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Gambar Produk</label>
                    <select name="image" class="form-control">
                        <option value="tumbler_cream.png">Cream Tumbler</option>
                        <option value="tumbler_mocha.png">Mocha Tumbler</option>
                        <option value="tumbler_black.png">Black Tumbler</option>
                        <option value="tumbler_white.png">White Tumbler</option>
                        <option value="tumbler_rosegold.png">Rose Gold Tumbler</option>
                        <option value="tumbler_green.png">Green Tumbler</option>
                    </select>
                </div>
                
                <!-- Color Variants -->
                <div class="form-group">
                    <label class="form-label">Varian Warna</label>
                    <div id="colorVariantsContainer">
                        <div class="color-variant-item">
                            <input type="color" name="variant_color[]" value="#F5E6D0">
                            <input type="text" name="variant_name[]" class="form-control" placeholder="Nama warna (cth: Cream)" style="flex:1;" value="">
                            <input type="number" name="variant_stock[]" class="form-control" placeholder="Stok" style="width:80px;" min="0" value="0">
                            <button type="button" class="color-variant-remove" onclick="this.parentElement.remove()">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>
                    </div>
                    <button type="button" id="addColorVariant" class="btn btn-outline" style="padding:0.5rem 1rem;font-size:0.85rem;margin-top:0.5rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:0.5rem;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Tambah Warna
                    </button>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi produk..."></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tags (pisahkan dengan koma)</label>
                    <input type="text" name="tags" class="form-control" placeholder="Best Seller, Custom Name">
                </div>
                
                <!-- Hidden: Primary color from first variant -->
                <input type="hidden" name="color" id="primaryColorInput" value="">
                <input type="hidden" name="custom_name" value="0">
                
                <div class="flex gap-2 justify-between" style="margin-top:1.5rem;">
                    <button type="button" class="btn btn-outline" onclick="closeModal('addProductModal')" style="padding:0.75rem 1.5rem;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="padding:0.75rem 2rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:0.5rem;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Tambah Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal-overlay modal-md" id="editProductModal">
    <div class="modal-content" style="max-width:700px;">
        <button class="modal-close"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
        <div style="padding:2rem;">
            <h2 style="font-size:1.5rem;margin-bottom:0.5rem;">Edit Produk</h2>
            <p style="color:var(--text-muted);font-size:0.9rem;margin-bottom:2rem;">Perbarui detail produk.</p>
            
            <form action="manage-product.php" method="POST" id="editProductForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editId">
                
                <div class="grid grid-cols-2 gap-2">
                    <div class="form-group">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select name="category" id="editCategory" class="form-control">
                            <option value="Custom Tumbler">Custom Tumbler</option>
                            <option value="Best Seller">Best Seller</option>
                            <option value="New Arrival">New Arrival</option>
                            <option value="Accessories">Accessories</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-2">
                    <div class="form-group">
                        <label class="form-label">Harga (Rp)</label>
                        <input type="number" name="price" id="editPrice" class="form-control" required min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stock" id="editStock" class="form-control" required min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kapasitas</label>
                        <select name="capacity" id="editCapacity" class="form-control">
                            <option value="350ml">350ml</option>
                            <option value="500ml">500ml</option>
                            <option value="750ml">750ml</option>
                            <option value="1000ml">1000ml</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-2">
                    <div class="form-group">
                        <label class="form-label">Tipe</label>
                        <select name="type" id="editType" class="form-control">
                            <option value="Non-custom">Non-custom</option>
                            <option value="Custom">Custom</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" id="editStatus" class="form-control">
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Warna</label>
                        <input type="text" name="color" id="editColor" class="form-control">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" id="editDescription" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tags</label>
                    <input type="text" name="tags" id="editTags" class="form-control">
                </div>
                
                <div class="flex gap-2 justify-between" style="margin-top:1.5rem;">
                    <button type="button" class="btn btn-outline" onclick="closeModal('editProductModal')" style="padding:0.75rem 1.5rem;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="padding:0.75rem 2rem;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay modal-sm" id="deleteProductModal">
    <div class="modal-content" style="max-width:450px;">
        <div class="confirm-dialog">
            <div class="confirm-icon" style="background:var(--status-danger-bg);color:var(--status-danger);">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
            </div>
            <h3>Hapus Produk?</h3>
            <p id="deleteProductName">Apakah Anda yakin ingin menghapus produk ini? Aksi ini tidak dapat dikembalikan.</p>
            <form action="manage-product.php" method="POST" style="display:inline;">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="deleteProductId">
                <div class="flex gap-2 justify-center">
                    <button type="button" class="btn btn-outline" onclick="closeModal('deleteProductModal')" style="padding:0.75rem 1.5rem;">Batal</button>
                    <button type="submit" class="btn" style="background:var(--status-danger);color:white;padding:0.75rem 1.5rem;">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/ShodweThumbler/assets/js/main.js"></script>
<script>
function editProduct(product) {
    document.getElementById('editId').value = product.id;
    document.getElementById('editName').value = product.name;
    document.getElementById('editCategory').value = product.category;
    document.getElementById('editPrice').value = product.price;
    document.getElementById('editStock').value = product.stock;
    document.getElementById('editCapacity').value = product.capacity;
    document.getElementById('editType').value = product.type;
    document.getElementById('editStatus').value = product.status;
    document.getElementById('editColor').value = product.color;
    document.getElementById('editDescription').value = product.description;
    document.getElementById('editTags').value = product.tags;
    openModal('editProductModal');
}

function confirmDeleteProduct(id, name) {
    document.getElementById('deleteProductId').value = id;
    document.getElementById('deleteProductName').textContent = 'Apakah Anda yakin ingin menghapus "' + name + '"? Aksi ini tidak dapat dikembalikan.';
    openModal('deleteProductModal');
}

// Set primary color from first variant on form submit
document.querySelector('#addProductModal form').addEventListener('submit', function() {
    const firstVariantName = this.querySelector('input[name="variant_name[]"]');
    if (firstVariantName && firstVariantName.value) {
        document.getElementById('primaryColorInput').value = firstVariantName.value;
    }
    // Set custom_name based on type
    const typeSelect = this.querySelector('select[name="type"]');
    const customField = this.querySelector('input[name="custom_name"]');
    if (typeSelect && customField) {
        customField.value = typeSelect.value === 'Custom' ? '1' : '0';
    }
});
</script>

</main>
</div>
</body>
</html>
