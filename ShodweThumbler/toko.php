<?php
require_once __DIR__ . '/includes/xml-functions.php';

// Get all categories for filter
$allProducts = getAllProducts();
$categories = [];
$colors = [];
foreach ($allProducts as $p) {
    if (!in_array($p['category'], $categories)) $categories[] = $p['category'];
    if (!in_array($p['color'], $colors) && !empty($p['color'])) $colors[] = $p['color'];
}

// Build filter parameters
$filters = [];
if (isset($_GET['category'])) $filters['category'] = $_GET['category'];
if (isset($_GET['color'])) $filters['color'] = $_GET['color'];
if (isset($_GET['type'])) $filters['type'] = $_GET['type'];
if (isset($_GET['search'])) $filters['search'] = $_GET['search'];

$products = getProductsByFilter($filters);

$bannerImg = getDashboardImage('toko', 'banner');
$bannerSrc = $bannerImg ? (file_exists(__DIR__ . '/assets/uploads/' . $bannerImg['filename']) ? 'assets/uploads/' . $bannerImg['filename'] : 'assets/images/' . $bannerImg['filename']) : 'assets/images/tumbler_green.png';
$bannerAlt = $bannerImg ? $bannerImg['alt_text'] : 'Premium Green Tumbler';
?>

<?php include 'components/header.php'; ?>

<div class="container py-8 flex gap-4" style="align-items: flex-start;">
    
    <!-- Sidebar Filters -->
    <aside style="width: 250px; flex-shrink: 0; background: var(--bg-white); padding: 1.5rem; border-radius: var(--border-radius-lg); border: 1px solid var(--border-color);">
        <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;">Filter Products</h3>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem;">Temukan tumbler sesuai gaya dan kebutuhanmu.</p>
        
        <form action="toko.php" method="GET">
            <div class="form-group mb-3">
                <label class="form-label">Kategori</label>
                <div class="flex flex-col gap-1">
                    <label class="flex items-center gap-1" style="font-size: 0.9rem;">
                        <input type="radio" name="category" value="All" <?= empty($_GET['category']) || $_GET['category'] == 'All' ? 'checked' : '' ?> onchange="this.form.submit()"> Semua
                    </label>
                    <?php foreach($categories as $cat): ?>
                        <label class="flex items-center gap-1" style="font-size: 0.9rem;">
                            <input type="radio" name="category" value="<?= htmlspecialchars($cat) ?>" <?= isset($_GET['category']) && $_GET['category'] == $cat ? 'checked' : '' ?> onchange="this.form.submit()"> <?= htmlspecialchars($cat) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="form-group mb-3">
                <label class="form-label">Warna</label>
                <div class="flex" style="flex-wrap: wrap; gap: 0.5rem;">
                    <?php foreach($colors as $col): ?>
                        <button type="submit" name="color" value="<?= htmlspecialchars($col) ?>" 
                                style="padding: 0.25rem 0.75rem; border-radius: var(--border-radius-full); font-size: 0.8rem; border: 1px solid <?= isset($_GET['color']) && $_GET['color'] == $col ? 'var(--primary-color)' : 'var(--border-color)' ?>; background: <?= isset($_GET['color']) && $_GET['color'] == $col ? 'var(--primary-light)' : 'var(--bg-secondary)' ?>; cursor: pointer;">
                            <?= htmlspecialchars($col) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="form-group mb-3">
                <label class="form-label">Tipe</label>
                <div class="flex" style="flex-wrap: wrap; gap: 0.5rem;">
                    <button type="submit" name="type" value="Custom" 
                            style="padding: 0.25rem 0.75rem; border-radius: var(--border-radius-full); font-size: 0.8rem; border: 1px solid <?= isset($_GET['type']) && $_GET['type'] == 'Custom' ? 'var(--primary-color)' : 'var(--border-color)' ?>; background: <?= isset($_GET['type']) && $_GET['type'] == 'Custom' ? 'var(--primary-color)' : 'var(--bg-secondary)' ?>; color: <?= isset($_GET['type']) && $_GET['type'] == 'Custom' ? 'white' : 'var(--text-main)' ?>; cursor: pointer;">
                        Custom
                    </button>
                    <button type="submit" name="type" value="Non-custom" 
                            style="padding: 0.25rem 0.75rem; border-radius: var(--border-radius-full); font-size: 0.8rem; border: 1px solid <?= isset($_GET['type']) && $_GET['type'] == 'Non-custom' ? 'var(--primary-color)' : 'var(--border-color)' ?>; background: <?= isset($_GET['type']) && $_GET['type'] == 'Non-custom' ? 'var(--primary-color)' : 'var(--bg-secondary)' ?>; color: <?= isset($_GET['type']) && $_GET['type'] == 'Non-custom' ? 'white' : 'var(--text-main)' ?>; cursor: pointer;">
                        Non-custom
                    </button>
                </div>
            </div>
            
            <?php if(!empty($_GET)): ?>
                <a href="toko.php" class="btn btn-outline w-full" style="padding: 0.5rem; font-size: 0.85rem;">Clear Filters</a>
            <?php endif; ?>
        </form>
    </aside>
    
    <!-- Product Grid -->
    <main style="flex: 1;">
        <!-- Banner -->
        <div style="background: white; border-radius: var(--border-radius-lg); overflow: hidden; display: flex; margin-bottom: 2rem; border: 1px solid var(--border-color);">
            <div style="padding: 3rem 2rem; flex: 1; display: flex; flex-direction: column; justify-content: center;">
                <div style="display: inline-block; padding: 0.25rem 1rem; background: var(--bg-secondary); border-radius: var(--border-radius-full); font-size: 0.75rem; font-weight: 500; margin-bottom: 1rem; width: max-content;">
                    Elegant tumbler collection
                </div>
                <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">Produk Kami</h1>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 1.5rem; max-width: 400px;">
                    Koleksi tumbler premium untuk rutinitas harian yang lebih stylish. Temukan custom tumbler, best seller, dan warna-warna lembut yang dirancang untuk semua kalangan.
                </p>
                <div class="flex gap-1 flex-wrap">
                    <span style="font-size: 0.8rem; background: var(--bg-main); padding: 0.25rem 0.75rem; border-radius: var(--border-radius-full);">Custom Name Available</span>
                    <span style="font-size: 0.8rem; background: var(--bg-main); padding: 0.25rem 0.75rem; border-radius: var(--border-radius-full);">Best Seller Picks</span>
                    <span style="font-size: 0.8rem; background: var(--bg-main); padding: 0.25rem 0.75rem; border-radius: var(--border-radius-full);">Premium Stainless</span>
                </div>
            </div>
            <div style="width: 300px; background: var(--primary-light);">
                <img src="<?= $bannerSrc ?>" alt="<?= htmlspecialchars($bannerAlt) ?>" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
        </div>
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 style="font-size: 1.5rem; margin-bottom: 0.25rem;">Didesain untuk gaya sehari-hari</h2>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Menampilkan <?= count($products) ?> produk dari koleksi tumbler Shodwe.</p>
            </div>
            <div class="flex gap-1">
                <button class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.875rem; background: white;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.5rem;"><line x1="21" y1="10" x2="3" y2="10"></line><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="14" x2="3" y2="14"></line><line x1="21" y1="18" x2="3" y2="18"></line></svg>
                    Sort: Terlaris
                </button>
            </div>
        </div>
        
        <?php if(count($products) > 0): ?>
            <div class="grid grid-cols-3 gap-2">
                <?php foreach($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image-wrap">
                            <?php if($product['tags']): ?>
                                <?php $tags = explode(',', $product['tags']); ?>
                                <span class="product-badge"><?= trim($tags[0]) ?></span>
                            <?php endif; ?>
                            <img src="assets/images/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                            
                            <div style="position: absolute; bottom: 1rem; left: 1rem; right: 1rem;">
                                <button class="btn" style="width: 100%; background: rgba(255,255,255,0.9); border: none; color: var(--text-main); font-size: 0.875rem;" data-quick-view='<?= json_encode(["id" => $product["id"], "name" => $product["name"], "price" => $product["price"], "image" => $product["image"], "color" => $product["color"], "description" => $product["description"], "rating" => $product["rating"], "reviews" => $product["reviews"], "category" => $product["category"], "capacity" => $product["capacity"], "type" => $product["type"]]) ?>'>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 0.5rem;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    Quick View
                                </button>
                            </div>
                        </div>
                        <div class="product-info" style="background: var(--bg-white);">
                            <div class="flex justify-between items-center mb-2" style="font-size: 0.8rem; color: var(--text-muted);">
                                <div class="flex items-center gap-1">
                                    <span style="color: var(--primary-color);">★</span> <?= $product['rating'] ?> (<?= $product['reviews'] ?>)
                                </div>
                                <?php if($product['type'] == 'Custom'): ?>
                                    <span>Custom Name</span>
                                <?php else: ?>
                                    <span><?= $product['capacity'] ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <h4 class="product-title" style="font-size: 1rem;"><?= htmlspecialchars($product['name']) ?></h4>
                            <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 38px;">
                                <?= htmlspecialchars($product['description']) ?>
                            </p>
                            
                            <div class="flex justify-between items-end">
                                <div>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">Price</span>
                                    <div class="product-price" style="margin-bottom: 0; font-size: 1rem;"><?= formatRupiah($product['price']) ?></div>
                                </div>
                                <button class="btn btn-primary" style="padding: 0.5rem; background: var(--primary-color); border: none;" data-add-to-cart='<?= json_encode(["id" => $product["id"], "name" => $product["name"], "price" => $product["price"], "image" => $product["image"], "color" => $product["color"]]) ?>'>
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><line x1="12" y1="11" x2="12" y2="17"></line><line x1="9" y1="14" x2="15" y2="14"></line></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <div class="flex justify-center mt-4 gap-1">
                <button class="btn-icon" style="background: var(--primary-color); color: white;">1</button>
                <button class="btn-icon">2</button>
                <button class="btn-icon">3</button>
                <button class="btn-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>
        <?php else: ?>
            <div style="padding: 4rem; text-align: center; background: white; border-radius: var(--border-radius-lg); border: 1px solid var(--border-color);">
                <p>Tidak ada produk yang ditemukan untuk filter ini.</p>
                <a href="toko.php" class="btn btn-primary mt-2">Reset Filter</a>
            </div>
        <?php endif; ?>
        
    </main>
</div>

<!-- Bottom Banner -->
<div class="container mb-4">
    <div class="flex justify-between items-center" style="background: white; padding: 1.5rem 2rem; border-radius: var(--border-radius-lg); border: 1px solid var(--border-color);">
        <div>
            <h4 style="margin-bottom: 0.25rem;">Shodwe Tumbler Hub</h4>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin: 0;">Premium aesthetic tumblers for every routine.</p>
        </div>
        <div class="flex gap-1">
            <span style="background: var(--bg-secondary); padding: 0.5rem 1rem; border-radius: var(--border-radius-full); font-size: 0.875rem;">Custom Order</span>
            <span style="background: var(--bg-secondary); padding: 0.5rem 1rem; border-radius: var(--border-radius-full); font-size: 0.875rem;">Shipping Info</span>
            <span style="background: var(--bg-secondary); padding: 0.5rem 1rem; border-radius: var(--border-radius-full); font-size: 0.875rem;">Customer Care</span>
        </div>
    </div>
</div>

<?php include 'components/footer.php'; ?>

<!-- Quick View Modal -->
<div class="modal-overlay" id="quickViewModal">
    <div class="modal-content" style="max-width:900px;">
        <button class="modal-close">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
        <div class="quick-view-body">
            <div class="quick-view-image">
                <img class="qv-image" src="" alt="">
            </div>
            <div class="quick-view-info">
                <div>
                    <span class="qv-category" style="font-size:0.8rem;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted);display:block;margin-bottom:0.5rem;"></span>
                    <h3 class="product-title qv-name"></h3>
                    <div class="flex items-center gap-1" style="margin-bottom:1rem;">
                        <span style="color:var(--primary-color);">★</span>
                        <span class="qv-rating" style="font-weight:500;"></span>
                        <span class="qv-reviews" style="color:var(--text-muted);font-size:0.85rem;"></span>
                        <span style="margin-left:0.5rem;font-size:0.8rem;color:var(--text-muted);">•</span>
                        <span class="qv-capacity" style="font-size:0.85rem;color:var(--text-muted);margin-left:0.25rem;"></span>
                    </div>
                    <div class="product-price qv-price"></div>
                    <p class="qv-desc" style="color:var(--text-muted);font-size:0.9rem;margin-bottom:1.5rem;line-height:1.6;"></p>
                </div>
                
                <div style="margin-bottom:1.5rem;">
                    <label style="font-size:0.875rem;font-weight:500;display:block;margin-bottom:0.5rem;">Pilih Warna</label>
                    <div class="color-swatches qv-colors"></div>
                </div>
                
                <div style="margin-bottom:1.5rem;">
                    <label style="font-size:0.875rem;font-weight:500;display:block;margin-bottom:0.5rem;">Jumlah</label>
                    <div class="qty-selector">
                        <button type="button" class="qv-qty-minus">−</button>
                        <input type="number" class="qv-qty" value="1" min="1" readonly>
                        <button type="button" class="qv-qty-plus">+</button>
                    </div>
                </div>
                
                <button class="btn btn-primary w-full qv-add-cart" style="padding:0.875rem;font-size:1rem;margin-top:auto;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:0.5rem;"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><line x1="12" y1="11" x2="12" y2="17"></line><line x1="9" y1="14" x2="15" y2="14"></line></svg>
                    Tambah ke Keranjang
                </button>
            </div>
        </div>
    </div>
</div>
