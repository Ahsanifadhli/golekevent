<?php

require_once 'config.php';
require_once 'templates/header.php';
?>

<section class="text-center bg-dark text-white p-5">
    <div class="container">
        <h1 class="display-4 fw-bold">GolekEvent</h1>
        <p class="lead">Platform Event Terlengkap untuk Komunitas Anda</p>
    </div>
</section>

<section id="why-us" class="container my-5">
    <h2 class="text-center mb-5">Mengapa Memilih GolekEvent?</h2>
    <div class="row text-center">
        <div class="col-lg-3 col-md-6 mb-4">
            <i class="fas fa-check-circle fa-3x text-primary mb-3"></i>
            <h4>Terverifikasi & Aman</h4>
            <p class="text-muted">Semua event melalui proses verifikasi untuk menjamin keamanan dan kenyamanan Anda.</p>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <i class="fas fa-users fa-3x text-primary mb-3"></i>
            <h4>Fokus Komunitas</h4>
            <p class="text-muted">Dirancang khusus untuk mendukung event dari berbagai komunitas dan organisasi.</p>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <i class="fas fa-tasks fa-3x text-primary mb-3"></i>
            <h4>Manajemen Mudah</h4>
            <p class="text-muted">Dashboard intuitif untuk panitia mengelola event dari A sampai Z.</p>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <i class="fas fa-ticket-alt fa-3x text-primary mb-3"></i>
            <h4>Pendaftaran Cepat</h4>
            <p class="text-muted">Proses pendaftaran yang simpel dan tiket elektronik langsung dikirim.</p>
        </div>
    </div>
</section>

<section id="events" class="bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-4">Event Terbaru</h2>
        <div class="row">
            <?php
            // Mengambil 6 event terbaru yang sudah disetujui
            $sql = "SELECT id, nama_event, tanggal_event, lokasi, brochure_file FROM events WHERE status = 'approved' ORDER BY tanggal_event DESC LIMIT 6";
            $result = $db->query($sql);

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $image_path = 'uploads/brochures/' . htmlspecialchars($row["brochure_file"]);
                    echo '<div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <img src="' . $image_path . '" class="card-img-top" alt="Brosur ' . htmlspecialchars($row["nama_event"]) . '" style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">' . htmlspecialchars($row["nama_event"]) . '</h5>
                                <p class="card-text text-muted"><small>
                                    ' . date('d F Y', strtotime($row["tanggal_event"])) . ' <br>
                                    ' . htmlspecialchars($row["lokasi"]) . '
                                </small></p>
                                <a href="detail_event.php?id=' . $row["id"] . '" class="btn btn-primary mt-auto">Lihat Detail</a>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo '<div class="col-12"><p class="text-center">Belum ada event yang tersedia saat ini.</p></div>';
            }
            ?>
        </div>
    </div>
</section>

<section id="benefits" class="container my-5">
    <h2 class="text-center mb-4">Manfaat yang Anda Dapatkan</h2>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="p-4 border rounded-3 h-100">
                <h3 class="mb-3">Untuk Peserta</h3>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Pendaftaran event cepat dan mudah.</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Tiket elektronik dengan barcode.</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Temukan beragam event menarik.</li>
                </ul>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="p-4 border rounded-3 h-100 bg-light">
                <h3 class="mb-3">Untuk Panitia</h3>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Buat halaman event profesional.</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Dashboard manajemen pendaftar.</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Sistem check-in via scan barcode.</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Laporan data peserta lengkap.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="container text-center my-5">
    <h2 class="mb-4">Siap Memulai?</h2>
    <a href="register.php?role=peserta" class="btn btn-primary btn-lg mx-2">Saya Peserta</a>
    <a href="register.php?role=panitia" class="btn btn-outline-primary btn-lg mx-2">Saya Panitia</a>
</section>

<section id="faq" class="container my-5">
    <h2 class="text-center mb-4">Frequently Asked Questions (FAQ)</h2>
    <div class="accordion" id="faqAccordion">

        <!-- FAQ 1 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Apakah GolekEvent gratis digunakan?
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    Untuk peserta, platform ini 100% gratis. Untuk panitia, kami menyediakan paket gratis dengan fitur dasar dan paket berbayar untuk fitur yang lebih canggih (coming soon).
                </div>
            </div>
        </div>

        <!-- FAQ 2 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Bagaimana cara saya mendapatkan tiket setelah mendaftar?
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    Setelah pendaftaran berhasil, tiket elektronik beserta kode barcode unik akan otomatis dikirimkan ke alamat email yang Anda daftarkan.
                </div>
            </div>
        </div>

        <!-- FAQ 3 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    Apakah saya bisa mendapatkan berbagai event?
                </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    Info event yang tersedia di GolekEvent tidak mencakup kegiatan seperti LGBT, konser musik, dan sejenisnya.
                </div>
            </div>
        </div>

        <!-- FAQ 4 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFour">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                    Bagaimana panitia bisa membuat acara di website GolekEvent?
                </button>
            </h2>
            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    Silakan ajukan terlebih dahulu kepada pihak admin tentang kegiatan yang akan diadakan. Lampirkan brosur agenda dan surat proposal kegiatan.
                </div>
            </div>
        </div>

    </div>
</section>


<?php
$db->close();
require_once 'templates/footer.php';
?>