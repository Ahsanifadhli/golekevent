<?php
require_once '../config.php';

// Pengecekan akses, pastikan hanya panitia yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'panitia') {
    header('Location: ../login.php');
    exit();
}

require_once '../templates/header.php';

// Mengambil data event dari database
$panitia_id = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT id, nama_event, tanggal_event, status FROM events WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $panitia_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container my-5">
    <h1 class="text-center mb-4">Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h1>

    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <a href="buat_event.php" class="btn btn-primary mb-4"><i class="fas fa-plus-circle"></i> Buat Event Baru</a>

    <div class="card shadow">
        <div class="card-header">
            <h2>Daftar Event Anda</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nama Event</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($event = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($event['nama_event']); ?></td>
                                    <td><?php echo date('d F Y', strtotime($event['tanggal_event'])); ?></td>
                                    <td>
                                        <?php
                                            $status = $event['status'];
                                            $badge_class = 'bg-secondary';
                                            if ($status == 'approved') $badge_class = 'bg-success';
                                            if ($status == 'pending') $badge_class = 'bg-warning text-dark';
                                            if ($status == 'rejected') $badge_class = 'bg-danger';
                                            echo "<span class='badge {$badge_class}'>" . ucfirst($status) . "</span>";
                                        ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="kelola_event.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-secondary">Kelola</a>
                                        <?php if ($status == 'approved'): ?>
                                            <!-- Tombol Scan hanya muncul jika event disetujui -->
                                            <a href="scan_tiket.php?event_id=<?php echo $event['id']; ?>" class="btn btn-sm btn-info">Scan Tiket</a>
                                        <?php endif; ?>
                                        <a href="hapus_event.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Anda belum membuat event apapun.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$stmt->close();
$db->close();
require_once '../templates/footer.php';
?>
