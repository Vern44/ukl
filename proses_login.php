<?php

session_start();


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


$stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :username LIMIT 1");
$stmt->execute([':username' => $username]);
$user = $stmt->fetch();


if ($user && password_verify($password, $user['password'])) {
    session_regenerate_id(true); 
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['username']  = $user['username'];
    $_SESSION['logged_in'] = true;

    header("Location: dashboard.php");
    exit;
}

header("Location: login.php?status=gagal");
exit;
