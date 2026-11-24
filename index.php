<?php
/**
 * index.php - Daftar inventaris barang dengan INNER JOIN 3 tabel (3NF)
 */
require_once 'config/koneksi.php';

$status = $_GET['status'] ?? '';
$pesan  = $_GET['pesan'] ?? '';

// Query INNER JOIN 3 tabel: menampilkan nama kategori & lokasi menggantikan foreign key ID
$sql = "SELECT b.id, b.kode_barang, b.nama_barang, b.stok, b.harga, b.tanggal_masuk,
               k.nama_kategori, l.nama_lokasi, l.penanggung_jawab
        FROM barang b
        INNER JOIN kategori k ON b.id_kategori = k.id_kategori
        INNER JOIN lokasi l ON b.id_lokasi = l.id_lokasi
        ORDER BY b.id DESC";
$daftar_barang = $pdo->query($sql)->fetchAll();

// Statistik ringkas inventaris
$stats = $pdo->query("SELECT COUNT(*) total_item, COALESCE(SUM(stok),0) total_stok, COALESCE(SUM(stok * harga),0) total_nilai FROM barang")->fetch();

$page_title = 'Sistem Inventaris Barang - CRUD 3NF';
require 'includes/header.php';
?>
<!-- Header Aplikasi -->
<header class="app-header">
    <div>
        <h1>📦 Sistem Inventaris Barang</h1>
        <p>Manajemen data inventaris berbasis PHP Native, MySQL PDO & Normalisasi 3NF</p>
    </div>
    <div><a href="tambah.php" class="btn btn-primary">+ Tambah Barang Baru</a></div>
</header>

<!-- Pesan Notifikasi Operasi -->
<?php
$notif = [
    'sukses_tambah' => ['success', 'Data barang baru berhasil disimpan ke database.'],
    'sukses_edit'   => ['success', 'Perubahan data barang berhasil diperbarui.'],
    'sukses_hapus'  => ['success', 'Data barang telah dihapus dari sistem.'],
    'gagal'         => ['danger', htmlspecialchars($pesan ?: 'Terjadi kesalahan saat memproses data.')]
];
if (isset($notif[$status])): ?>
    <div class="alert alert-<?= $notif[$status][0] ?>">
        <span><?= $notif[$status][0] === 'success' ? '✅' : '❌' ?> <strong><?= $notif[$status][0] === 'success' ? 'Berhasil' : 'Gagal' ?>!</strong> <?= $notif[$status][1] ?></span>
    </div>
<?php endif; ?>

<!-- Kartu Statistik -->
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

<!-- Tabel Data Berelasi -->
<div class="card">
    <div class="card-header">
        <h2>Daftar Barang Tersedia (Relasi 3NF)</h2>
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
                        <th>Lokasi Penempatan</th>
                        <th style="text-align: right;">Stok</th>
                        <th style="text-align: right;">Harga Satuan</th>
                        <th>Tanggal Masuk</th>
                        <th style="text-align: center; width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftar_barang)): ?>
                        <tr><td colspan="9"><div class="empty-state"><p>Belum ada data barang.</p><a href="tambah.php" class="btn btn-primary" style="margin-top: 10px;">+ Tambah Barang Pertama</a></div></td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($daftar_barang as $row): ?>
                            <tr>
                                <td style="text-align: center; color: var(--text-muted);"><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($row['kode_barang']) ?></strong></td>
                                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td><span class="badge"><?= htmlspecialchars($row['nama_kategori']) ?></span></td>
                                <td>
                                    <strong><?= htmlspecialchars($row['nama_lokasi']) ?></strong><br>
                                    <small style="color: var(--text-muted); font-size: 0.78rem;">PJ: <?= htmlspecialchars($row['penanggung_jawab']) ?></small>
                                </td>
                                <td style="text-align: right;"><?= number_format($row['stok'], 0, ',', '.') ?></td>
                                <td style="text-align: right;">Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_masuk'])) ?></td>
                                <td style="text-align: center;">
                                    <div class="action-buttons" style="justify-content: center;">
                                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                                        <a href="hapus.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus barang <?= htmlspecialchars($row['nama_barang']) ?>?');">Hapus</a>
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
<?php require 'includes/footer.php'; ?>
