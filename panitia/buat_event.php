<?php
session_start();

// Pengecekan akses, pastikan hanya panitia yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'panitia') {
    header('Location: ../login.php');
    exit();
}

// Menyertakan header
require_once '../templates/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h2 class="mb-0">Formulir Pengajuan Event Baru</h2>
                </div>
                <div class="card-body">
                    <p class="text-muted">Silakan isi detail event Anda. Semua event akan ditinjau oleh Admin sebelum dipublikasikan.</p>
                    
                    <form action="proses_buat_event.php" method="post" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label for="nama_kegiatan" class="form-label">Nama Kegiatan</label>
                            <input type="text" class="form-control" id="nama_kegiatan" name="nama_kegiatan" required>
                        </div>

                        <div class="mb-3">
                            <label for="jenis_kegiatan" class="form-label">Jenis Kegiatan</label>
                            <input type="text" class="form-control" id="jenis_kegiatan" name="jenis_kegiatan" placeholder="Contoh: Seminar, Workshop, Konser Amal" required>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="gambar_brosur" class="form-label">Gambar Brosur</label>
                            <input type="file" class="form-control" id="gambar_brosur" name="gambar_brosur" accept="image/jpeg, image/png" required>
                            <div class="form-text">Format yang diizinkan: .jpeg, .jpg, .png. Maksimal 2MB.</div>
                        </div>

                        <div class="mb-3">
                            <label for="lokasi" class="form-label">Lokasi</label>
                            <input type="text" class="form-control" id="lokasi" name="lokasi" required>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                        </div>

                        <div class="mb-3">
                            <label for="surat_proposal" class="form-label">Surat Proposal</label>
                            <input type="file" class="form-control" id="surat_proposal" name="surat_proposal" accept=".pdf,.doc,.docx" required>
                            <div class="form-text">Format yang diizinkan: .pdf, .doc, .docx. Maksimal 5MB.</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Ajukan Event untuk Direview</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Menyertakan footer
require_once '../templates/footer.php';
?>