<?php
session_start();
require_once '../config.php';

// 1. Pengecekan akses dan session yang benar
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'panitia') {
    die("Akses ditolak. Anda bukan panitia.");
}

// 2. Memproses form hanya jika metodenya POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 3. Mengambil semua data dari form dengan nama yang benar
    $user_id = $_SESSION['user_id'];
    $nama_kegiatan = trim($_POST['nama_kegiatan']);
    $jenis_kegiatan = trim($_POST['jenis_kegiatan']);
    $deskripsi = trim($_POST['deskripsi']);
    $lokasi = trim($_POST['lokasi']);
    $tanggal = trim($_POST['tanggal']);
    
    // --- Proses Upload File dengan Aman ---
    
    $upload_dir_brosur = '../uploads/brochures/';
    $upload_dir_proposal = '../uploads/proposals/';
    
    // Pastikan direktori ada, jika tidak, buat
    if (!is_dir($upload_dir_brosur)) { mkdir($upload_dir_brosur, 0755, true); }
    if (!is_dir($upload_dir_proposal)) { mkdir($upload_dir_proposal, 0755, true); }

    // Proses Brosur
    $brosur_file = $_FILES['gambar_brosur'];
    $brosur_ext = strtolower(pathinfo($brosur_file['name'], PATHINFO_EXTENSION));
    $allowed_brosur_ext = ['jpg', 'jpeg', 'png'];
    if (!in_array($brosur_ext, $allowed_brosur_ext) || $brosur_file['size'] > 2097152) { // 2MB
        die("Error: Format file brosur harus JPG/PNG dan ukuran di bawah 2MB.");
    }
    $nama_file_brosur = uniqid('brosur_', true) . '.' . $brosur_ext;
    move_uploaded_file($brosur_file['tmp_name'], $upload_dir_brosur . $nama_file_brosur);

    // Proses Proposal
    $proposal_file = $_FILES['surat_proposal'];
    $proposal_ext = strtolower(pathinfo($proposal_file['name'], PATHINFO_EXTENSION));
    $allowed_proposal_ext = ['pdf', 'doc', 'docx'];
    if (!in_array($proposal_ext, $allowed_proposal_ext) || $proposal_file['size'] > 5242880) { // 5MB
        die("Error: Format file proposal harus PDF/DOC/DOCX dan ukuran di bawah 5MB.");
    }
    $nama_file_proposal = uniqid('proposal_', true) . '.' . $proposal_ext;
    move_uploaded_file($proposal_file['tmp_name'], $upload_dir_proposal . $nama_file_proposal);

    // --- Menyimpan data event ke database ---
    
    // 4. Query SQL dan bind_param yang benar
    $stmt = $db->prepare("INSERT INTO events (user_id, nama_event, deskripsi, tanggal_event, lokasi, file_proposal, brochure_file) VALUES (?, ?, ?, ?, ?, ?, ?)");
    // Tipe data: i (integer), s (string), s, s, s, s, s
    $stmt->bind_param("issssss", $user_id, $nama_kegiatan, $deskripsi, $tanggal, $lokasi, $nama_file_proposal, $nama_file_brosur);

    if ($stmt->execute()) {
        // Redirect ke dashboard dengan pesan sukses
        $_SESSION['flash_message'] = "Event berhasil diajukan dan sedang menunggu review dari admin.";
        header("Location: dashboard.php");
        exit();
    } else {
        die("Gagal menyimpan data event ke database.");
    }
}
?>