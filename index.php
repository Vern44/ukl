<?php

require_once 'includes/auth.php'; 
$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("Location: dashboard.php{$qs}");
exit;
