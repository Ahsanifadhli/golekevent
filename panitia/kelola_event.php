<?php

require_once '../config.php';

// Pengecekan akses, pastikan hanya panitia yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'panitia') {
    header('Location: ../login.php');
    exit();
}

// Validasi ID event dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error: ID event tidak valid.");
}
$event_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Mengambil data event dari database dan mendefinisikan variabel $event
$stmt = $db->prepare("SELECT * FROM events WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $event_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: Event tidak ditemukan atau Anda tidak memiliki hak akses.");
}

// Variabel $event sekarang sudah ada isinya
$event = $result->fetch_assoc();
$stmt->close();

// Logika untuk mengambil field form yang sudah ada untuk event ini
$stmt_fields = $db->prepare("SELECT field_label, field_type FROM event_form_fields WHERE event_id = ?");
$stmt_fields->bind_param("i", $event_id);
$stmt_fields->execute();
$form_fields_result = $stmt_fields->get_result();
$stmt_fields->close();

// Memanggil header
require_once '../templates/header.php';
?>

<div class="container my-5">
    <h1 class="mb-3">Kelola Event: <?php echo htmlspecialchars($event['nama_event']); ?></h1>
    <a href="dashboard.php" class="btn btn-secondary mb-4">← Kembali ke Dashboard</a>

    <div class="card shadow">
        <div class="card-header">
            <h3>Status Persetujuan</h3>
        </div>
        <div class="card-body">
            <?php 
                // Logika kondisional untuk menampilkan status
                switch ($event['status']) {
                    case 'rejected':
                        echo "<div class='alert alert-danger'>";
                        echo "<h4 class='alert-heading'>Event Ditolak</h4>";
                        echo "<p>Maaf, event Anda ditolak oleh admin. Berikut adalah catatannya:</p>";
                        echo "<hr><p class='mb-0'>" . htmlspecialchars($event['rejection_note']) . "</p>";
                        echo "</div>";
                        break;
                    case 'pending':
                        echo "<div class='alert alert-warning'>";
                        echo "<h4 class='alert-heading'>Menunggu Persetujuan</h4>";
                        echo "<p class='mb-0'>Event Anda sedang ditinjau oleh Admin. Silakan cek kembali nanti.</p>";
                        echo "</div>";
                        break;
                    case 'approved':
                        echo "<div class='alert alert-success'>";
                        echo "<h4 class='alert-heading'>Event Disetujui!</h4>";
                        echo "<p class='mb-0'>Selamat, event Anda telah disetujui. Sekarang Anda bisa membuat form pendaftaran untuk peserta di bawah ini.</p>";
                        echo "</div>";
                        break;
                }
            ?>
        </div>
    </div>

    <?php if ($event['status'] == 'approved'): ?>
    <div class="card shadow mt-4">
        <div class="card-header">
            <h3>Buat Form Pendaftaran Peserta</h3>
        </div>
        <div class="card-body">
            
            <h5>Kolom Kustom yang Sudah Ada:</h5>
            <?php if ($form_fields_result->num_rows > 0): ?>
                <table class="table table-sm table-striped">
                    <thead><tr><th>Nama Kolom</th><th>Tipe</th></tr></thead>
                    <tbody>
                        <?php while($field = $form_fields_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($field['field_label']); ?></td>
                                <td><?php echo htmlspecialchars($field['field_type']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted">Belum ada kolom kustom yang ditambahkan.</p>
            <?php endif; ?>

            <hr class="my-4">

            <h5>Tambah Kolom Baru:</h5>
            <form action="proses_tambah_field.php" method="post">
                <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                
                <div class="mb-3">
                    <label for="field_label" class="form-label">Nama Kolom / Label</label>
                    <input type="text" class="form-control" name="field_label" required>
                </div>
                
                <div class="mb-3">
                    <label for="field_type" class="form-label">Tipe Kolom</label>
                    <select class="form-select" name="field_type" id="fieldTypeSelect" required>
                        <option value="text">Text (Satu Baris)</option>
                        <option value="textarea">Textarea (Paragraf)</option>
                        <option value="dropdown">Dropdown (Pilihan)</option>
                    </select>
                </div>

                <div class="mb-3" id="fieldOptionsContainer" style="display: none;">
                    <label for="field_options" class="form-label">Pilihan Jawaban (pisahkan dengan koma)</label>
                    <input type="text" class="form-control" name="field_options" placeholder="Contoh: S,M,L,XL">
                </div>
                
                <button type="submit" class="btn btn-primary">Tambah Kolom</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

</div>

<script>
// JavaScript untuk menampilkan/menyembunyikan input 'Pilihan Jawaban'
document.getElementById('fieldTypeSelect').addEventListener('change', function() {
    var container = document.getElementById('fieldOptionsContainer');
    if (this.value === 'dropdown') {
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
    }
});
</script>

<?php
$db->close();
require_once '../templates/footer.php';
?>