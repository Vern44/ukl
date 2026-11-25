<?php
/**
 * logout.php - Menghapus seluruh data session dan mengarahkan ke login.php
 */
session_start();
session_unset();   // Hapus semua variabel $_SESSION
session_destroy(); // Hancurkan session aktif di server

header("Location: login.php?status=logout");
exit;
