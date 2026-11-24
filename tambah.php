<?php
/**
 * File: tambah.php
 * Deskripsi: Menampilkan form tambah barang baru dan memproses data input
 *            menggunakan validasi server-side dan PDO Prepared Statements.
 */

require_once 'config/koneksi.php';

$error = '';
$kode_barang   = '';
$nama_barang   = '';
$kategori      = '';
$stok          = '';
$harga         = '';
$tanggal_masuk = date('Y-m-d');

// Proses form saat metode HTTP adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitasi dan pembersihan input pengguna
    $kode_barang   = trim($_POST['kode_barang'] ?? '');
    $nama_barang   = trim($_POST['nama_barang'] ?? '');
    $kategori      = trim($_POST['kategori'] ?? '');
    $stok          = trim($_POST['stok'] ?? '');
    $harga         = trim($_POST['harga'] ?? '');
    $tanggal_masuk = trim($_POST['tanggal_masuk'] ?? '');

    // Validasi input di sisi server
    if (empty($kode_barang) || empty($nama_barang) || empty($kategori) || $stok === '' || $harga === '' || empty($tanggal_masuk)) {
        $error = 'Seluruh kolom formulir wajib diisi!';
    } elseif (!is_numeric($stok) || (int)$stok < 0) {
        $error = 'Jumlah stok harus berupa angka positif atau nol.';
    } elseif (!is_numeric($harga) || (int)$harga < 0) {
        $error = 'Harga barang harus berupa angka positif atau nol.';
    } else {
        try {
            // Cek apakah kode barang sudah digunakan oleh barang lain
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM barang WHERE kode_barang = :kode_barang");
            $checkStmt->execute([':kode_barang' => $kode_barang]);
            
            if ($checkStmt->fetchColumn() > 0) {
                $error = "Kode barang '{$kode_barang}' sudah terdaftar, gunakan kode lain.";
            } else {
                // Query simpan data menggunakan PDO Prepared Statement
                $sql = "INSERT INTO barang (kode_barang, nama_barang, kategori, stok, harga, tanggal_masuk) 
                        VALUES (:kode_barang, :nama_barang, :kategori, :stok, :harga, :tanggal_masuk)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':kode_barang'   => $kode_barang,
                    ':nama_barang'   => $nama_barang,
                    ':kategori'      => $kategori,
                    ':stok'          => (int)$stok,
                    ':harga'         => (int)$harga,
                    ':tanggal_masuk' => $tanggal_masuk,
                ]);

                // Redirect kembali ke index dengan status sukses
                header("Location: index.php?status=sukses_tambah");
                exit;
            }
        } catch (PDOException $e) {
            $error = 'Gagal menyimpan data ke database: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang - Sistem Inventaris</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container" style="max-width: 680px;">
        <!-- Header Formulir -->
        <header class="app-header">
            <div>
                <h1>Tambah Barang Baru</h1>
                <p>Masukkan rincian informasi barang ke dalam sistem inventaris</p>
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

        <!-- Form Tambah Data -->
        <div class="card">
            <div class="card-body">
                <form action="tambah.php" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="kode_barang">Kode Barang <span class="required">*</span></label>
                            <input type="text" id="kode_barang" name="kode_barang" class="form-control" 
                                   placeholder="Contoh: BRG-007" 
                                   value="<?= htmlspecialchars($kode_barang) ?>" 
                                   required maxlength="20" autofocus>
                        </div>
                        <div class="form-group">
                            <label for="kategori">Kategori <span class="required">*</span></label>
                            <select id="kategori" name="kategori" class="form-control" required>
                                <option value="" disabled <?= empty($kategori) ? 'selected' : '' ?>>-- Pilih Kategori --</option>
                                <?php
                                $daftar_kategori = ['Elektronik', 'Aksesoris Komputer', 'Furnitur', 'ATK', 'Peralatan', 'Lainnya'];
                                foreach ($daftar_kategori as $kat):
                                ?>
                                    <option value="<?= $kat ?>" <?= ($kategori === $kat) ? 'selected' : '' ?>><?= $kat ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nama_barang">Nama Barang <span class="required">*</span></label>
                        <input type="text" id="nama_barang" name="nama_barang" class="form-control" 
                               placeholder="Contoh: Monitor LG UltraWide 29 Inch" 
                               value="<?= htmlspecialchars($nama_barang) ?>" 
                               required maxlength="100">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="stok">Jumlah Stok (Unit) <span class="required">*</span></label>
                            <input type="number" id="stok" name="stok" class="form-control" 
                                   placeholder="0" min="0" 
                                   value="<?= htmlspecialchars($stok) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="harga">Harga Satuan (Rp) <span class="required">*</span></label>
                            <input type="number" id="harga" name="harga" class="form-control" 
                                   placeholder="Contoh: 1500000" min="0" step="500" 
                                   value="<?= htmlspecialchars($harga) ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_masuk">Tanggal Masuk <span class="required">*</span></label>
                        <input type="date" id="tanggal_masuk" name="tanggal_masuk" class="form-control" 
                               value="<?= htmlspecialchars($tanggal_masuk) ?>" required>
                    </div>

                    <div class="form-actions">
                        <a href="index.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">💾 Simpan Data Barang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
