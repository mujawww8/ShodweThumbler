<?php
require_once __DIR__ . '/includes/xml-functions.php';
require_once __DIR__ . '/includes/helpers.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => $_POST['name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'subject' => $_POST['subject'] ?? '',
        'message' => $_POST['message'] ?? ''
    ];
    
    // Basic validation
    if (empty($data['name']) || empty($data['email']) || empty($data['subject']) || empty($data['message'])) {
        $error = 'Semua field harus diisi.';
    } elseif (!validateEmail($data['email'])) {
        $error = 'Format email tidak valid.';
    } else {
        if (addContact($data)) {
            $success = true;
        } else {
            $error = 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.';
        }
    }
}
?>

<?php include 'components/header.php'; ?>

<!-- Leaflet.js CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<!-- Header -->
<section style="background-color: var(--bg-main); padding: 4rem 0 2rem;">
    <div class="container text-center">
        <h1 style="font-size: 3.5rem; margin-bottom: 1rem;">Hubungi Kami</h1>
        <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto; font-size: 1.1rem;">
            Kami siap membantu Anda kapan saja. Kirimkan pesan melalui form di bawah ini atau kunjungi toko offline kami.
        </p>
    </div>
</section>

<!-- Contact Form & Info -->
<section class="section-padding bg-main">
    <div class="container flex gap-4">
        
        <!-- Contact Info -->
        <div style="flex: 1; background: white; padding: 3rem; border-radius: var(--border-radius-lg); border: 1px solid var(--border-color);">
            <h2 style="font-size: 1.8rem; margin-bottom: 1rem;">Informasi Kontak</h2>
            <p style="color: var(--text-muted); margin-bottom: 2.5rem; font-size: 0.95rem;">
                Punya pertanyaan seputar custom order atau pesanan Anda? Jangan ragu untuk menghubungi tim kami.
            </p>
            
            <div class="flex flex-col gap-3">
                <div class="flex items-start gap-2">
                    <div class="btn-icon" style="flex-shrink: 0; background: var(--bg-secondary); color: var(--primary-color);">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    </div>
                    <div>
                        <h4 style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.25rem;">Alamat Toko</h4>
                        <p style="font-weight: 500; font-size: 1rem; margin: 0;">Jl. Senopati No. 45, Kebayoran Baru,<br>Jakarta Selatan</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-2">
                    <div class="btn-icon" style="flex-shrink: 0; background: var(--bg-secondary); color: var(--primary-color);">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    </div>
                    <div>
                        <h4 style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.25rem;">Telepon / WhatsApp</h4>
                        <p style="font-weight: 500; font-size: 1rem; margin: 0;">+62 811 2345 6789</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-2">
                    <div class="btn-icon" style="flex-shrink: 0; background: var(--bg-secondary); color: var(--primary-color);">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    </div>
                    <div>
                        <h4 style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.25rem;">Email Customer Service</h4>
                        <p style="font-weight: 500; font-size: 1rem; margin: 0;">hello@shodwetumbler.com</p>
                    </div>
                </div>
                
                <div class="flex items-start gap-2">
                    <div class="btn-icon" style="flex-shrink: 0; background: var(--bg-secondary); color: var(--primary-color);">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <div>
                        <h4 style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.25rem;">Jam Operasional</h4>
                        <p style="font-weight: 500; font-size: 1rem; margin: 0;">Senin - Sabtu: 09:00 - 20:00 WIB</p>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-1 mt-4 pt-4" style="border-top: 1px solid var(--border-color);">
                <a href="#" class="btn-icon" style="background: white; border: 1px solid var(--border-color);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                </a>
                <a href="#" class="btn-icon" style="background: white; border: 1px solid var(--border-color);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </a>
                <a href="#" class="btn-icon" style="background: white; border: 1px solid var(--border-color);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"></path></svg>
                </a>
            </div>
            
            <div style="margin-top: 2rem; height: 250px; border-radius: var(--border-radius-md); overflow: hidden; position: relative;" id="store-map"></div>
        </div>
        
        <!-- Contact Form -->
        <div style="flex: 1; background: white; padding: 3rem; border-radius: var(--border-radius-lg); border: 1px solid var(--border-color);">
            <h2 style="font-size: 1.8rem; margin-bottom: 1rem;">Kirim Pesan</h2>
            <p style="color: var(--text-muted); margin-bottom: 2.5rem; font-size: 0.95rem;">
                Isi form di bawah ini dan kami akan membalas pesan Anda selambatnya 1x24 jam.
            </p>
            
            <?php if ($success): ?>
                <div class="alert alert-success">Pesan Anda berhasil dikirim. Kami akan segera menghubungi Anda.</div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <form action="kontak.php" method="POST">
                <div class="grid grid-cols-2 gap-2">
                    <div class="form-group">
                        <label class="form-label" for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Nama Anda" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="nama@email.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="subject">Subjek</label>
                    <input type="text" id="subject" name="subject" class="form-control" placeholder="Subjek pesan" required value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="message">Pesan</label>
                    <textarea id="message" name="message" class="form-control" rows="5" placeholder="Tulis pesan Anda di sini..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary w-full mt-2 flex items-center justify-center gap-1" style="padding: 1rem;">
                    Kirim Pesan
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                </button>
            </form>
        </div>
        
    </div>
</section>

<!-- Floating chat button matching design -->
<div style="position: fixed; bottom: 2rem; right: 2rem; z-index: 50;">
    <button data-chat-btn style="width: 60px; height: 60px; border-radius: 50%; background: var(--primary-color); border: none; color: white; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-lg); cursor: pointer; transition: transform 0.3s;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
    </button>
</div>

<?php include 'components/footer.php'; ?>

<!-- Leaflet.js -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mapEl = document.getElementById('store-map');
    if (!mapEl) return;
    
    const map = L.map('store-map').setView([-6.2405, 106.7982], 16);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19
    }).addTo(map);
    
    const marker = L.marker([-6.2405, 106.7982]).addTo(map);
    marker.bindPopup(`
        <div style="text-align:center;padding:0.5rem;">
            <strong style="font-size:1rem;">Shodwe Tumbler Hub</strong><br>
            <span style="font-size:0.85rem;color:#666;">Jl. Senopati No. 45, Kebayoran Baru</span><br>
            <span style="font-size:0.85rem;color:#666;">Jakarta Selatan</span><br>
            <span style="font-size:0.8rem;color:#C4A265;">Senin - Sabtu: 09:00 - 20:00 WIB</span>
        </div>
    `).openPopup();
});
</script>
