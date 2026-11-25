<?php
/**
 * File: config/koneksi.php
 * Deskripsi: Mengelola koneksi database MySQL menggunakan PDO dengan penanganan try-catch.
 */

// Konfigurasi kredensial database (prefix db_ agar tidak bentrok dengan variabel form)
$db_host = 'localhost';
$db_name = 'db_inventaris';
$db_user = 'root';
$db_pass = '';

try {
    // Membuat instance PDO untuk koneksi ke MySQL
    $dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Menampilkan error sebagai exception
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Hasil query berupa array asosiatif
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Menggunakan native prepared statements
    ];

    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // Tangkap error jika koneksi gagal dan hentikan script
    die("Koneksi database gagal: " . $e->getMessage());
}
