<?php

require_once '../config.php';

// Pengecekan akses khusus admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

require_once '../templates/header.php';

// Mengambil semua event, diurutkan dari yang terbaru
$stmt = $db->prepare("SELECT e.id, e.nama_event, e.tanggal_event, e.status, u.nama AS nama_panitia 
                         FROM events e 
                         JOIN users u ON e.user_id = u.id 
                         ORDER BY e.created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container my-5">
    <h1 class="mb-4">Kelola Semua Event</h1>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Event</th>
                        <th>Panitia</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row["nama_event"]); ?></td>
                                <td><?php echo htmlspecialchars($row["nama_panitia"]); ?></td>
                                <td><?php echo date('d M Y', strtotime($row["tanggal_event"])); ?></td>
                                <td>
                                    <?php
                                        $status_class = '';
                                        switch ($row['status']) {
                                            case 'approved':
                                                $status_class = 'bg-success';
                                                break;
                                            case 'pending':
                                                $status_class = 'bg-warning';
                                                break;
                                            case 'rejected':
                                                $status_class = 'bg-danger';
                                                break;
                                        }
                                        echo "<span class='badge {$status_class}'>" . htmlspecialchars($row['status']) . "</span>";
                                    ?>
                                </td>
                                <td class="text-end">
                                    <a href="edit_event.php?id=<?php echo $row["id"]; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="hapus_event.php?id=<?php echo $row["id"]; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">Belum ada event yang dibuat.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$stmt->close();
$db->close();
require_once '../templates/footer.php';
?>