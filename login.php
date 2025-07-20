<?php
// Panggil config.php yang sudah berisi session_start()
require_once 'config.php';

// Jika pengguna sudah login, langsung arahkan ke dashboard yang sesuai
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') header('Location: admin/dashboard.php');
    elseif ($_SESSION['role'] == 'panitia') header('Location: panitia/dashboard.php');
    else header('Location: peserta/dashboard.php');
    exit();
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT id, nama, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Set session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];

            // Arahkan berdasarkan role
            if ($user['role'] == 'admin') header('Location: admin/dashboard.php');
            elseif ($user['role'] == 'panitia') header('Location: panitia/dashboard.php');
            else header('Location: peserta/dashboard.php');
            exit();
        }
    }
    $error = "Email atau password yang Anda masukkan salah.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GolekEvent</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #333; margin-bottom: 10px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; color: #555; font-weight: 500; }
        input[type="email"], input[type="password"] { width: 100%; padding: 12px; border: 2px solid #e1e1e1; border-radius: 5px; font-size: 16px; transition: border-color 0.3s; }
        .btn { width: 100%; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
        .message { padding: 12px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .register-link { text-align: center; margin-top: 20px; }
        .register-link a { color: #667eea; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><h1>Login GolekEvent</h1></div>
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-group"><label for="email">Email</label><input type="email" name="email" id="email" required></div>
            <div class="form-group"><label for="password">Password</label><input type="password" name="password" id="password" required></div>
            <button type="submit" class="btn">Login</button>
        </form>
        <div class="register-link"><p>Belum punya akun? <a href="register.php">Daftar di sini</a></p></div>
    </div>
</body>
</html>
