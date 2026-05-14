<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row g-3">
    <div class="col-12 mb-2">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold">Manajemen SPP (SPP) 💰</h3>
                <p class="text-muted">Kelola tarif, tagihan, dan pembayaran santri.</p>
            </div>
            <div>
                <a href="<?= base_url('spp/tagihan/generate') ?>" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-magic me-2"></i>Generate Tagihan
                </a>
            </div>
        </div>
    </div>

    <!-- Statistik -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">
            <div class="card-body">
                <h6 class="opacity-75">Total Tagihan</h6>
                <h4 class="fw-bold mb-0">Rp <?= number_format($total_tagihan, 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-success text-white">
            <div class="card-body">
                <h6 class="opacity-75">Total Terbayar</h6>
                <h4 class="fw-bold mb-0">Rp <?= number_format($total_terbayar, 0, ',', '.') ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-info text-white">
            <div class="card-body">
                <h6 class="opacity-75">Lunas</h6>
                <h4 class="fw-bold mb-0"><?= $tagihan_lunas ?> Transaksi</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-danger text-white">
            <div class="card-body">
                <h6 class="opacity-75">Belum Lunas</h6>
                <h4 class="fw-bold mb-0"><?= $tagihan_belum ?> Transaksi</h4>
            </div>
        </div>
    </div>

    <!-- Menu Cepat -->
    <div class="col-md-4 mt-4">
        <a href="<?= base_url('spp/tarif') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 h-100 hover-up">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3 me-3">
                        <i class="bi bi-tags fs-3"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Tarif SPP</h6>
                        <small class="text-muted">Atur nominal bulanan</small>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4 mt-4">
        <a href="<?= base_url('spp/tagihan') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 h-100 hover-up">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-3 me-3">
                        <i class="bi bi-receipt fs-3"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Data Tagihan</h6>
                        <small class="text-muted">Lihat semua tagihan santri</small>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4 mt-4">
        <a href="<?= base_url('spp/pembayaran') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 h-100 hover-up">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-3 me-3">
                        <i class="bi bi-cash-stack fs-3"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Pembayaran</h6>
                        <small class="text-muted">Input transaksi bayar</small>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<style>
    .hover-up {
        transition: transform 0.2s;
    }
    .hover-up:hover {
        transform: translateY(-5px);
    }
</style>
<?= $this->endSection() ?>
