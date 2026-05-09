<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/xml-functions.php';
requireAdmin();

$success = '';
$error = '';
$editImage = null;

// Create uploads directory if it doesn't exist
$uploadDir = __DIR__ . '/../assets/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $title = $_POST['title'] ?? '';
        $section = $_POST['section'] ?? '';
        $page = $_POST['page'] ?? '';
        $alt_text = $_POST['alt_text'] ?? '';
        $description = $_POST['description'] ?? '';
        
        if (empty($title) || empty($section) || empty($page)) {
            $error = 'Judul, section, dan halaman wajib diisi.';
        } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $error = 'Silakan pilih file gambar.';
        } else {
            $file = $_FILES['image'];
            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (!in_array($file['type'], $allowed)) {
                $error = 'Format file tidak didukung. Gunakan JPG, PNG, WebP, atau GIF.';
            } elseif ($file['size'] > 5 * 1024 * 1024) {
                $error = 'Ukuran file maksimal 5MB.';
            } else {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'dashboard_' . time() . '_' . rand(100,999) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                    $result = addDashboardImage([
                        'title' => $title,
                        'section' => $section,
                        'page' => $page,
                        'filename' => $filename,
                        'alt_text' => $alt_text,
                        'description' => $description,
                        'status' => 'Aktif'
                    ]);
                    if ($result) {
                        $success = 'Gambar berhasil ditambahkan!';
                    } else {
                        $error = 'Gagal menyimpan data gambar.';
                    }
                } else {
                    $error = 'Gagal mengupload file.';
                }
            }
        }
    }
    
    if ($action === 'update') {
        $id = $_POST['id'] ?? '';
        $title = $_POST['title'] ?? '';
        $alt_text = $_POST['alt_text'] ?? '';
        $description = $_POST['description'] ?? '';
        $status = $_POST['status'] ?? 'Aktif';
        $section = $_POST['section'] ?? '';
        $page = $_POST['page'] ?? '';
        
        $data = [
            'title' => $title,
            'alt_text' => $alt_text,
            'description' => $description,
            'status' => $status,
            'section' => $section,
            'page' => $page
        ];
        
        // Handle new image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (!in_array($file['type'], $allowed)) {
                $error = 'Format file tidak didukung.';
            } elseif ($file['size'] > 5 * 1024 * 1024) {
                $error = 'Ukuran file maksimal 5MB.';
            } else {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'dashboard_' . time() . '_' . rand(100,999) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                    // Delete old uploaded file (not default ones)
                    $oldImage = getDashboardImageById($id);
                    if ($oldImage && file_exists($uploadDir . $oldImage['filename'])) {
                        unlink($uploadDir . $oldImage['filename']);
                    }
                    $data['filename'] = $filename;
                }
            }
        }
        
        if (empty($error)) {
            if (updateDashboardImage($id, $data)) {
                $success = 'Gambar berhasil diperbarui!';
            } else {
                $error = 'Gagal memperbarui gambar.';
            }
        }
    }
    
    if ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        if (deleteDashboardImage($id)) {
            $success = 'Gambar berhasil dihapus!';
        } else {
            $error = 'Gagal menghapus gambar.';
        }
    }
}

// Check if editing
if (isset($_GET['edit'])) {
    $editImage = getDashboardImageById($_GET['edit']);
}

$images = getAllDashboardImages();
$pageFilter = $_GET['page_filter'] ?? '';
if ($pageFilter) {
    $images = array_values(array_filter($images, function($img) use ($pageFilter) {
        return $img['page'] === $pageFilter;
    }));
}
?>

<?php include '../components/admin-header.php'; ?>

<script>
    document.querySelector('.admin-header > div').innerHTML = '<h1 style="font-size: 1.8rem; margin-bottom: 0;">Kelola Gambar Dashboard</h1><p style="color: var(--text-muted); font-size: 0.9rem; margin: 0;">Upload, edit, dan kelola gambar yang tampil di halaman customer.</p>';
</script>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Filter & Add Button -->
<div class="flex justify-between items-center mb-4">
    <div class="flex gap-2">
        <a href="gambar-dashboard.php" class="btn <?= $pageFilter === '' ? 'btn-primary' : 'btn-outline' ?>" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Semua</a>
        <a href="gambar-dashboard.php?page_filter=beranda" class="btn <?= $pageFilter === 'beranda' ? 'btn-primary' : 'btn-outline' ?>" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Beranda</a>
        <a href="gambar-dashboard.php?page_filter=tentang-kami" class="btn <?= $pageFilter === 'tentang-kami' ? 'btn-primary' : 'btn-outline' ?>" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Tentang Kami</a>
        <a href="gambar-dashboard.php?page_filter=toko" class="btn <?= $pageFilter === 'toko' ? 'btn-primary' : 'btn-outline' ?>" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Toko</a>
        <a href="gambar-dashboard.php?page_filter=auth" class="btn <?= $pageFilter === 'auth' ? 'btn-primary' : 'btn-outline' ?>" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Auth (Login/Register)</a>
    </div>
    <button onclick="document.getElementById('addModal').classList.add('active')" class="btn btn-primary" style="padding: 0.6rem 1.25rem;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:0.5rem;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Tambah Gambar
    </button>
</div>

<!-- Image Grid -->
<div class="grid grid-cols-3 gap-2">
    <?php foreach($images as $img): 
        $imgPath = file_exists(__DIR__ . '/../assets/uploads/' . $img['filename']) 
            ? '/ShodweThumbler/assets/uploads/' . $img['filename'] 
            : '/ShodweThumbler/assets/images/' . $img['filename'];
    ?>
    <div class="settings-card" style="padding: 0; overflow: hidden; margin-bottom: 0;">
        <div style="height: 200px; background: var(--bg-secondary); overflow: hidden; position: relative;">
            <img src="<?= $imgPath ?>" alt="<?= htmlspecialchars($img['alt_text']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
            <span class="badge <?= $img['status'] === 'Aktif' ? 'status-active' : 'status-inactive' ?>" style="position: absolute; top: 0.75rem; right: 0.75rem;">
                <?= htmlspecialchars($img['status']) ?>
            </span>
        </div>
        <div style="padding: 1.25rem;">
            <h4 style="font-size: 1rem; margin-bottom: 0.25rem;"><?= htmlspecialchars($img['title']) ?></h4>
            <div class="flex gap-1 mb-2" style="flex-wrap: wrap;">
                <span class="badge" style="background: var(--primary-light); color: var(--primary-color);"><?= htmlspecialchars($img['page']) ?></span>
                <span class="badge" style="background: var(--status-info-bg); color: var(--status-info);"><?= htmlspecialchars($img['section']) ?></span>
            </div>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem; line-height: 1.4;"><?= htmlspecialchars($img['description']) ?></p>
            <div style="font-size: 0.75rem; color: var(--text-light); margin-bottom: 1rem;">
                Diperbarui: <?= htmlspecialchars($img['updated_at']) ?>
            </div>
            <div class="flex gap-1">
                <a href="gambar-dashboard.php?edit=<?= $img['id'] ?>" class="btn btn-outline" style="flex:1; padding: 0.5rem; font-size: 0.8rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:0.35rem;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    Edit
                </a>
                <form method="POST" style="flex:1;" onsubmit="return confirm('Yakin ingin menghapus gambar ini?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $img['id'] ?>">
                    <button type="submit" class="btn" style="width:100%; padding: 0.5rem; font-size: 0.8rem; background: var(--status-danger-bg); color: var(--status-danger); border: 1px solid var(--status-danger);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:0.35rem;"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if (empty($images)): ?>
<div style="text-align: center; padding: 4rem 2rem; color: var(--text-muted);">
    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto 1rem; opacity: 0.3;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
    <p>Belum ada gambar. Klik "Tambah Gambar" untuk memulai.</p>
</div>
<?php endif; ?>

<!-- ADD MODAL -->
<div class="modal-overlay" id="addModal">
    <div class="modal-content" style="max-width: 600px;">
        <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('active')">&times;</button>
        <div style="padding: 2rem;">
            <h3 style="margin-bottom: 1.5rem;">Tambah Gambar Baru</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label class="form-label">Judul Gambar *</label>
                    <input type="text" name="title" class="form-control" required placeholder="Contoh: Hero Banner Utama">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div class="form-group">
                        <label class="form-label">Halaman *</label>
                        <select name="page" class="form-control" required>
                            <option value="">Pilih Halaman</option>
                            <option value="beranda">Beranda</option>
                            <option value="tentang-kami">Tentang Kami</option>
                            <option value="toko">Toko</option>
                            <option value="auth">Auth (Login/Register)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Section *</label>
                        <input type="text" name="section" class="form-control" required placeholder="Contoh: hero, gallery-1">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">File Gambar * (max 5MB)</label>
                    <input type="file" name="image" class="form-control" accept="image/*" required style="padding: 0.5rem;">
                </div>
                <div class="form-group">
                    <label class="form-label">Alt Text</label>
                    <input type="text" name="alt_text" class="form-control" placeholder="Deskripsi singkat gambar">
                </div>
                <div class="form-group">
                    <label class="form-label">Keterangan</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Deskripsi lokasi/fungsi gambar"></textarea>
                </div>
                <div class="flex gap-2 justify-between" style="margin-top: 1.5rem;">
                    <button type="button" class="btn btn-outline" onclick="this.closest('.modal-overlay').classList.remove('active')">Batal</button>
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">Upload Gambar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<?php if ($editImage): 
    $editImgPath = file_exists(__DIR__ . '/../assets/uploads/' . $editImage['filename']) 
        ? '/ShodweThumbler/assets/uploads/' . $editImage['filename'] 
        : '/ShodweThumbler/assets/images/' . $editImage['filename'];
?>
<div class="modal-overlay active" id="editModal">
    <div class="modal-content" style="max-width: 650px;">
        <button class="modal-close" onclick="window.location.href='gambar-dashboard.php'">&times;</button>
        <div style="padding: 2rem;">
            <h3 style="margin-bottom: 1.5rem;">Edit Gambar</h3>
            <div style="margin-bottom: 1.5rem; border-radius: var(--border-radius-md); overflow: hidden; height: 180px; background: var(--bg-secondary);">
                <img src="<?= $editImgPath ?>" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= $editImage['id'] ?>">
                <div class="form-group">
                    <label class="form-label">Judul Gambar</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($editImage['title']) ?>" required>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div class="form-group">
                        <label class="form-label">Halaman</label>
                        <select name="page" class="form-control" required>
                            <option value="beranda" <?= $editImage['page'] === 'beranda' ? 'selected' : '' ?>>Beranda</option>
                            <option value="tentang-kami" <?= $editImage['page'] === 'tentang-kami' ? 'selected' : '' ?>>Tentang Kami</option>
                            <option value="toko" <?= $editImage['page'] === 'toko' ? 'selected' : '' ?>>Toko</option>
                            <option value="auth" <?= $editImage['page'] === 'auth' ? 'selected' : '' ?>>Auth (Login/Register)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Section</label>
                        <input type="text" name="section" class="form-control" value="<?= htmlspecialchars($editImage['section']) ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Ganti Gambar (opsional, max 5MB)</label>
                    <input type="file" name="image" class="form-control" accept="image/*" style="padding: 0.5rem;">
                    <small style="color: var(--text-muted);">File saat ini: <?= htmlspecialchars($editImage['filename']) ?></small>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div class="form-group">
                        <label class="form-label">Alt Text</label>
                        <input type="text" name="alt_text" class="form-control" value="<?= htmlspecialchars($editImage['alt_text']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="Aktif" <?= $editImage['status'] === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="Nonaktif" <?= $editImage['status'] === 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Keterangan</label>
                    <textarea name="description" class="form-control" rows="2"><?= htmlspecialchars($editImage['description']) ?></textarea>
                </div>
                <div class="flex gap-2 justify-between" style="margin-top: 1.5rem;">
                    <a href="gambar-dashboard.php" class="btn btn-outline">Batal</a>
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="/ShodweThumbler/assets/js/main.js"></script>
</main>
</div>
</body>
</html>
