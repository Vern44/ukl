<?php
/**
 * File: edit.php
 * Deskripsi: Mengambil data barang dan data master relasi (kategori & lokasi) berdasarkan ID,
 *            menampilkan dropdown terpilih (selected) secara otomatis, dan memproses update
 *            menggunakan PDO Prepared Statements.
 */

require_once 'config/koneksi.php';

// Validasi parameter ID dari URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: index.php?status=gagal&pesan=" . urlencode("ID barang tidak valid atau tidak disertakan."));
    exit;
}

$error = '';

// Ambil data referensi dari tabel master kategori & lokasi untuk dropdown dinamis
try {
    $kategoriList = $pdo->query("SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori ASC")->fetchAll();
    $lokasiList   = $pdo->query("SELECT id_lokasi, nama_lokasi, penanggung_jawab FROM lokasi ORDER BY nama_lokasi ASC")->fetchAll();

    // Ambil data barang yang akan diedit dari database
    $stmt = $pdo->prepare("SELECT * FROM barang WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $barang = $stmt->fetch();

    if (!$barang) {
        header("Location: index.php?status=gagal&pesan=" . urlencode("Data barang dengan ID tersebut tidak ditemukan."));
        exit;
    }
} catch (PDOException $e) {
    die("Gagal mengambil data dari database: " . $e->getMessage());
}

// Proses form saat formulir disubmit (metode POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_barang   = trim($_POST['kode_barang'] ?? '');
    $nama_barang   = trim($_POST['nama_barang'] ?? '');
    $id_kategori   = filter_input(INPUT_POST, 'id_kategori', FILTER_VALIDATE_INT);
    $id_lokasi     = filter_input(INPUT_POST, 'id_lokasi', FILTER_VALIDATE_INT);
    $stok          = trim($_POST['stok'] ?? '');
    $harga         = trim($_POST['harga'] ?? '');
    $tanggal_masuk = trim($_POST['tanggal_masuk'] ?? '');

    // Validasi input di sisi server
    if (empty($kode_barang) || empty($nama_barang) || !$id_kategori || !$id_lokasi || $stok === '' || $harga === '' || empty($tanggal_masuk)) {
        $error = 'Seluruh kolom formulir wajib diisi dengan benar!';
    } elseif (!is_numeric($stok) || (int)$stok < 0) {
        $error = 'Jumlah stok harus berupa angka positif atau nol.';
    } elseif (!is_numeric($harga) || (int)$harga < 0) {
        $error = 'Harga barang harus berupa angka positif atau nol.';
    } else {
        try {
            // Periksa apakah kode barang sudah digunakan oleh data barang LAIN
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM barang WHERE kode_barang = :kode_barang AND id != :id");
            $checkStmt->execute([
                ':kode_barang' => $kode_barang,
                ':id'          => $id,
            ]);

            if ($checkStmt->fetchColumn() > 0) {
                $error = "Kode barang '{$kode_barang}' sudah dipakai oleh barang lain, mohon gunakan kode berbeda.";
            } else {
                // Update data barang dengan relasi foreign key menggunakan PDO Prepared Statements
                $sql = "UPDATE barang 
                        SET kode_barang = :kode_barang, 
                            nama_barang = :nama_barang, 
                            id_kategori = :id_kategori, 
                            id_lokasi = :id_lokasi, 
                            stok = :stok, 
                            harga = :harga, 
                            tanggal_masuk = :tanggal_masuk 
                        WHERE id = :id";
                $updateStmt = $pdo->prepare($sql);
                $updateStmt->execute([
                    ':kode_barang'   => $kode_barang,
                    ':nama_barang'   => $nama_barang,
                    ':id_kategori'   => $id_kategori,
                    ':id_lokasi'     => $id_lokasi,
                    ':stok'          => (int)$stok,
                    ':harga'         => (int)$harga,
                    ':tanggal_masuk' => $tanggal_masuk,
                    ':id'            => $id,
                ]);

                // Redirect kembali ke index dengan notifikasi sukses
                header("Location: index.php?status=sukses_edit");
                exit;
            }
        } catch (PDOException $e) {
            $error = 'Gagal memperbarui data: ' . $e->getMessage();
        }
    }
} else {
    // Isi variabel dengan data lama dari database untuk pertama kali ditampilkan di form
    $kode_barang   = $barang['kode_barang'];
    $nama_barang   = $barang['nama_barang'];
    $id_kategori   = $barang['id_kategori'];
    $id_lokasi     = $barang['id_lokasi'];
    $stok          = $barang['stok'];
    $harga         = $barang['harga'];
    $tanggal_masuk = $barang['tanggal_masuk'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang - Sistem Inventaris</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container" style="max-width: 680px;">
        <!-- Header Formulir -->
        <header class="app-header">
            <div>
                <h1>Edit Data Barang</h1>
                <p>Perbarui rincian informasi dan relasi master barang ID #<?= htmlspecialchars((string)$id) ?></p>
            </div>
            <div>
                <a href="index.php" class="btn btn-secondary">← Kembali</a>
            </div>
        </header>

        <!-- Pesan Error Validasi -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <span>❌ <strong>Perhatian:</strong> <?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- Form Edit Data Berelasi -->
        <div class="card">
            <div class="card-body">
                <form action="edit.php?id=<?= $id ?>" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="kode_barang">Kode Barang <span class="required">*</span></label>
                            <input type="text" id="kode_barang" name="kode_barang" class="form-control" 
                                   value="<?= htmlspecialchars($kode_barang) ?>" 
                                   required maxlength="20">
                        </div>
                        <div class="form-group">
                            <label for="nama_barang">Nama Barang <span class="required">*</span></label>
                            <input type="text" id="nama_barang" name="nama_barang" class="form-control" 
                                   value="<?= htmlspecialchars($nama_barang) ?>" 
                                   required maxlength="100">
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- Dropdown Relasi Kategori dengan Opsi Otomatis Terpilih (Selected) -->
                        <div class="form-group">
                            <label for="id_kategori">Kategori Barang <span class="required">*</span></label>
                            <select id="id_kategori" name="id_kategori" class="form-control" required>
                                <option value="" disabled>-- Pilih Kategori --</option>
                                <?php foreach ($kategoriList as $kat): ?>
                                    <option value="<?= $kat['id_kategori'] ?>" <?= ($id_kategori == $kat['id_kategori']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($kat['nama_kategori']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Dropdown Relasi Lokasi dengan Opsi Otomatis Terpilih (Selected) -->
                        <div class="form-group">
                            <label for="id_lokasi">Lokasi Penempatan <span class="required">*</span></label>
                            <select id="id_lokasi" name="id_lokasi" class="form-control" required>
                                <option value="" disabled>-- Pilih Lokasi --</option>
                                <?php foreach ($lokasiList as $lok): ?>
                                    <option value="<?= $lok['id_lokasi'] ?>" <?= ($id_lokasi == $lok['id_lokasi']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($lok['nama_lokasi']) ?> (PJ: <?= htmlspecialchars($lok['penanggung_jawab']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="stok">Jumlah Stok (Unit) <span class="required">*</span></label>
                            <input type="number" id="stok" name="stok" class="form-control" 
                                   min="0" value="<?= htmlspecialchars((string)$stok) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="harga">Harga Satuan (Rp) <span class="required">*</span></label>
                            <input type="number" id="harga" name="harga" class="form-control" 
                                   min="0" step="500" value="<?= htmlspecialchars((string)$harga) ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_masuk">Tanggal Masuk <span class="required">*</span></label>
                        <input type="date" id="tanggal_masuk" name="tanggal_masuk" class="form-control" 
                               value="<?= htmlspecialchars($tanggal_masuk) ?>" required>
                    </div>

                    <div class="form-actions">
                        <a href="index.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
