<?php
// config.php dipanggil untuk memulai session
require_once 'config.php';

// Hapus semua variabel session
$_SESSION = array();

// Hancurkan session
session_destroy();

// Arahkan pengguna kembali ke halaman login dengan pesan
header("Location: login.php?status=logout_success");
exit();
?>
