<?php
/**
 * index.php - Halaman awal terproteksi yang mengarahkan user login ke dashboard.php
 */
require_once 'includes/auth.php'; // Proteksi session: redirect ke login.php jika belum login

$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: dashboard.php{$qs}");
exit;
