<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/xml-functions.php';

$bgImg = getDashboardImage('auth', 'login-bg');
$bgSrc = $bgImg ? (file_exists(__DIR__ . '/assets/uploads/' . $bgImg['filename']) ? 'assets/uploads/' . $bgImg['filename'] : 'assets/images/' . $bgImg['filename']) : 'assets/images/tumbler_cream.png';
$bgAlt = $bgImg ? $bgImg['alt_text'] : 'Login Background';

if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: /ShodweThumbler/admin/index.php');
    } else {
        header('Location: /ShodweThumbler/index.php');
    }
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Email dan password harus diisi.';
    } else {
        if (loginUser($email, $password)) {
            if (isAdmin()) {
                header('Location: /ShodweThumbler/admin/index.php');
            } else {
                header('Location: /ShodweThumbler/index.php');
            }
            exit;
        } else {
            $error = 'Email atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Shodwe Tumbler Hub</title>
    <link rel="stylesheet" href="/ShodweThumbler/assets/css/style.css">
</head>
<body>

<div class="auth-layout">
    <div class="auth-image">
        <img src="<?= $bgSrc ?>" alt="<?= htmlspecialchars($bgAlt) ?>" style="object-position: center;">
        <div style="position: absolute; bottom: 4rem; left: 4rem; color: white;">
            <h1 style="font-size: 3.5rem; margin-bottom: 0.5rem; line-height: 1;">Welcome<br><span style="color: var(--primary-color);">Back.</span></h1>
            <p style="font-size: 1.1rem; max-width: 400px; opacity: 0.9;">
                Continue your journey with our premium, eco-friendly custom tumblers. Stay hydrated in style.
            </p>
        </div>
    </div>
    
    <div class="auth-content">
        <div style="width: 100%; max-width: 450px; margin: 0 auto;">
            <a href="index.php" class="logo" style="margin-bottom: 3rem;">
                <div class="logo-icon">S</div>
                Shodwe.
            </a>
            
            <div style="text-align: center; margin-bottom: 2.5rem;">
                <h2 style="font-size: 2rem; margin-bottom: 0.5rem;">Login ke Akun Anda</h2>
                <p style="color: var(--text-muted); font-size: 0.95rem;">Silakan masukkan detail akun Anda untuk melanjutkan.</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label class="form-label" for="email">Email atau Username</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="johndoe@example.com" value="<?= htmlspecialchars($email) ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" class="form-control" placeholder="password123" required>
                        <button type="button" data-toggle-password="password" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-muted);">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </div>
                
                <div class="flex justify-between items-center mb-4">
                    <label class="flex items-center gap-1" style="font-size: 0.875rem; cursor: pointer;">
                        <input type="checkbox" checked style="accent-color: var(--primary-color);">
                        Remember me
                    </label>
                    <a href="#" style="font-size: 0.875rem; color: var(--primary-color);">Lupa Password?</a>
                </div>
                
                <button type="submit" class="btn btn-primary w-full" style="padding: 1rem; font-size: 1rem;">Login / Masuk</button>
            </form>
            
            <div style="margin: 2rem 0; position: relative; text-align: center;">
                <hr style="border: none; border-top: 1px solid var(--border-color);">
                <span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: var(--bg-main); padding: 0 1rem; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; letter-spacing: 0.05em;">ATAU LOGIN DENGAN</span>
            </div>
            
            <button class="btn btn-outline w-full flex items-center justify-center gap-1" style="padding: 0.875rem; background: white; border-color: var(--border-color); color: var(--text-main);">
                <svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/><path d="M1 1h22v22H1z" fill="none"/></svg>
                Login dengan Google
            </button>
            
            <p class="text-center mt-4" style="font-size: 0.875rem; color: var(--text-muted);">
                Belum punya akun? <a href="register.php" style="color: var(--primary-color); text-decoration: underline;">Daftar sekarang</a>
            </p>
        </div>
    </div>
</div>

<script src="/ShodweThumbler/assets/js/main.js"></script>
</body>
</html>
