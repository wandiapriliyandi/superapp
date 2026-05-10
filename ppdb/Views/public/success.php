<?= $this->extend('Ppdb\Views\public\layout') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6 text-center">
        <div class="card border-0 shadow-lg p-5">
            <div class="display-1 text-success mb-4"><i class="bi bi-patch-check-fill"></i></div>
            <h2 class="fw-bold mb-3">Pendaftaran Berhasil!</h2>
            <p class="text-muted mb-4">Terima kasih, pendaftaran Anda telah kami terima dengan nomor pendaftaran:</p>
            
            <div class="bg-body-tertiary p-4 rounded-4 mb-4 border border-dashed">
                <h1 class="fw-bold text-primary mb-0"><?= $pendaftar['nomor_pendaftaran'] ?></h1>
            </div>

            <p class="mb-5">Silakan simpan nomor pendaftaran di atas. Admin kami akan menghubungi Anda melalui WhatsApp untuk tahap seleksi selanjutnya.</p>
            
            <a href="<?= base_url('ppdb/daftar') ?>" class="btn btn-outline-primary rounded-pill px-4">Kembali ke Halaman Utama</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
