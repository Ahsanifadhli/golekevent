<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GolekEvent - Platform Event Terlengkap</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- CSS Kustom Anda -->
    <link rel="stylesheet" href="/assets/css/style.css"> <!-- Gunakan path absolut -->

    <style>
        /* PERBAIKAN: Memberi jarak di atas body agar konten tidak tertutup oleh header */
        body {
            padding-top: 70px; /* Sesuaikan nilainya dengan tinggi header Anda */
        }
    </style>
</head>
<body>
    <header>
        <!-- PERBAIKAN: Tambahkan kelas 'fixed-top' di sini -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm fixed-top">
            <div class="container">
                <a class="navbar-brand fw-bold" href="/index.php">GolekEvent</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="main-nav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <?php // Logika untuk menampilkan menu berdasarkan status login dan role ?>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php // --- MENU UNTUK PENGGUNA YANG SUDAH LOGIN --- ?>
                            
                            <li class="nav-item">
                                <span class="navbar-text me-3">
                                    Halo, <?php echo htmlspecialchars($_SESSION['nama']); ?>!
                                </span>
                            </li>

                            <?php if ($_SESSION['role'] == 'peserta'): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/peserta/dashboard.php">Dashboard</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/peserta/tiket_saya.php">Tiket Saya</a>
                                </li>
                            <?php elseif ($_SESSION['role'] == 'panitia'): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/panitia/dashboard.php">Dashboard Panitia</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/panitia/buat_event.php">Buat Event</a>
                                </li>
                            <?php elseif ($_SESSION['role'] == 'admin'): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/admin/dashboard.php">Dashboard Admin</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/admin/review_event.php">Review Event</a>
                                </li>
                            <?php endif; ?>

                            <li class="nav-item ms-lg-3">
                                <a class="btn btn-outline-light btn-sm" href="/logout.php">Logout</a>
                            </li>

                        <?php else: ?>
                            <?php // --- MENU UNTUK PENGUNJUNG (GUEST) --- ?>
                            <li class="nav-item">
                                <a class="nav-link active" href="/index.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Cari Event</a>
                            </li>
                            <li class="nav-item ms-lg-3">
                                <a class="btn btn-primary" href="/login.php">Masuk / Daftar</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main>
