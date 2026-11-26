<?php
session_start();


if (!empty($_SESSION['logged_in'])) {
    header("Location: dashboard.php");
    exit;
}

$status = $_GET['status'] ?? '';
$notif = [
    'gagal'       => ['danger', 'Username atau password salah.'],
    'kosong'      => ['danger', 'Username dan password wajib diisi.'],
    'harus_login' => ['danger', 'Silakan login terlebih dahulu untuk mengakses aplikasi.'],
    'logout'      => ['success', 'Anda telah berhasil logout dari sistem.'],
];

$page_title = 'Login - Sistem Inventaris Barang';
$max_width  = '420px';
require 'includes/header.php';
?>
<div style="margin-top: 48px;">
    <header class="app-header" style="justify-content: center; text-align: center;">
        <div>
            <h1 style="justify-content: center;">🔐 Login Inventaris</h1>
            <p>Masuk untuk mengelola data inventaris barang</p>
        </div>
    </header>

    <?php if (isset($notif[$status])): ?>
        <div class="alert alert-<?= $notif[$status][0] ?>">
            <span><?= $notif[$status][0] === 'success' ? '✅' : '❌' ?> <?= $notif[$status][1] ?></span>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="proses_login.php" method="POST">
                <div class="form-group">
                    <label for="username">Username <span class="required">*</span></label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px;">Masuk ke Dashboard</button>
            </form>
        </div>
    </div>
</div>
<?php require 'includes/footer.php'; ?>
