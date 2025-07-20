<?php
// Aktifkan tampilan error untuk mempermudah debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Mulai session

// Mengimpor file konfigurasi (berisi koneksi $db dan fungsi bantuan)
require_once 'config.php';

// ====================================================================
// INI BAGIAN PERBAIKAN UNTUK WARNING 'UNDEFINED VARIABLE'
// Kode ini memastikan $role selalu ada nilainya sejak halaman dibuka,
// mengambil dari URL atau default ke 'peserta'.
$role = isset($_GET['role']) && ($_GET['role'] == 'panitia') ? 'panitia' : 'peserta';
// ====================================================================

// Inisialisasi variabel pesan
$error = '';
$success = '';

// Proses form hanya jika metode request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi input dasar
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $konfirmasi_password = trim($_POST['konfirmasi_password']);
    $role_dari_form = trim($_POST['role']); // Ambil role dari hidden input

    // Ambil nama organisasi jika rolenya panitia, jika tidak, isi dengan NULL
    $nama_organisasi = ($role_dari_form == 'panitia') ? trim($_POST['nama_organisasi']) : NULL;

    // Validasi Lanjutan
    if (empty($nama) || empty($email) || empty($password) || empty($konfirmasi_password)) {
        $error = "Semua kolom wajib diisi.";
    } elseif ($role_dari_form == 'panitia' && empty($nama_organisasi)) {
        $error = "Nama Komunitas / Organisasi wajib diisi untuk panitia.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (strlen($password) < 8) {
        $error = "Password minimal harus 8 karakter.";
    } elseif ($password !== $konfirmasi_password) {
        $error = "Password dan Konfirmasi Password tidak cocok.";
    } elseif (get_user_by_email($db, $email)) { // Pastikan fungsi ini ada di config.php
        $error = "Email sudah terdaftar. Silakan gunakan email lain.";
    } else {
        // Jika semua validasi lolos
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (nama, email, password, role, nama_organisasi) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sssss", $nama, $email, $hashed_password, $role_dari_form, $nama_organisasi);

        if ($stmt->execute()) {
            $success = "Pendaftaran berhasil! Silakan <a href='login.php'>login</a>.";
        } else {
            $error = "Pendaftaran gagal. Terjadi kesalahan pada server.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - GolekEvent</title>
<style>
    /* Menggunakan CSS dari template login Anda agar konsisten */
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px; }
    .header { text-align: center; margin-bottom: 30px; }
    .header h1 { color: #333; margin-bottom: 10px; }
    .form-group { margin-bottom: 20px; }
    label { display: block; margin-bottom: 5px; color: #555; font-weight: 500; }
    input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 12px; border: 2px solid #e1e1e1; border-radius: 5px; font-size: 16px; transition: border-color 0.3s; }
    input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus { outline: none; border-color: #667eea; }
    .btn { width: 100%; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; transition: transform 0.2s; }
    .btn:hover { transform: translateY(-2px); }
    .message { padding: 12px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
    .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .login-link { text-align: center; margin-top: 20px; }
    .login-link a { color: #667eea; text-decoration: none; }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Buat Akun GolekEvent</h1>
        <p>Daftar sebagai <?php echo htmlspecialchars($role); ?></p> </div>

    <?php if (!empty($error)): ?>
        <div class="message error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="message success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="post" action="register.php">
        <input type="hidden" name="role" value="<?php echo htmlspecialchars($role); ?>">
        
        <div class="form-group">
            <label for="nama">Nama Lengkap</label>
            <input type="text" name="nama" id="nama" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>

        <?php if ($role == 'panitia'): ?>
        <div class="form-group">
            <label for="nama_organisasi">Nama Komunitas / Organisasi</label>
            <input type="text" name="nama_organisasi" id="nama_organisasi" required>
        </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div class="form-group">
            <label for="konfirmasi_password">Ulangi Password</label>
            <input type="password" name="konfirmasi_password" id="konfirmasi_password" required>
        </div>
        <button type="submit" class="btn">Daftar</button>
    </form>
    <div class="login-link">
        <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</div>
</body>
</html>