<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex align-items-center">
            <a href="<?= base_url('monitoring') ?>" class="btn btn-light rounded-circle me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h3 class="fw-bold text-dark mb-0"><?= $title ?></h3>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 text-center">
                <h6 class="fw-bold mb-4">Demografi Berdasarkan Jenis Kelamin</h6>
                <div class="d-flex justify-content-center gap-5 mt-4">
                    <?php foreach($gender as $g): ?>
                    <div>
                        <h1 class="fw-bold mb-0 text-primary"><?= $g['jumlah'] ?></h1>
                        <p class="text-muted"><?= $g['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></p>
                        <i class="bi bi-person-fill fs-1 opacity-25"></i>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 opacity-75">Statistik Tambahan</h6>
                <p>Data demografi santri ini diambil secara real-time dari database akademik terpusat untuk memantau sebaran pendaftar dan santri aktif.</p>
                <hr class="bg-white opacity-25">
                <div class="d-flex justify-content-between align-items-center">
                    <span>Total Keseluruhan</span>
                    <h3 class="fw-bold mb-0">
                        <?= array_sum(array_column($gender, 'jumlah')) ?>
                    </h3>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
