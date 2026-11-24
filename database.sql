-- ========================================================
-- Database: db_inventaris
-- Aplikasi: Sistem Pendataan & Inventaris Barang
-- ========================================================

-- Buat database jika belum ada
CREATE DATABASE IF NOT EXISTS `db_inventaris` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_inventaris`;

-- Struktur tabel `barang`
DROP TABLE IF EXISTS `barang`;
CREATE TABLE `barang` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_barang` VARCHAR(20) NOT NULL UNIQUE,
  `nama_barang` VARCHAR(100) NOT NULL,
  `kategori` VARCHAR(50) NOT NULL,
  `stok` INT NOT NULL DEFAULT 0,
  `harga` INT NOT NULL DEFAULT 0,
  `tanggal_masuk` DATE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data contoh untuk pengujian (Minimal 5 baris)
INSERT INTO `barang` (`kode_barang`, `nama_barang`, `kategori`, `stok`, `harga`, `tanggal_masuk`) VALUES
('BRG-001', 'Laptop ASUS Vivobook 14', 'Elektronik', 15, 8500000, '2026-01-10'),
('BRG-002', 'Mouse Wireless Logitech M220', 'Aksesoris Komputer', 35, 165000, '2026-01-15'),
('BRG-003', 'Keyboard Mechanical RGB TKL', 'Aksesoris Komputer', 20, 420000, '2026-02-01'),
('BRG-004', 'Kursi Kerja Ergonomis Mesh', 'Furnitur', 8, 1250000, '2026-02-08'),
('BRG-005', 'Monitor LED 24 Inch Samsung', 'Elektronik', 12, 1750000, '2026-02-14'),
('BRG-006', 'Kertas HVS A4 80gr PaperOne', 'ATK', 60, 55000, '2026-02-20');
