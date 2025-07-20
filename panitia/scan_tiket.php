<?php
require_once '../config.php';

// Pengecekan akses, pastikan hanya panitia yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'panitia') {
    header('Location: ../login.php');
    exit();
}

// Ambil event_id dari URL untuk validasi dan statistik awal
if (!isset($_GET['event_id']) || !is_numeric($_GET['event_id'])) {
    die("Error: ID Event tidak valid.");
}
$event_id = intval($_GET['event_id']);

// --- Ambil Data Awal untuk Statistik ---
// Total Pendaftar
$stmt_total = $db->prepare("SELECT COUNT(*) as total FROM registrations WHERE event_id = ?");
$stmt_total->bind_param("i", $event_id);
$stmt_total->execute();
$total_pendaftar = $stmt_total->get_result()->fetch_assoc()['total'] ?? 0;
$stmt_total->close();

// Total yang Sudah Hadir
$stmt_hadir = $db->prepare("SELECT COUNT(*) as total FROM registrations WHERE event_id = ? AND status_kehadiran = 'sudah_hadir'");
$stmt_hadir->bind_param("i", $event_id);
$stmt_hadir->execute();
$total_hadir = $stmt_hadir->get_result()->fetch_assoc()['total'] ?? 0;
$stmt_hadir->close();

// Aktivitas Terakhir
$stmt_activity = $db->prepare("
    SELECT u.nama, u.email 
    FROM registrations r
    JOIN users u ON r.user_id = u.id 
    WHERE r.event_id = ? AND r.status_kehadiran = 'sudah_hadir' 
    ORDER BY r.updated_at DESC LIMIT 5
");
$stmt_activity->bind_param("i", $event_id);
$stmt_activity->execute();
$recent_activities = $stmt_activity->get_result();
$stmt_activity->close();

require_once '../templates/header.php';
?>
<!-- CSS tema gelap sudah dihapus, kita akan menggunakan tema terang standar Bootstrap -->

<div class="container-fluid my-4">
    <div class="row">
        <!-- Kolom Kiri: Scanner dan Aktivitas -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-camera me-2"></i>Check-in Peserta (Kamera Aktif)</h5>
                </div>
                <div class="card-body">
                    <div id="qr-reader" style="width:100%;"></div>
                    <div id="scan-result" class="mt-3"></div>
                </div>
            </div>
            <div class="card shadow">
                 <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Aktivitas Check-in Terbaru</h5>
                </div>
                <div class="card-body">
                    <ul id="scanned-list" class="list-group list-group-flush">
                        <?php if ($recent_activities->num_rows > 0): ?>
                            <?php while($activity = $recent_activities->fetch_assoc()): ?>
                                <li class="list-group-item">
                                    <strong><?php echo htmlspecialchars($activity['nama']); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($activity['email']); ?></small>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li class="list-group-item text-muted" id="no-activity">Belum ada peserta yang check-in.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Statistik -->
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistik Kehadiran</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <span>Total Pendaftar</span>
                        <span id="total-pendaftar" class="fw-bold"><?php echo $total_pendaftar; ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Kehadiran</span>
                        <span id="total-hadir" class="fw-bold"><?php echo $total_hadir; ?> / <?php echo $total_pendaftar; ?></span>
                    </div>
                    <?php $persentase = ($total_pendaftar > 0) ? ($total_hadir / $total_pendaftar) * 100 : 0; ?>
                    <div class="progress mt-2" style="height: 20px;">
                        <div id="progress-bar" class="progress-bar" role="progressbar" style="width: <?php echo $persentase; ?>%;" aria-valuenow="<?php echo $persentase; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo round($persentase); ?>%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    function onScanSuccess(decodedText, decodedResult) {
        let resultContainer = document.getElementById('scan-result');
        resultContainer.innerHTML = `<div class="alert alert-info">Memvalidasi: ${decodedText}...</div>`;

        const formData = new URLSearchParams();
        formData.append('kode_tiket', decodedText);
        formData.append('event_id', '<?php echo $event_id; ?>');

        fetch('proses_scan.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                
                // Update Statistik
                document.getElementById('total-hadir').textContent = `${data.updatedStats.hadir} / ${data.updatedStats.total}`;
                let newPercentage = (data.updatedStats.total > 0) ? (data.updatedStats.hadir / data.updatedStats.total) * 100 : 0;
                let progressBar = document.getElementById('progress-bar');
                progressBar.style.width = newPercentage + '%';
                progressBar.textContent = Math.round(newPercentage) + '%';

                // Update Daftar Aktivitas dengan Nama dan Email
                const scannedList = document.getElementById('scanned-list');
                const newEntry = document.createElement('li');
                newEntry.className = 'list-group-item';
                newEntry.innerHTML = `<strong>${data.participantName}</strong><br><small class="text-muted">${data.participantEmail}</small>`;
                
                const noActivity = document.getElementById('no-activity');
                if (noActivity) {
                    noActivity.remove();
                }
                
                scannedList.prepend(newEntry);

            } else {
                resultContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        });
    }

    let html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: {width: 250, height: 250} });
    html5QrcodeScanner.render(onScanSuccess, (error) => {});
</script>

<?php
require_once '../templates/footer.php';
?>
