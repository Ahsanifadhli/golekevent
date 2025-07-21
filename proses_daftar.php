<?php
// Menggunakan namespace untuk PHPMailer dan QR Code Generator
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Panggil config.php (yang berisi session_start() dan autoload.php) di paling atas
require_once 'config.php';

// Pengecekan session, pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Pastikan ini adalah request POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Ambil dan sanitasi data dari form dan session
    $event_id = intval($_POST['event_id']);
    $user_id = $_SESSION['user_id'];
    $custom_fields = isset($_POST['custom_fields']) ? $_POST['custom_fields'] : [];
    $custom_data_json = json_encode($custom_fields);
    $kode_tiket = 'GEV-' . strtoupper(bin2hex(random_bytes(6)));

    // 2. Simpan pendaftaran ke database
    $stmt = $db->prepare("INSERT INTO registrations (event_id, user_id, kode_tiket, custom_data) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $event_id, $user_id, $kode_tiket, $custom_data_json);

    if ($stmt->execute()) {
        $stmt->close();

        // 3. Generate QR Code (Barcode Kotak)
        try {
            // PERBAIKAN: Menggunakan sintaks baru untuk membuat QR Code
            $qr_code = new QrCode($kode_tiket);
            $writer = new PngWriter();
            $result = $writer->write($qr_code);

            // Path untuk menyimpan gambar QR Code
            $qrcode_dir = 'uploads/barcodes/';
            if (!is_dir($qrcode_dir)) {
                mkdir($qrcode_dir, 0755, true);
            }
            $qrcode_path = $qrcode_dir . $kode_tiket . '.png';
            
            // Simpan gambar QR Code sebagai file
            $result->saveToFile($qrcode_path);

        } catch (Exception $e) {
            error_log("Gagal membuat QR Code: " . $e->getMessage());
            $qrcode_path = null;
        }

        // 4. Kirim Email Notifikasi
        try {
            // Ambil data nama dan email pengguna untuk dikirim email
            $stmt_user = $db->prepare("SELECT nama, email FROM users WHERE id = ?");
            $stmt_user->bind_param("i", $user_id);
            $stmt_user->execute();
            $user_data = $stmt_user->get_result()->fetch_assoc();
            $stmt_user->close();

            if ($user_data) {
                $nama_pendaftar = $user_data['nama'];
                $email_pendaftar = $user_data['email'];

                $mail = new PHPMailer(true);
                // Konfigurasi Server SMTP Hostinger
                $mail->isSMTP();
                $mail->Host       = '';
                $mail->SMTPAuth   = true;
                $mail->Username   = ''; // GANTI DENGAN EMAIL ANDA
                $mail->Password   = '';     // GANTI DENGAN PASSWORD ANDA
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;

                // Pengirim dan Penerima
                $mail->setFrom('', 'GolekEvent');
                $mail->addAddress($email_pendaftar, $nama_pendaftar);

                // Lampiran
                if ($qrcode_path && file_exists($qrcode_path)) {
                    $mail->addAttachment($qrcode_path);
                }

                // Konten Email
                $mail->isHTML(true);
                $mail->Subject = 'Tiket Event Anda - ' . $kode_tiket;
                $mail->Body    = "Halo <b>$nama_pendaftar</b>,<br><br>Pendaftaran Anda berhasil! Ini adalah kode tiket Anda: <b>$kode_tiket</b>.<br><br>Silakan tunjukkan QR Code di lampiran email ini saat check-in.<br><br>Terima kasih,<br>Tim GolekEvent";

                $mail->send();
            }
        } catch (Exception $e) {
            error_log("Email tidak terkirim: {$mail->ErrorInfo}");
        }

        // 5. Redirect ke halaman tiket dengan pesan sukses
        $_SESSION['flash_message'] = "Pendaftaran berhasil! Tiket QR Code Anda telah dikirim ke email.";
        header('Location: peserta/tiket_saya.php');
        exit;

    } else {
        $_SESSION['flash_message'] = "Gagal mendaftar. Anda mungkin sudah terdaftar di event ini.";
        header('Location: daftar.php?id=' . $event_id);
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>
