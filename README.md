# GolekEvent

## Deskripsi
GolekEvent adalah platform manajemen event berbasis web yang dirancang untuk mempermudah panitia dalam membuat dan mengelola acara, serta memberikan kemudahan bagi peserta untuk menemukan dan mendaftar event. Fitur utama kami meliputi sistem persetujuan event oleh admin, pendaftaran peserta dengan form dinamis, dan sistem check-in berbasis scan QR Code.

## Fitur Utama
-   **Manajemen Peran:** Tiga peran pengguna (Admin, Panitia, Peserta) dengan dashboard masing-masing.
-   **Moderasi Event:** Admin memiliki kontrol penuh untuk menyetujui atau menolak event yang diajukan panitia.
-   **Form Pendaftaran Dinamis:** Panitia dapat membuat kolom form kustom untuk setiap event.
-   **Tiket & Notifikasi Otomatis:** Peserta otomatis menerima tiket dengan QR Code unik melalui email setelah mendaftar.
-   **Check-in Cepat:** Panitia dapat melakukan check-in peserta dengan mudah menggunakan fitur scan QR Code di lokasi acara.

## Teknologi yang Digunakan
-   **Backend:** PHP
-   **Database:** MySQL
-   **Frontend:** HTML, CSS, JavaScript, Bootstrap 5
-   **Library Pihak Ketiga:**
    -   `endroid/qr-code` (untuk membuat QR Code)
    -   `phpmailer/phpmailer` (untuk mengirim email notifikasi)
-   **Development Assistant:** IBM Granite (untuk akselerasi dan peningkatan kualitas kode)

## Instruksi Setup
1.  Clone repository ini.
2.  Salin file `config.example.php` dan ubah namanya menjadi `config.php`.
3.  Sesuaikan detail koneksi database di dalam `config.php`.
4.  Jalankan `composer install` di terminal untuk mengunduh semua library yang dibutuhkan.
5.  Impor file `database.sql` ke dalam database MySQL Anda.
6.  Jalankan file `buat_admin.php` sekali untuk membuat akun admin pertama, lalu segera hapus file tersebut.

## Penjelasan Dukungan AI (IBM Granite)
Dalam pengembangan proyek GolekEvent, IBM Granite digunakan sebagai asisten pemrograman untuk mempercepat dan meningkatkan kualitas kode di beberapa area kunci:

1.  **Desain Awal Database:** Saya memberikan rancangan tabel, dan IBM Granite membantu men-generate query `CREATE TABLE` dalam format SQL yang benar, lengkap dengan `FOREIGN KEY` dan tipe data yang sesuai.
2.  **Pembuatan Logika Backend:** Saya menggunakan AI untuk membuat draf awal logika PHP yang kompleks, seperti:
    -   Skrip untuk memproses registrasi dan login pengguna dengan standar keamanan (Prepared Statements dan `password_hash()`).
    -   Logika untuk sistem persetujuan event oleh admin.
    -   Skrip untuk memvalidasi tiket yang di-scan.
3.  **Debugging & Perbaikan:** Saat menghadapi error, saya memberikan potongan kode dan pesan error kepada AI untuk mendapatkan analisis dan saran perbaikan. Contohnya adalah saat memperbaiki error `Undefined variable` dan `Unknown column` di database.
4.  **Integrasi Library:** Saya meminta AI untuk memberikan contoh dasar cara mengintegrasikan library pihak ketiga seperti PHPMailer dan QR Code generator ke dalam alur pendaftaran.

Penggunaan AI ini secara signifikan mengurangi waktu yang dihabiskan untuk menulis kode boilerplate dan mencari solusi untuk masalah umum, sehingga saya bisa lebih fokus pada perancangan alur kerja aplikasi dan desain antarmuka.
