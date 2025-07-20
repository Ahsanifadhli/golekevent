<?php

require_once '../config.php';

// 1. Pengecekan akses, pastikan hanya panitia yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'panitia') {
    die("Akses ditolak.");
}

// 2. Memproses form hanya jika metodenya POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 3. Validasi input yang diterima dari form
    if (!isset($_POST['event_id'], $_POST['field_label'], $_POST['field_type'])) {
        die("Data form tidak lengkap.");
    }

    $event_id = intval($_POST['event_id']);
    $field_label = trim($_POST['field_label']);
    $field_type = trim($_POST['field_type']);
    $field_options = isset($_POST['field_options']) ? trim($_POST['field_options']) : NULL;

    // Pastikan panitia hanya bisa menambah field ke event miliknya sendiri
    $user_id = $_SESSION['user_id'];
    $stmt_check = $db->prepare("SELECT id FROM events WHERE id = ? AND user_id = ?");
    $stmt_check->bind_param("ii", $event_id, $user_id);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows === 0) {
        die("Anda tidak memiliki hak akses untuk event ini.");
    }
    $stmt_check->close();


    if (empty($field_label) || empty($field_type)) {
        die("Nama kolom dan tipe kolom wajib diisi.");
    }
    
    if ($field_type === 'dropdown' && empty($field_options)) {
        die("Pilihan jawaban wajib diisi untuk tipe kolom dropdown.");
    }

    // 4. Menyimpan data ke database menggunakan prepared statement
    $sql = "INSERT INTO event_form_fields (event_id, field_label, field_type, field_options) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("isss", $event_id, $field_label, $field_type, $field_options);

    if ($stmt->execute()) {
        $_SESSION['flash_message'] = "Kolom baru berhasil ditambahkan.";
    } else {
        $_SESSION['flash_message'] = "Gagal menambahkan kolom baru.";
    }
    $stmt->close();
    $db->close();

    // 5. Redirect kembali ke halaman kelola event
    header("Location: kelola_event.php?id=" . $event_id);
    exit();

} else {
    // Jika file diakses langsung tanpa metode POST, redirect ke dashboard
    header("Location: dashboard.php");
    exit();
}
?>