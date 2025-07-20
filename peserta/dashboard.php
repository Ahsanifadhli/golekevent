<?php
// Panggil config.php (yang sudah berisi session_start()) di paling atas
require_once '../config.php';

// Pengecekan akses, pastikan pengguna sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'peserta') {
    header('Location: ../login.php');
    exit();
}

// Panggil header setelah semua logika PHP selesai
require_once '../templates/header.php';

$user_id = $_SESSION['user_id'];

// --- Logika untuk Mengambil Data Statistik dengan Aman ---

// 1. Menghitung event yang SUDAH dihadiri
$stmt_attended = $db->prepare("SELECT COUNT(*) as total FROM registrations WHERE user_id = ? AND status_kehadiran = 'sudah_hadir'");
$stmt_attended->bind_param("i", $user_id);
$stmt_attended->execute();
$attended_events = $stmt_attended->get_result()->fetch_assoc()['total'];
$stmt_attended->close();

// 2. Menghitung tiket untuk event yang AKAN datang (belum dihadiri)
$stmt_upcoming = $db->prepare("SELECT COUNT(*) as total FROM registrations WHERE user_id = ? AND status_kehadiran = 'belum_hadir'");
$stmt_upcoming->bind_param("i", $user_id);
$stmt_upcoming->execute();
$upcoming_tickets = $stmt_upcoming->get_result()->fetch_assoc()['total'];
$stmt_upcoming->close();

// --- Logika BARU: Mengambil daftar event yang tersedia ---
$available_events_stmt = $db->prepare("
    SELECT e.id, e.nama_event, e.tanggal_event, e.lokasi, e.brochure_file
    FROM events e
    WHERE e.status = 'approved' AND e.id NOT IN (
        SELECT r.event_id FROM registrations r WHERE r.user_id = ?
    )
    ORDER BY e.tanggal_event ASC
    LIMIT 3
");
$available_events_stmt->bind_param("i", $user_id);
$available_events_stmt->execute();
$available_events_result = $available_events_stmt->get_result();
$available_events_stmt->close();
?>

<div class="container my-5">
    <h1 class="mb-4">Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h1>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title text-muted">Event yang Telah Dihadiri</h5>
                    <p class="card-text display-4 fw-bold"><?php echo $attended_events; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title text-muted">Tiket Untuk Event Mendatang</h5>
                    <p class="card-text display-4 fw-bold"><?php echo $upcoming_tickets; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="d-grid gap-2 mt-4">
        <a href="tiket_saya.php" class="btn btn-primary btn-lg">Lihat Semua Tiket Saya</a>
    </div>

    <!-- Bagian BARU: Daftar Event Tersedia -->
    <hr class="my-5">
    <h2 class="mb-4">Event Tersedia Untuk Anda</h2>
    <div class="row">
        <?php if ($available_events_result->num_rows > 0): ?>
            <?php while($event = $available_events_result->fetch_assoc()): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php $image_path = '../uploads/brochures/' . htmlspecialchars($event["brochure_file"]); ?>
                        <img src="<?php echo $image_path; ?>" class="card-img-top" alt="Brosur Event" style="height: 200px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($event['nama_event']); ?></h5>
                            <p class="card-text text-muted"><small>
                                <?php echo date('d F Y', strtotime($event['tanggal_event'])); ?><br>
                                <?php echo htmlspecialchars($event['lokasi']); ?>
                            </small></p>
                            <a href="../detail_event.php?id=<?php echo $event['id']; ?>" class="btn btn-outline-primary mt-auto">Lihat Detail & Daftar</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center text-muted">Saat ini tidak ada event baru yang tersedia untuk Anda.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$db->close();
require_once '../templates/footer.php';
?>
