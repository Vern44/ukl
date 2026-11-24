<?php
/**
 * File: tambah.php
 * Deskripsi: Menampilkan formulir penambahan barang dengan dropdown dinamis yang
 *            mengambil data dari tabel master (kategori & lokasi) serta memproses
 *            penyimpanan menggunakan PDO Prepared Statements.
 */

require_once 'config/koneksi.php';

$error = '';
$kode_barang   = '';
$nama_barang   = '';
$id_kategori   = '';
$id_lokasi     = '';
$stok          = '';
$harga         = '';
$tanggal_masuk = date('Y-m-d');

// Mengambil data referensi dari tabel master kategori & lokasi untuk dropdown dinamis
try {
    $kategoriList = $pdo->query("SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori ASC")->fetchAll();
    $lokasiList   = $pdo->query("SELECT id_lokasi, nama_lokasi, penanggung_jawab FROM lokasi ORDER BY nama_lokasi ASC")->fetchAll();
} catch (PDOException $e) {
    die("Gagal memuat data master: " . $e->getMessage());
}

// Proses form saat metode HTTP adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitasi dan pembersihan input pengguna
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
            // Cek apakah kode barang sudah pernah terdaftar (harus unik)
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM barang WHERE kode_barang = :kode_barang");
            $checkStmt->execute([':kode_barang' => $kode_barang]);
            
            if ($checkStmt->fetchColumn() > 0) {
                $error = "Kode barang '{$kode_barang}' sudah terdaftar, gunakan kode lain.";
            } else {
                // Query insert menggunakan PDO Prepared Statements dengan relasi FK (id_kategori & id_lokasi)
                $sql = "INSERT INTO barang (kode_barang, nama_barang, id_kategori, id_lokasi, stok, harga, tanggal_masuk) 
                        VALUES (:kode_barang, :nama_barang, :id_kategori, :id_lokasi, :stok, :harga, :tanggal_masuk)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':kode_barang'   => $kode_barang,
                    ':nama_barang'   => $nama_barang,
                    ':id_kategori'   => $id_kategori,
                    ':id_lokasi'     => $id_lokasi,
                    ':stok'          => (int)$stok,
                    ':harga'         => (int)$harga,
                    ':tanggal_masuk' => $tanggal_masuk,
                ]);

                // Redirect kembali ke index dengan notifikasi status sukses
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
                <p>Masukkan data barang dengan memilih referensi kategori & lokasi yang sesuai</p>
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

        <!-- Form Tambah Data Berelasi -->
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
                            <label for="nama_barang">Nama Barang <span class="required">*</span></label>
                            <input type="text" id="nama_barang" name="nama_barang" class="form-control" 
                                   placeholder="Contoh: Monitor LG 27 Inch" 
                                   value="<?= htmlspecialchars($nama_barang) ?>" 
                                   required maxlength="100">
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- Dropdown Relasi Tabel Master: kategori -->
                        <div class="form-group">
                            <label for="id_kategori">Kategori Barang <span class="required">*</span></label>
                            <select id="id_kategori" name="id_kategori" class="form-control" required>
                                <option value="" disabled <?= empty($id_kategori) ? 'selected' : '' ?>>-- Pilih Kategori --</option>
                                <?php foreach ($kategoriList as $kat): ?>
                                    <option value="<?= $kat['id_kategori'] ?>" <?= ($id_kategori == $kat['id_kategori']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($kat['nama_kategori']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Dropdown Relasi Tabel Master: lokasi -->
                        <div class="form-group">
                            <label for="id_lokasi">Lokasi Penempatan <span class="required">*</span></label>
                            <select id="id_lokasi" name="id_lokasi" class="form-control" required>
                                <option value="" disabled <?= empty($id_lokasi) ? 'selected' : '' ?>>-- Pilih Lokasi --</option>
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
                                   placeholder="0" min="0" 
                                   value="<?= htmlspecialchars((string)$stok) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="harga">Harga Satuan (Rp) <span class="required">*</span></label>
                            <input type="number" id="harga" name="harga" class="form-control" 
                                   placeholder="Contoh: 1500000" min="0" step="500" 
                                   value="<?= htmlspecialchars((string)$harga) ?>" required>
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
