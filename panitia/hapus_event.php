<?php
// Panggil config.php (yang berisi session_start()) di paling atas
require_once '../config.php';

// 1. Pengecekan akses, pastikan hanya panitia yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'panitia') {
    die("Akses ditolak.");
}

// 2. Validasi ID event dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error: ID event tidak valid.");
}
$event_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// 3. Ambil nama file sebelum dihapus dari database
$stmt_select = $db->prepare("SELECT brochure_file, file_proposal FROM events WHERE id = ? AND user_id = ?");
$stmt_select->bind_param("ii", $event_id, $user_id);
$stmt_select->execute();
$result = $stmt_select->get_result();

if ($result->num_rows === 1) {
    $event_files = $result->fetch_assoc();
    $stmt_select->close();

    // 4. Hapus file fisik dari server (brosur dan proposal)
    $brochure_path = '../uploads/brochures/' . $event_files['brochure_file'];
    $proposal_path = '../uploads/proposals/' . $event_files['file_proposal'];

    if (file_exists($brochure_path)) {
        unlink($brochure_path);
    }
    if (file_exists($proposal_path)) {
        unlink($proposal_path);
    }

    // 5. Hapus record event dari database
    // Pengecekan user_id di sini adalah lapisan keamanan tambahan
    $stmt_delete = $db->prepare("DELETE FROM events WHERE id = ? AND user_id = ?");
    $stmt_delete->bind_param("ii", $event_id, $user_id);

    if ($stmt_delete->execute()) {
        $_SESSION['flash_message'] = "Event berhasil dihapus.";
    } else {
        $_SESSION['flash_message'] = "Gagal menghapus event.";
    }
    $stmt_delete->close();

} else {
    // Jika event tidak ditemukan atau bukan milik panitia ini
    $_SESSION['flash_message'] = "Error: Event tidak ditemukan atau Anda tidak memiliki hak akses.";
}

// 6. Redirect kembali ke halaman dashboard panitia
header("Location: dashboard.php");
exit();
?>
