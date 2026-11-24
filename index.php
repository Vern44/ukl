<?php
/**
 * File: index.php
 * Deskripsi: Halaman utama menampilkan daftar data barang, ringkasan statistik,
 *            serta tombol aksi Tambah, Edit, dan Hapus dengan notifikasi status.
 */

require_once 'config/koneksi.php';

// Ambil pesan status dari query string untuk notifikasi
$status = $_GET['status'] ?? '';
$pesan  = $_GET['pesan'] ?? '';

// Query untuk mengambil semua data barang dari database
try {
    $stmt = $pdo->query("SELECT * FROM barang ORDER BY id DESC");
    $daftar_barang = $stmt->fetchAll();

    // Query statistik ringkas
    $stat_stmt = $pdo->query("SELECT COUNT(*) as total_item, COALESCE(SUM(stok), 0) as total_stok, COALESCE(SUM(stok * harga), 0) as total_nilai FROM barang");
    $stats = $stat_stmt->fetch();
} catch (PDOException $e) {
    die("Gagal mengambil data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventaris Barang</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Header Aplikasi -->
        <header class="app-header">
            <div>
                <h1>📦 Sistem Inventaris Barang</h1>
                
            </div>
            <div>
                <a href="tambah.php" class="btn btn-primary">+ Tambah Barang Baru</a>
            </div>
        </header>

        <!-- Pesan Notifikasi Status -->
        <?php if ($status === 'sukses_tambah'): ?>
            <div class="alert alert-success">
                <span>✅ <strong>Berhasil!</strong> Data barang baru berhasil disimpan ke database.</span>
            </div>
        <?php elseif ($status === 'sukses_edit'): ?>
            <div class="alert alert-success">
                <span>✅ <strong>Berhasil!</strong> Perubahan data barang berhasil diperbarui.</span>
            </div>
        <?php elseif ($status === 'sukses_hapus'): ?>
            <div class="alert alert-success">
                <span>✅ <strong>Berhasil!</strong> Data barang telah dihapus dari sistem.</span>
            </div>
        <?php elseif ($status === 'gagal'): ?>
            <div class="alert alert-danger">
                <span>❌ <strong>Gagal!</strong> <?= htmlspecialchars($pesan ?: 'Terjadi kesalahan saat memproses data.') ?></span>
            </div>
        <?php endif; ?>

        <!-- Kartu Statistik Singkat -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="label">Total Jenis Barang</div>
                <div class="value"><?= number_format($stats['total_item'], 0, ',', '.') ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Total Unit Stok</div>
                <div class="value"><?= number_format($stats['total_stok'], 0, ',', '.') ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Estimasi Nilai Aset</div>
                <div class="value">Rp <?= number_format($stats['total_nilai'], 0, ',', '.') ?></div>
            </div>
        </div>

        <!-- Tabel Daftar Barang -->
        <div class="card">
            <div class="card-header">
                <h2>Daftar Barang Tersedia</h2>
                <span class="badge"><?= count($daftar_barang) ?> Data Ditemukan</span>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">No</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th style="text-align: right;">Stok</th>
                                <th style="text-align: right;">Harga Satuan</th>
                                <th>Tanggal Masuk</th>
                                <th style="text-align: center; width: 160px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($daftar_barang)): ?>
                                <tr>
                                    <td colspan="8">
                                        <div class="empty-state">
                                            <p>Belum ada data barang dalam inventaris.</p>
                                            <a href="tambah.php" class="btn btn-primary" style="margin-top: 10px;">+ Tambah Barang Pertama</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($daftar_barang as $row): ?>
                                    <tr>
                                        <td style="text-align: center; color: var(--text-muted);"><?= $no++ ?></td>
                                        <td><strong><?= htmlspecialchars($row['kode_barang']) ?></strong></td>
                                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                        <td><span class="badge"><?= htmlspecialchars($row['kategori']) ?></span></td>
                                        <td style="text-align: right;"><?= number_format($row['stok'], 0, ',', '.') ?></td>
                                        <td style="text-align: right;">Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['tanggal_masuk'])) ?></td>
                                        <td style="text-align: center;">
                                            <div class="action-buttons" style="justify-content: center;">
                                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                                                <a href="hapus.php?id=<?= $row['id'] ?>" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Apakah Anda yakin ingin menghapus barang <?= htmlspecialchars($row['nama_barang']) ?> (<?= htmlspecialchars($row['kode_barang']) ?>)?');">
                                                    Hapus
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
