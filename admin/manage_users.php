<?php

require_once '../config.php';

// 1. Pengecekan session dan role yang benar
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// 2. Memanggil header SEBELUM menampilkan konten apa pun
require_once '../templates/header.php';

// 3. Menggunakan variabel koneksi dan nama kolom yang benar
$sql = "SELECT id, nama, email, role, nama_organisasi FROM users ORDER BY role, nama";
$result = $db->query($sql);
?>

<div class="container my-5">
    <h1 class="mb-4">Kelola Pengguna</h1>
    
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Nama Organisasi</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row["id"]; ?></td>
                                    <td><?php echo htmlspecialchars($row["nama"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["email"]); ?></td>
                                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row["role"]); ?></span></td>
                                    <td><?php echo htmlspecialchars($row["nama_organisasi"] ?? 'N/A'); ?></td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-primary btn-sm edit-btn" data-id="<?php echo $row["id"]; ?>">Edit</button>
                                        <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $row["id"]; ?>">Hapus</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data pengguna.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// Menutup koneksi database dan memanggil footer
$db->close();
require_once '../templates/footer.php';
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Fungsi untuk tombol Hapus
    $('.delete-btn').click(function() {
        const userId = $(this).data('id');
        if (confirm('Apakah Anda yakin ingin menghapus pengguna dengan ID ' + userId + '?')) {
            // Di sini Anda bisa menambahkan logika penghapusan via AJAX
            // Contoh: window.location.href = 'proses_hapus_user.php?id=' + userId;
            alert('Logika hapus untuk user ID ' + userId + ' dijalankan.');
        }
    });

    // Fungsi untuk tombol Edit
    $('.edit-btn').click(function() {
        const userId = $(this).data('id');
        // Di sini Anda bisa menambahkan logika untuk menampilkan form edit
        alert('Logika edit untuk user ID ' + userId + ' dijalankan.');
    });
});
</script>