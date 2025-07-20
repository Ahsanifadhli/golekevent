<?php
require_once '../config.php';

// Pengecekan akses, pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

require_once '../templates/header.php';

// Mengambil user_id dari session
$user_id = $_SESSION['user_id'];

// Mengambil data tiket dengan aman menggunakan prepared statement
$stmt = $db->prepare("
    SELECT 
        e.nama_event, 
        e.tanggal_event, 
        e.lokasi, 
        r.kode_tiket 
    FROM 
        registrations r
    JOIN 
        events e ON r.event_id = e.id 
    WHERE 
        r.user_id = ?
    ORDER BY 
        e.tanggal_event DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container my-5">
    <h1 class="mb-4">Tiket Saya</h1>

    <!-- ======================================================= -->
    <!-- BAGIAN BARU: Menampilkan pesan sukses dari session -->
    <!-- ======================================================= -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
                echo $_SESSION['flash_message']; 
                unset($_SESSION['flash_message']); // Hapus pesan setelah ditampilkan
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <!-- ======================================================= -->

    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($ticket = $result->fetch_assoc()): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header text-center">
                            <h5 class="mb-0"><?php echo htmlspecialchars($ticket['nama_event']); ?></h5>
                        </div>
                        <div class="card-body text-center">
                            <p><strong>Kode Tiket:</strong><br>
                                <span class="font-monospace"><?php echo htmlspecialchars($ticket['kode_tiket']); ?></span>
                            </p>
                            <!-- Menampilkan gambar barcode -->
                            <img src="../uploads/barcodes/<?php echo htmlspecialchars($ticket['kode_tiket']); ?>.png" class="img-fluid my-3" alt="Barcode Tiket">
                        </div>
                        <div class="card-footer text-muted">
                            <small>
                                <i class="fas fa-calendar-alt"></i> <?php echo date('d F Y', strtotime($ticket['tanggal_event'])); ?><br>
                                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($ticket['lokasi']); ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    Anda belum memiliki tiket. Silakan daftar ke salah satu event kami!
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$stmt->close();
$db->close();
require_once '../templates/footer.php';
?>
