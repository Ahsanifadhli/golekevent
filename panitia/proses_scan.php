<?php
require_once '../config.php';

header('Content-Type: application/json');

function send_json_response($success, $message, $extraData = []) {
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $extraData));
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'panitia') {
    send_json_response(false, 'Akses ditolak.');
}

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['kode_tiket'], $_POST['event_id'])) {
    send_json_response(false, 'Permintaan tidak valid.');
}

$kode_tiket = trim($_POST['kode_tiket']);
$event_id = intval($_POST['event_id']);

// Cek tiket di database
$stmt = $db->prepare("SELECT id, status_kehadiran, user_id FROM registrations WHERE kode_tiket = ? AND event_id = ?");
$stmt->bind_param("si", $kode_tiket, $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    send_json_response(false, 'Tiket tidak valid untuk event ini.');
}

$registration = $result->fetch_assoc();
$stmt->close();

if ($registration['status_kehadiran'] === 'sudah_hadir') {
    send_json_response(false, 'Error: Tiket ini sudah di-scan sebelumnya.');
}

// Update status kehadiran
$update_stmt = $db->prepare("UPDATE registrations SET status_kehadiran = 'sudah_hadir', updated_at = NOW() WHERE id = ?");
$update_stmt->bind_param("i", $registration['id']);

if ($update_stmt->execute()) {
    // Ambil nama dan email peserta
    $user_stmt = $db->prepare("SELECT nama, email FROM users WHERE id = ?");
    $user_stmt->bind_param("i", $registration['user_id']);
    $user_stmt->execute();
    $user_data = $user_stmt->get_result()->fetch_assoc();
    $user_stmt->close();

    // Hitung ulang statistik
    $stats_stmt = $db->prepare("
        SELECT 
            (SELECT COUNT(*) FROM registrations WHERE event_id = ?) as total,
            (SELECT COUNT(*) FROM registrations WHERE event_id = ? AND status_kehadiran = 'sudah_hadir') as hadir
    ");
    $stats_stmt->bind_param("ii", $event_id, $event_id);
    $stats_stmt->execute();
    $updated_stats = $stats_stmt->get_result()->fetch_assoc();
    $stats_stmt->close();

    send_json_response(true, 'Check-in Berhasil!', [
        'participantName' => $user_data['nama'], 
        'participantEmail' => $user_data['email'], 
        'updatedStats' => $updated_stats
    ]);
} else {
    send_json_response(false, 'Gagal melakukan check-in di database.');
}

$update_stmt->close();
$db->close();
?>
