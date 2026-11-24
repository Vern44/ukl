<?php
/**
 * File: config/koneksi.php
 * Deskripsi: Mengelola koneksi database MySQL menggunakan PDO dengan penanganan try-catch.
 */

// Konfigurasi kredensial database
$host     = 'localhost';
$dbname   = 'db_inventaris';
$username = 'root';
$password = '';

try {
    // Membuat instance PDO untuk koneksi ke MySQL
    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Menampilkan error sebagai exception
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Hasil query berupa array asosiatif
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Menggunakan native prepared statements
    ];

    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // Tangkap error jika koneksi gagal dan hentikan script
    die("Koneksi database gagal: " . $e->getMessage());
}
