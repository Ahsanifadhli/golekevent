<?php
/*
File ini adalah contoh konfigurasi.
Salin file ini dan ubah namanya menjadi 'config.php', lalu isi detail di bawah ini.
*/

// Aktifkan baris ini jika Anda ingin menampilkan error saat development
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

// Anda perlu menjalankan 'composer install' agar file ini ada
// require_once __DIR__ . '/vendor/autoload.php';

// session_start();

// --- KREDENSIAL DATABASE ---
// Ganti dengan detail koneksi database Anda
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'ganti_dengan_username_db_anda');
define('DB_PASSWORD', 'ganti_dengan_password_db_anda');
define('DB_NAME', 'ganti_dengan_nama_db_anda');


// --- KONEKSI DATABASE ---
$db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($db->connect_error) {
    die("Koneksi ke database gagal: " . $db->connect_error);
}


// --- FUNGSI BANTUAN ---
/**
 * Memeriksa apakah email sudah ada di tabel users.
 * @param mysqli $db Koneksi database
 * @param string $email Email yang akan dicek
 * @return bool True jika email ada, false jika tidak ada
 */
function get_user_by_email($db, $email) {
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $count = $stmt->num_rows;
    $stmt->close();
    
    return $count > 0;
}
?>
