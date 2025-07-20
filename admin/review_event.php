<?php

require_once '../config.php';

// Pengecekan akses khusus admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

require_once '../templates/header.php';

// PERUBAHAN 1: Tambahkan 'e.file_proposal' ke dalam query SELECT
$stmt = $db->prepare("SELECT e.id AS event_id, e.nama_event, e.tanggal_event, e.file_proposal, u.nama AS nama_panitia 
                         FROM events e 
                         JOIN users u ON e.user_id = u.id 
                         WHERE e.status = 'pending'
                         ORDER BY e.created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container my-5">
    <h1 class="mb-4">Review Event Baru</h1>

    <div class="card shadow">
        <div class="card-header">
            Event yang Menunggu Persetujuan
        </div>
        <div class="card-body">
            <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Event</th>
                                <th>Nama Panitia</th>
                                <th>Tanggal Event</th>
                                <th>Proposal</th> <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row["nama_event"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["nama_panitia"]); ?></td>
                                    <td><?php echo date('d F Y', strtotime($row["tanggal_event"])); ?></td>
                                    <td>
                                        <a href="../uploads/proposals/<?php echo htmlspecialchars($row['file_proposal']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            Lihat File
                                        </a>
                                    </td>
                                    <td class="text-end">
                                        <a href="proses_review.php?id=<?php echo $row["event_id"]; ?>&action=approve" class="btn btn-sm btn-success">Setujui</a>
                                        <a href="proses_review.php?id=<?php echo $row["event_id"]; ?>&action=reject" class="btn btn-sm btn-danger">Tolak</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center">Tidak ada event yang menunggu persetujuan saat ini.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$stmt->close();
$db->close();
require_once '../templates/footer.php';
?>