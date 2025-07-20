<?php

require_once '../config.php';

// Pengecekan akses khusus admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak."); // Hentikan eksekusi jika bukan admin
}

// Validasi input dari URL
if (isset($_GET['id']) && isset($_GET['action'])) {
    $event_id = intval($_GET['id']);
    $action = $_GET['action'];

    // Tentukan status baru berdasarkan aksi
    $new_status = '';
    if ($action == 'approve') {
        $new_status = 'approved';
    } elseif ($action == 'reject') {
        $new_status = 'rejected';
    }

    // Lakukan update jika status baru valid
    if (!empty($new_status)) {
        $stmt = $db->prepare("UPDATE events SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $event_id);
        
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = "Status event berhasil diperbarui.";
        } else {
            $_SESSION['flash_message'] = "Gagal memperbarui status event.";
        }
        $stmt->close();
    }
}

// Redirect kembali ke halaman review
header("Location: review_event.php");
exit();
?>