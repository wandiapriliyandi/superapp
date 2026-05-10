<?= $this->extend('Ppdb\Views\public\layout') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6 text-center">
        <div class="card border-0 shadow-lg p-5">
            <div class="display-1 text-danger mb-4"><i class="bi bi-lock-fill"></i></div>
            <h2 class="fw-bold mb-3">Pendaftaran Ditutup</h2>
            <p class="text-muted mb-4">Mohon maaf, saat ini tidak ada gelombang pendaftaran yang sedang dibuka.</p>
            <p>Silakan hubungi sekretariat atau cek kembali di lain waktu.</p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
