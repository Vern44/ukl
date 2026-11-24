<?php
/**
 * File: hapus.php
 * Deskripsi: Memproses penghapusan data barang berdasarkan ID yang dikirim melalui parameter URL,
 *            menggunakan query PDO Prepared Statements dan mengarahkan kembali ke index.php.
 */

require_once 'config/koneksi.php';

// Validasi parameter ID dari URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php?status=gagal&pesan=" . urlencode("ID barang tidak valid atau tidak disertakan."));
    exit;
}

try {
    // Eksekusi query hapus menggunakan PDO Prepared Statement
    $stmt = $pdo->prepare("DELETE FROM barang WHERE id = :id");
    $stmt->execute([':id' => $id]);

    // Periksa apakah ada baris data yang terhapus
    if ($stmt->rowCount() > 0) {
        header("Location: index.php?status=sukses_hapus");
        exit;
    } else {
        header("Location: index.php?status=gagal&pesan=" . urlencode("Data barang tidak ditemukan atau sudah dihapus sebelumnya."));
        exit;
    }
} catch (PDOException $e) {
    // Tangani error penghapusan data (misal foreign key restriction jika ada)
    header("Location: index.php?status=gagal&pesan=" . urlencode("Gagal menghapus data: " . $e->getMessage()));
    exit;
}
