<?php

require_once 'includes/auth.php'; 
require_once 'config/koneksi.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header("Location: dashboard.php?status=gagal&pesan=" . urlencode("ID barang tidak valid.")); exit; }


$kategoriList = $pdo->query("SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori")->fetchAll();
$lokasiList   = $pdo->query("SELECT id_lokasi, nama_lokasi, penanggung_jawab FROM lokasi ORDER BY nama_lokasi")->fetchAll();


$stmt = $pdo->prepare("SELECT * FROM barang WHERE id = ?");
$stmt->execute([$id]);
$d = $stmt->fetch();
if (!$d) { header("Location: dashboard.php?status=gagal&pesan=" . urlencode("Data barang tidak ditemukan.")); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = ['kode_barang', 'nama_barang', 'id_kategori', 'id_lokasi', 'stok', 'harga', 'tanggal_masuk'];
    foreach ($fields as $f) $d[$f] = trim($_POST[$f] ?? '');

    if (in_array('', array_intersect_key($d, array_flip($fields)), true)) {
        $error = 'Seluruh kolom formulir wajib diisi!';
    } elseif (!is_numeric($d['stok']) || (int)$d['stok'] < 0 || !is_numeric($d['harga']) || (int)$d['harga'] < 0) {
        $error = 'Stok dan harga harus berupa angka non-negatif.';
    } else {
       
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM barang WHERE kode_barang = ? AND id != ?");
        $stmt->execute([$d['kode_barang'], $id]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Kode barang '{$d['kode_barang']}' sudah dipakai barang lain.";
        } else {
            $sql = "UPDATE barang SET kode_barang=?, nama_barang=?, id_kategori=?, id_lokasi=?, stok=?, harga=?, tanggal_masuk=? WHERE id=?";
            $pdo->prepare($sql)->execute([$d['kode_barang'], $d['nama_barang'], (int)$d['id_kategori'], (int)$d['id_lokasi'], (int)$d['stok'], (int)$d['harga'], $d['tanggal_masuk'], $id]);
            header("Location: dashboard.php?status=sukses_edit");
            exit;
        }
    }
}

$page_title = 'Edit Barang - Sistem Inventaris';
$max_width  = '680px';
$btn_label  = '💾 Simpan Perubahan';
require 'includes/header.php';
?>
<header class="app-header">
    <div>
        <h1>Edit Data Barang</h1>
        <p>Perbarui rincian informasi dan relasi master barang ID #<?= $id ?></p>
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
    <form action="edit.php?id=<?= $id ?>" method="POST">
        <?php require 'includes/form_barang.php'; ?>
    </form>
</div></div>
<?php require 'includes/footer.php'; ?>
