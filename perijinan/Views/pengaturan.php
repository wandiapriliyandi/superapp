<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8 text-center py-5">
        <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="Coming Soon" style="max-height: 300px;" class="mb-4">
        <h2 class="fw-bold text-primary">Fitur Pengaturan Sedang Dikembangkan</h2>
        <p class="text-muted">Halaman ini nantinya akan digunakan untuk mengonfigurasi batas waktu izin, poin pelanggaran keterlambatan, dan daftar petugas pemberi izin.</p>
        <a href="<?= base_url('perijinan') ?>" class="btn btn-primary rounded-pill px-4 mt-3">Kembali ke Daftar Izin</a>
    </div>
</div>
<?= $this->endSection() ?>
