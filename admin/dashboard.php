<?php


// Mengimpor file konfigurasi untuk koneksi database
require_once '../config.php';

// Pengecekan akses: Hanya admin yang boleh masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Menyertakan header setelah pengecekan session
require_once '../templates/header.php';

// --- Logika untuk Mengambil Data Statistik ---

// 1. Total Pengguna
$totalUsersResult = $db->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $totalUsersResult->fetch_assoc()['total'];

// 2. Total Event
$totalEventsResult = $db->query("SELECT COUNT(*) as total FROM events");
$totalEvents = $totalEventsResult->fetch_assoc()['total'];

// 3. Event Menunggu Persetujuan
$pendingEventsResult = $db->query("SELECT COUNT(*) as total FROM events WHERE status = 'pending'");
$pendingEvents = $pendingEventsResult->fetch_assoc()['total'];

?>

<div class="container my-5">
    <h1 class="mb-4">Dashboard Admin</h1>

    <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Pengguna</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?php echo htmlspecialchars($totalUsers); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Total Event</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?php echo htmlspecialchars($totalEvents); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Event Menunggu Persetujuan</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?php echo htmlspecialchars($pendingEvents); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mt-4">
        <div class="card-header">
            Aksi Cepat
        </div>
        <div class="card-body">
            <a class="btn btn-primary" href="review_event.php">
                <i class="fas fa-check-double"></i> Review Event Baru
                <span class="badge bg-danger ms-1"><?php echo htmlspecialchars($pendingEvents); ?></span>
            </a>
            <a class="btn btn-secondary" href="manage_users.php">
                <i class="fas fa-users-cog"></i> Kelola Pengguna
            </a>
            <a class="btn btn-secondary" href="manage_events.php">
                <i class="fas fa-calendar-day"></i> Kelola Semua Event
            </a>
        </div>
    </div>
</div>

<?php
// Menutup koneksi database dan menyertakan footer
$db->close();
require_once '../templates/footer.php';
?>