<?php
/**
 * includes/auth.php - Penjaga autentikasi session untuk seluruh halaman terproteksi
 */
session_start();

// Jika session logged_in belum ada/false, paksa redirect ke halaman login
if (empty($_SESSION['logged_in'])) {
    header("Location: login.php?status=harus_login");
    exit;
}
