<?php
session_start();

if (empty($_SESSION['logged_in'])) {
    header("Location: login.php?status=harus_login");
    exit;
}
