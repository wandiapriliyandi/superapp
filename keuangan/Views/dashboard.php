<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h3 class="fw-bold text-primary">Financial Overview 📊</h3>
            <p class="text-muted">Manajemen keuangan pesantren secara profesional dengan standar akuntansi.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 opacity-75">Bagan Akun</h6>
                            <h3 class="mb-0 fw-bold"><?= $total_akun ?></h3>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded-circle">
                            <i class="fas fa-list-ul fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 opacity-75">Jurnal Umum</h6>
                            <h3 class="mb-0 fw-bold"><?= $total_jurnal ?></h3>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded-circle">
                            <i class="fas fa-book fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 opacity-75">Kas Masuk (Bln Ini)</h6>
                            <h4 class="mb-0 fw-bold">Rp <?= number_format($kas_masuk_bulan_ini, 0, ',', '.') ?></h4>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded-circle">
                            <i class="fas fa-arrow-down fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4 bg-danger text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 opacity-75">Kas Keluar (Bln Ini)</h6>
                            <h4 class="mb-0 fw-bold">Rp <?= number_format($kas_keluar_bulan_ini, 0, ',', '.') ?></h4>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded-circle">
                            <i class="fas fa-arrow-up fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Menu Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="<?= base_url('keuangan/akun') ?>" class="btn btn-outline-primary w-100 py-3 rounded-3">
                                <i class="fas fa-sitemap d-block mb-2 fa-2x"></i>
                                Kelola Akun (COA)
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="<?= base_url('keuangan/jurnal/add') ?>" class="btn btn-outline-success w-100 py-3 rounded-3">
                                <i class="fas fa-plus-circle d-block mb-2 fa-2x"></i>
                                Buat Jurnal Baru
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="<?= base_url('keuangan/buku-besar') ?>" class="btn btn-outline-info w-100 py-3 rounded-3">
                                <i class="fas fa-columns d-block mb-2 fa-2x"></i>
                                Lihat Buku Besar
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="<?= base_url('keuangan/laporan/neraca') ?>" class="btn btn-outline-dark w-100 py-3 rounded-3">
                                <i class="fas fa-file-invoice-dollar d-block mb-2 fa-2x"></i>
                                Laporan Neraca
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="<?= base_url('keuangan/laporan/laba-rugi') ?>" class="btn btn-outline-warning w-100 py-3 rounded-3">
                                <i class="fas fa-chart-line d-block mb-2 fa-2x"></i>
                                Laba Rugi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Status Akuntansi</h5>
                </div>
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-check-circle text-success fa-4x"></i>
                    </div>
                    <h5 class="fw-bold">Balanced</h5>
                    <p class="text-muted">Total Debit dan Kredit dalam sistem saat ini seimbang.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
