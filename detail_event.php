<?php
require_once 'config.php';

// Validasi ID event dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error: ID event tidak valid.");
}
$event_id = intval($_GET['id']);

// Mengambil data event dari database menggunakan prepared statement
// PERBAIKAN: Menggunakan nama kolom yang benar (e.g., nama_event, u.nama)
$stmt = $db->prepare("
    SELECT 
        e.nama_event, e.deskripsi, e.tanggal_event, e.lokasi, e.brochure_file, 
        u.nama AS nama_panitia, u.nama_organisasi
    FROM 
        events e
    JOIN 
        users u ON e.user_id = u.id
    WHERE 
        e.id = ? AND e.status = 'approved'
");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah event ditemukan dan statusnya 'approved'
if ($result->num_rows === 0) {
    // Agar tidak membocorkan informasi, tampilkan pesan 'tidak ditemukan'
    // baik jika ID salah maupun jika event belum disetujui.
    die("Event tidak ditemukan.");
}
$event = $result->fetch_assoc();
$stmt->close();

// Memanggil header SETELAH semua logika PHP selesai
require_once 'templates/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-6 mb-4">
            <img src="uploads/brochures/<?php echo htmlspecialchars($event['brochure_file']); ?>" class="img-fluid rounded shadow" alt="Brosur Event">
        </div>

        <div class="col-md-6">
            <h1 class="fw-bold"><?php echo htmlspecialchars($event['nama_event']); ?></h1>
            <hr>
            
            <h3 class="mt-4">Deskripsi</h3>
            <p><?php echo nl2br(htmlspecialchars($event['deskripsi'])); // nl2br untuk menjaga format paragraf ?></p>

            <h3 class="mt-4">Tanggal & Lokasi</h3>
            <p>
                <strong><i class="fas fa-calendar-alt"></i> Tanggal:</strong> <?php echo date('l, d F Y', strtotime($event['tanggal_event'])); ?><br>
                <strong><i class="fas fa-map-marker-alt"></i> Lokasi:</strong> <?php echo htmlspecialchars($event['lokasi']); ?>
            </p>

            <h3 class="mt-4">Informasi Panitia</h3>
            <p>
                <strong><i class="fas fa-user"></i> Nama:</strong> <?php echo htmlspecialchars($event['nama_panitia']); ?><br>
                <strong><i class="fas fa-building"></i> Organisasi:</strong> <?php echo htmlspecialchars($event['nama_organisasi']); ?>
            </p>

            <div class="d-grid gap-2 mt-5">
                <a href="daftar.php?id=<?php echo $event_id; ?>" class="btn btn-primary btn-lg">Daftar Sekarang</a>
            </div>
        </div>
    </div>
</div>

<?php
$db->close();
require_once 'templates/footer.php';
?>