# Sistem Inventaris Barang (CRUD PHP Native & MySQL PDO)

Aplikasi web CRUD (Create, Read, Update, Delete) sederhana, terstruktur, dan modern untuk pendataan dan manajemen inventaris barang. Dibangun menggunakan **PHP Native**, **MySQL**, **HTML5**, **CSS3 murni (tanpa framework eksternal)**, dan koneksi database **PDO (PHP Data Objects)** dengan **Prepared Statements**.

---

## 📋 Fitur Utama
- **Read (Tampil Data & Statistik)**: Menampilkan seluruh inventaris barang dalam tabel responsif beserta ringkasan statistik (total jenis barang, total stok unit, estimasi nilai aset).
- **Create (Tambah Barang)**: Formulir penambahan barang baru dengan validasi data wajib (HTML5 & PHP) serta pemeriksaan keunikan kode barang.
- **Update (Edit Barang)**: Mengambil data barang berdasarkan ID dan memperbarui informasi barang secara aman.
- **Delete (Hapus Barang)**: Menghapus data barang dengan dialog konfirmasi sebelum eksekusi.
- **Keamanan Terjamin**:
  - Menggunakan **PDO Prepared Statements** untuk mencegah ancaman *SQL Injection*.
  - Sanitasi teks dan fungsi `htmlspecialchars()` untuk mencegah serangan *Cross-Site Scripting (XSS)*.
- **Notifikasi Interaktif**: Pesan alert status yang jelas untuk setiap operasi berhasil atau gagal.

---

## 📁 Struktur Folder & File
```text
ukl/
│
├── config/
│   └── koneksi.php       # Konfigurasi koneksi MySQL dengan PDO & try-catch
├── assets/
│   └── css/
│       └── style.css     # Styling antarmuka modern, bersih, dan responsif
├── index.php             # Halaman utama menampilkan tabel inventaris & notifikasi
├── tambah.php            # Form dan proses penambahan data barang baru
├── edit.php              # Form dan proses pembaruan data barang berdasarkan ID
├── hapus.php             # Skrip proses penghapusan data barang berdasarkan ID
├── database.sql          # Skrip SQL pembuatan database, tabel, dan data contoh
└── README.md             # Dokumentasi lengkap petunjuk penggunaan
```

---

## 🗄️ Spesifikasi Database
- **Nama Database**: `db_inventaris`
- **Nama Tabel**: `barang`

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `INT AUTO_INCREMENT` | **Primary Key** unik |
| `kode_barang` | `VARCHAR(20)` | Kode unik barang (misal: `BRG-001`) |
| `nama_barang` | `VARCHAR(100)` | Nama lengkap barang |
| `kategori` | `VARCHAR(50)` | Kategori barang (Elektronik, ATK, Furnitur, dll.) |
| `stok` | `INT` | Jumlah ketersediaan stok fisik |
| `harga` | `INT` | Harga satuan barang (Rupiah) |
| `tanggal_masuk`| `DATE` | Tanggal pencatatan barang masuk |

---

## 💻 Kebutuhan Sistem
1. **Web Server**: Apache (tersedia di XAMPP / Laragon / WampServer)
2. **PHP**: Versi 7.4 atau PHP 8.x (dengan ekstensi `pdo_mysql` aktif)
3. **Database**: MySQL versi 5.7+ atau MariaDB 10.3+
4. **Web Browser**: Google Chrome, Mozilla Firefox, Microsoft Edge, atau Safari

---

## 🚀 Langkah-Langkah Menjalankan di Localhost

### 1. Letakkan Folder Project
Pastikan folder `ukl` berada di dalam direktori `htdocs` server web lokal Anda:
- **XAMPP**: `C:\xampp\htdocs\ukl`
- **Laragon**: `C:\laragon\www\ukl`

### 2. Nyalakan Apache & MySQL
Buka aplikasi **XAMPP Control Panel**, lalu klik tombol **Start** pada modul **Apache** dan **MySQL**.

### 3. Impor Database
1. Buka browser dan akses [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Buat database baru bernama `db_inventaris` (atau langsung ke tab **Import**).
3. Klik tab **Import** / **Impor**.
4. Klik tombol **Choose File** dan pilih file `database.sql` yang ada di folder project `ukl/database.sql`.
5. Klik tombol **Go** / **Kirim** di bagian bawah untuk mengeksekusi impor.

### 4. Konfigurasi Koneksi (Jika Berbeda)
Jika Anda menggunakan username/password MySQL yang berbeda, sesuaikan di file `config/koneksi.php`:
```php
$host     = 'localhost';
$dbname   = 'db_inventaris';
$username = 'root'; // default xampp: root
$password = '';     // default xampp: kosong
```

### 5. Akses Website
Buka browser dan kunjungi URL berikut:
```text
http://localhost/ukl/
```
Aplikasi siap digunakan untuk mengelola data inventaris barang!
