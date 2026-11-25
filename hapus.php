<?php
/**
 * hapus.php - Pemrosesan penghapusan data barang berdasarkan ID (terproteksi session)
 */
require_once 'includes/auth.php'; // Proteksi session: wajib login
require_once 'config/koneksi.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: dashboard.php?status=gagal&pesan=" . urlencode("ID barang tidak valid."));
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM barang WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: dashboard.php?status=" . ($stmt->rowCount() > 0 ? "sukses_hapus" : "gagal"));
} catch (PDOException $e) {
    header("Location: dashboard.php?status=gagal&pesan=" . urlencode("Gagal menghapus: " . $e->getMessage()));
}
exit;
