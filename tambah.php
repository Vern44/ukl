<?php

require_once 'includes/auth.php';
require_once 'config/koneksi.php';


$kategoriList = $pdo->query("SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori")->fetchAll();
$lokasiList   = $pdo->query("SELECT id_lokasi, nama_lokasi, penanggung_jawab FROM lokasi ORDER BY nama_lokasi")->fetchAll();

$error = '';
$d = ['kode_barang' => '', 'nama_barang' => '', 'id_kategori' => '', 'id_lokasi' => '', 'stok' => '', 'harga' => '', 'tanggal_masuk' => date('Y-m-d')];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($d as $k => $v) $d[$k] = trim($_POST[$k] ?? '');

    if (in_array('', $d, true)) {
        $error = 'Seluruh kolom formulir wajib diisi!';
    } elseif (!is_numeric($d['stok']) || (int)$d['stok'] < 0 || !is_numeric($d['harga']) || (int)$d['harga'] < 0) {
        $error = 'Stok dan harga harus berupa angka non-negatif.';
    } else {
       
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM barang WHERE kode_barang = ?");
        $stmt->execute([$d['kode_barang']]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Kode barang '{$d['kode_barang']}' sudah terdaftar, gunakan kode lain.";
        } else {
            $sql = "INSERT INTO barang (kode_barang, nama_barang, id_kategori, id_lokasi, stok, harga, tanggal_masuk) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([$d['kode_barang'], $d['nama_barang'], (int)$d['id_kategori'], (int)$d['id_lokasi'], (int)$d['stok'], (int)$d['harga'], $d['tanggal_masuk']]);
            header("Location: dashboard.php?status=sukses_tambah");
            exit;
        }
    }
}

$page_title = 'Tambah Barang - Sistem Inventaris';
$max_width  = '680px';
$btn_label  = '💾 Simpan Data Barang';
require 'includes/header.php';
?>
<header class="app-header">
    <div>
        <h1>Tambah Barang Baru</h1>
        <p>Masukkan data barang dengan memilih referensi kategori & lokasi yang sesuai</p>
    </div>
    <div class="action-buttons" style="align-items: center;">
        <span class="badge">👤 <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="dashboard.php" class="btn btn-secondary btn-sm">← Kembali</a>
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
</header>

<?php if ($error): ?>
    <div class="alert alert-danger"><span>❌ <strong>Perhatian:</strong> <?= htmlspecialchars($error) ?></span></div>
<?php endif; ?>

<div class="card"><div class="card-body">
    <form action="tambah.php" method="POST">
        <?php require 'includes/form_barang.php'; ?>
    </form>
</div></div>
<?php require 'includes/footer.php'; ?>
