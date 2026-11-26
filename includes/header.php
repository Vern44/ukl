<?php

$page_title = $page_title ?? 'Sistem Inventaris Barang';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container"<?= !empty($max_width) ? ' style="max-width: ' . htmlspecialchars($max_width) . ';"' : '' ?>>
