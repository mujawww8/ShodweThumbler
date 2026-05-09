<?php
require_once __DIR__ . '/includes/xml-functions.php';
$products = getAllProducts();
$featuredProducts = array_slice($products, 0, 4);
$reviews = getAllReviews();

// Load dynamic dashboard images
$heroImg = getDashboardImage('beranda', 'hero');
$heroSrc = $heroImg ? (file_exists(__DIR__ . '/assets/uploads/' . $heroImg['filename']) ? 'assets/uploads/' . $heroImg['filename'] : 'assets/images/' . $heroImg['filename']) : 'assets/images/tumbler_cream.png';
$heroAlt = $heroImg ? $heroImg['alt_text'] : 'Premium Cream Tumbler';

$personalizeImg = getDashboardImage('beranda', 'personalize');
$personalizeSrc = $personalizeImg ? (file_exists(__DIR__ . '/assets/uploads/' . $personalizeImg['filename']) ? 'assets/uploads/' . $personalizeImg['filename'] : 'assets/images/' . $personalizeImg['filename']) : 'assets/images/tumbler_mocha.png';
$personalizeAlt = $personalizeImg ? $personalizeImg['alt_text'] : 'Personalized Tumbler';
?>

<?php include 'components/header.php'; ?>

<!-- Hero Section -->
<section style="background-color: var(--bg-main); overflow: hidden; padding-top: 2rem;">
    <div class="container grid grid-cols-2 items-center gap-4">
        <div>
            <div style="display: inline-block; padding: 0.25rem 1rem; background: var(--bg-secondary); border-radius: var(--border-radius-full); font-size: 0.875rem; font-weight: 500; margin-bottom: 1.5rem;">
                Premium Collection 2024
            </div>
            <h1 style="font-size: 4rem; line-height: 1.1; margin-bottom: 1.5rem;">Stay Hydrated in<br>Style.</h1>
            <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 2rem; max-width: 400px;">
                Discover our elegant, premium tumblers designed for everyday life. Personalize yours with custom name engraving for a truly unique aesthetic.
            </p>
            <div class="flex gap-2 mb-4">
                <a href="toko.php" class="btn btn-primary" style="padding: 1rem 2rem;">Toko</a>
                <a href="#" class="btn btn-outline" style="padding: 1rem 2rem;" data-open-cart>Keranjang</a>
            </div>
            
            <div class="flex gap-4 mt-4 pt-4" style="border-top: 1px solid var(--border-color);">
                <div>
                    <h3 style="font-size: 1.5rem; margin-bottom: 0.25rem;">10k+</h3>
                    <p style="font-size: 0.875rem; color: var(--text-muted);">Happy Customers</p>
                </div>
                <div>
                    <h3 style="font-size: 1.5rem; margin-bottom: 0.25rem;">4.9/5</h3>
                    <p style="font-size: 0.875rem; color: var(--text-muted);">Average Rating</p>
                </div>
            </div>
        </div>
        <div style="position: relative; height: 600px; background: var(--primary-light); border-radius: 24px; overflow: hidden;">
            <img src="<?= $heroSrc ?>" alt="<?= htmlspecialchars($heroAlt) ?>" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="section-padding bg-white" style="border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
    <div class="container grid grid-cols-3 gap-4 text-center">
        <div class="flex flex-col items-center">
            <div class="btn-icon" style="width: 60px; height: 60px; background: var(--bg-secondary); color: var(--primary-color); margin-bottom: 1.5rem;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20"></path><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            </div>
            <h4 style="margin-bottom: 0.5rem;">Eco-Friendly</h4>
            <p class="text-muted" style="font-size: 0.95rem; max-width: 250px;">Made from sustainable materials to reduce single-use plastic waste.</p>
        </div>
        <div class="flex flex-col items-center">
            <div class="btn-icon" style="width: 60px; height: 60px; background: var(--bg-secondary); color: var(--primary-color); margin-bottom: 1.5rem;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
            </div>
            <h4 style="margin-bottom: 0.5rem;">Custom Name</h4>
            <p class="text-muted" style="font-size: 0.95rem; max-width: 250px;">Personalize your tumbler with elegant laser engraving of your name.</p>
        </div>
        <div class="flex flex-col items-center">
            <div class="btn-icon" style="width: 60px; height: 60px; background: var(--bg-secondary); color: var(--primary-color); margin-bottom: 1.5rem;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
            </div>
            <h4 style="margin-bottom: 0.5rem;">Premium Material</h4>
            <p class="text-muted" style="font-size: 0.95rem; max-width: 250px;">High-quality stainless steel keeps your drinks cold for 24h or hot for 12h.</p>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="section-padding bg-main">
    <div class="container">
        <div class="flex justify-between items-center mb-4">
            <h2>Featured Products</h2>
            <a href="toko.php" class="btn btn-outline" style="background: white;">View All</a>
        </div>
        
        <div class="grid grid-cols-4 gap-2">
            <?php foreach($featuredProducts as $product): ?>
                <div class="product-card">
                    <div class="product-image-wrap">
                        <?php if($product['tags']): ?>
                            <?php $tags = explode(',', $product['tags']); ?>
                            <span class="product-badge"><?= trim($tags[0]) ?></span>
                        <?php endif; ?>
                        <img src="assets/images/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                    </div>
                    <div class="product-info">
                        <h4 class="product-title"><?= htmlspecialchars($product['name']) ?></h4>
                        <div class="product-price"><?= formatRupiah($product['price']) ?></div>
                        <button class="btn btn-outline w-full" data-add-to-cart='<?= json_encode(["id" => $product["id"], "name" => $product["name"], "price" => $product["price"], "image" => $product["image"], "color" => $product["color"]]) ?>'>Add to Cart</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Personalize Section -->
<section style="background: var(--bg-secondary); display: flex;">
    <div style="flex: 1; padding: 6rem 4rem; display: flex; flex-direction: column; justify-content: center;">
        <h2 style="font-size: 2.5rem; margin-bottom: 1rem; max-width: 400px;">Personalize Your Daily Essential.</h2>
        <p style="color: var(--text-muted); margin-bottom: 2rem; max-width: 400px;">
            Make it truly yours or create the perfect gift. We offer high-precision laser engraving for any name or initials with an elegant typeface.
        </p>
        <div>
            <a href="toko.php?type=Custom" class="btn btn-primary" style="padding: 1rem 2rem;">Customize Now</a>
        </div>
    </div>
    <div style="flex: 1; background: var(--primary-light);">
        <img src="<?= $personalizeSrc ?>" alt="<?= htmlspecialchars($personalizeAlt) ?>" style="width: 100%; height: 100%; object-fit: cover;">
    </div>
</section>

<!-- Testimonials -->
<section class="section-padding bg-main">
    <div class="container">
        <h2 class="text-center" style="margin-bottom: 3rem;">Loved by Our Community</h2>
        
        <div class="grid grid-cols-3 gap-2">
            <?php foreach($reviews as $review): ?>
                <div style="background: white; padding: 2rem; border-radius: var(--border-radius-lg); border: 1px solid var(--border-color);">
                    <div style="color: var(--primary-color); margin-bottom: 1rem;">
                        <?= renderStars($review['rating']) ?>
                    </div>
                    <p style="font-style: italic; color: var(--text-main); margin-bottom: 1.5rem; font-size: 0.95rem;">
                        "<?= htmlspecialchars($review['text']) ?>"
                    </p>
                    <div class="flex items-center gap-1">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--bg-secondary); overflow: hidden;">
                            <!-- Placeholder avatar -->
                            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: var(--primary-light); color: var(--primary-color); font-weight: bold;">
                                <?= substr($review['name'], 0, 1) ?>
                            </div>
                        </div>
                        <div>
                            <h5 style="margin-bottom: 0; font-size: 0.9rem;"><?= htmlspecialchars($review['name']) ?></h5>
                            <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0;">Verified Buyer</p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>
