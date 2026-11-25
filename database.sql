-- ========================================================
-- Database: db_inventaris
-- Aplikasi: Sistem Inventaris Barang (3NF + Autentikasi Users)
-- ========================================================

CREATE DATABASE IF NOT EXISTS `db_inventaris` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_inventaris`;

DROP TABLE IF EXISTS `barang`;
DROP TABLE IF EXISTS `lokasi`;
DROP TABLE IF EXISTS `kategori`;
DROP TABLE IF EXISTS `users`;

-- 1. Tabel Autentikasi: users
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabel Master: kategori
CREATE TABLE `kategori` (
  `id_kategori` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_kategori` VARCHAR(50) NOT NULL UNIQUE,
  `deskripsi` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabel Master: lokasi
CREATE TABLE `lokasi` (
  `id_lokasi` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_lokasi` VARCHAR(50) NOT NULL UNIQUE,
  `penanggung_jawab` VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabel Utama: barang
CREATE TABLE `barang` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_barang` VARCHAR(20) NOT NULL UNIQUE,
  `nama_barang` VARCHAR(100) NOT NULL,
  `id_kategori` INT NOT NULL,
  `id_lokasi` INT NOT NULL,
  `stok` INT NOT NULL DEFAULT 0,
  `harga` INT NOT NULL DEFAULT 0,
  `tanggal_masuk` DATE NOT NULL,
  CONSTRAINT `fk_barang_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_barang_lokasi` FOREIGN KEY (`id_lokasi`) REFERENCES `lokasi` (`id_lokasi`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Akun admin default (username: admin | password: admin123)
INSERT INTO `users` (`username`, `password`) VALUES
('admin', '$2y$12$1VK5apq1oQ22UCnIf8KX.uZaWrzNfEzkPwVfsHpzHYRvJG5HiE0L.');

-- Data Contoh Master: kategori
INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `deskripsi`) VALUES
(1, 'Elektronik', 'Peralatan dan perangkat berbasis listrik atau digital'),
(2, 'Aksesoris Komputer', 'Perangkat periferal dan pelengkap komputer/laptop'),
(3, 'Furnitur', 'Perabotan kerja seperti meja, kursi, dan lemari'),
(4, 'ATK', 'Alat tulis kantor dan kebutuhan kertas kerja');

-- Data Contoh Master: lokasi
INSERT INTO `lokasi` (`id_lokasi`, `nama_lokasi`, `penanggung_jawab`) VALUES
(1, 'Gudang Utama', 'Budi Santoso'),
(2, 'Lab Komputer 1', 'Ahmad Fauzi'),
(3, 'Ruang Administrasi', 'Siti Rahma'),
(4, 'Ruang Guru & Dosen', 'Hendra Wijaya');

-- Data Contoh Utama: barang
INSERT INTO `barang` (`kode_barang`, `nama_barang`, `id_kategori`, `id_lokasi`, `stok`, `harga`, `tanggal_masuk`) VALUES
('BRG-001', 'Laptop ASUS Vivobook 14', 1, 2, 15, 8500000, '2026-01-10'),
('BRG-002', 'Mouse Wireless Logitech M220', 2, 2, 35, 165000, '2026-01-15'),
('BRG-003', 'Keyboard Mechanical RGB TKL', 2, 2, 20, 420000, '2026-02-01'),
('BRG-004', 'Kursi Kerja Ergonomis Mesh', 3, 3, 8, 1250000, '2026-02-08'),
('BRG-005', 'Monitor LED 24 Inch Samsung', 1, 2, 12, 1750000, '2026-02-14'),
('BRG-006', 'Kertas HVS A4 80gr PaperOne', 4, 1, 60, 55000, '2026-02-20');
