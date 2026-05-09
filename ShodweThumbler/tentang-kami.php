<?php
require_once __DIR__ . '/includes/xml-functions.php';

// Load dynamic dashboard images for this page
function getPageImage($section, $default) {
    $img = getDashboardImage('tentang-kami', $section);
    if ($img) {
        $path = file_exists(__DIR__ . '/assets/uploads/' . $img['filename']) 
            ? 'assets/uploads/' . $img['filename'] 
            : 'assets/images/' . $img['filename'];
        return ['src' => $path, 'alt' => $img['alt_text'] ?: $default];
    }
    return ['src' => 'assets/images/' . $default . '.png', 'alt' => $default];
}

$imgHero = getPageImage('hero', 'tumbler_white');
$imgStory = getPageImage('story', 'tumbler_cream');
$imgGallery1 = getPageImage('gallery-1', 'tumbler_cream');
$imgGallery2 = getPageImage('gallery-2', 'tumbler_mocha');
$imgGallery3 = getPageImage('gallery-3', 'tumbler_green');
$imgGallery4 = getPageImage('gallery-4', 'tumbler_rosegold');
?>

<?php include 'components/header.php'; ?>

<!-- Hero -->
<section style="background-color: var(--bg-main); padding: 4rem 0;">
    <div class="container text-center">
        <h1 style="font-size: 3.5rem; margin-bottom: 1rem; line-height: 1.2;">Menciptakan Keanggunan<br>dalam Setiap Tetes.</h1>
        <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto 3rem; font-size: 1.1rem;">
            Shodwe Tumbler Hub lahir dari ide sederhana: hidrasi tidak seharusnya mengorbankan gaya Anda. Kami meningkatkan rutinitas harian Anda dengan peralatan minum premium dan estetis.
        </p>
        
        <div style="width: 100%; height: 400px; border-radius: var(--border-radius-lg); overflow: hidden; background: var(--bg-secondary);">
            <img src="<?= $imgHero['src'] ?>" alt="<?= htmlspecialchars($imgHero['alt']) ?>" style="width: 100%; height: 100%; object-fit: cover; object-position: center 30%;">
        </div>
    </div>
</section>

<!-- Story Section -->
<section class="section-padding bg-white">
    <div class="container grid grid-cols-2 gap-4 items-center">
        <div>
            <h2 style="font-size: 2.5rem; margin-bottom: 1.5rem;">Kisah kami</h2>
            <div style="color: var(--text-main); font-size: 1rem; line-height: 1.8;">
                <p style="margin-bottom: 1rem;">Kami melihat adanya celah di pasar. Meskipun ada banyak gelas minum yang tersedia, menemukan gelas yang benar-benar sesuai dengan gaya hidup estetis yang terkurasi merupakan tantangan. Sebagian besar fungsional, namun menampilkan warna-warna mencolok, desain yang besar, dan material yang kurang menarik.</p>
                
                <p style="margin-bottom: 1rem;">Shodwe diciptakan untuk menjembatani kesenjangan tersebut. Kami menggabungkan bahan-bahan premium yang mampu mempertahankan suhu dengan desain minimalis yang elegan dan palet warna lembut yang natural. Kami percaya bahwa kebutuhan sehari-hari Anda harus menjadi perpanjangan yang tak terpisahkan dari gaya pribadi Anda.</p>
                
                <p>Hari ini, kami bangga menawarkan peralatan minum yang tidak hanya menjaga kopi Anda tetap panas atau air Anda tetap dingin tetapi juga melengkapi pakaian Anda, ruang kerja Anda, dan hidup Anda.</p>
            </div>
        </div>
        <div style="height: 500px; border-radius: var(--border-radius-lg); overflow: hidden; background: var(--bg-secondary);">
            <img src="<?= $imgStory['src'] ?>" alt="<?= htmlspecialchars($imgStory['alt']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
    </div>
</section>

<!-- Vision Mission -->
<section class="section-padding" style="background: var(--bg-main);">
    <div class="container grid grid-cols-2 gap-4">
        <div style="background: var(--bg-secondary); padding: 4rem 3rem; border-radius: var(--border-radius-lg); text-align: center;">
            <div class="btn-icon" style="width: 64px; height: 64px; background: white; margin: 0 auto 1.5rem; color: var(--primary-color);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            </div>
            <h3 style="font-size: 1.8rem; margin-bottom: 1rem;">Visi</h3>
            <p style="color: var(--text-muted); font-size: 1rem;">Menjadi merek global terkemuka untuk perlengkapan minum premium dan estetis yang memberdayakan individu untuk menjalani gaya hidup berkelanjutan tanpa mengorbankan keanggunan, kemewahan, dan ekspresi pribadi.</p>
        </div>
        
        <div style="background: var(--bg-secondary); padding: 4rem 3rem; border-radius: var(--border-radius-lg); text-align: center;">
            <div class="btn-icon" style="width: 64px; height: 64px; background: white; margin: 0 auto 1.5rem; color: var(--primary-color);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle></svg>
            </div>
            <h3 style="font-size: 1.8rem; margin-bottom: 1rem;">Misi</h3>
            <p style="color: var(--text-muted); font-size: 1rem;">Menghadirkan gelas minum berkualitas tinggi dan ramah lingkungan dengan desain minimalis yang dapat disesuaikan. Kami berupaya memberikan pengalaman pelanggan yang luar biasa dan membina komunitas yang sangat menghargai estetika dan keberlanjutan.</p>
        </div>
    </div>
</section>

<!-- Gallery -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="text-center mb-4">
            <h2 style="font-size: 2.5rem; margin-bottom: 0.5rem;">Estetika Shodwe</h2>
            <p style="color: var(--text-muted);">Dirancang untuk meningkatkan momen-momen harian Anda.</p>
        </div>
        
        <div class="grid" style="grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1rem; height: 400px;">
            <div style="border-radius: var(--border-radius-lg); overflow: hidden; background: var(--bg-secondary);">
                <img src="<?= $imgGallery1['src'] ?>" alt="<?= htmlspecialchars($imgGallery1['alt']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div style="border-radius: var(--border-radius-lg); overflow: hidden; background: var(--bg-secondary);">
                <img src="<?= $imgGallery2['src'] ?>" alt="<?= htmlspecialchars($imgGallery2['alt']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
        </div>
        
        <div class="grid" style="grid-template-columns: 1fr 2fr; gap: 1rem; height: 300px;">
            <div style="border-radius: var(--border-radius-lg); overflow: hidden; background: var(--bg-secondary);">
                <img src="<?= $imgGallery3['src'] ?>" alt="<?= htmlspecialchars($imgGallery3['alt']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div style="border-radius: var(--border-radius-lg); overflow: hidden; background: var(--bg-secondary);">
                <img src="<?= $imgGallery4['src'] ?>" alt="<?= htmlspecialchars($imgGallery4['alt']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>
