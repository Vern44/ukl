<?php
/**
 * proses_login.php - Verifikasi kredensial user menggunakan PDO & password_verify()
 */
session_start();

// Tolak akses langsung selain metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

require_once 'config/koneksi.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header("Location: login.php?status=kosong");
    exit;
}

// Ambil data user berdasarkan username menggunakan PDO Prepared Statement
$stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :username LIMIT 1");
$stmt->execute([':username' => $username]);
$user = $stmt->fetch();

// Verifikasi password input terhadap hash bcrypt di database
if ($user && password_verify($password, $user['password'])) {
    session_regenerate_id(true); // Cegah session fixation saat login sukses
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['username']  = $user['username'];
    $_SESSION['logged_in'] = true;

    header("Location: dashboard.php");
    exit;
}

header("Location: login.php?status=gagal");
exit;
