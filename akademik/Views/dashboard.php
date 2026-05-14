<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <i class="bi bi-people-fill fs-1 opacity-50"></i>
                    <h2 class="fw-bold mb-0"><?= $total_santri ?></h2>
                </div>
                <h6 class="mb-0 fw-semibold">Total Santri</h6>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-success text-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <i class="bi bi-gender-male fs-1 opacity-50"></i>
                    <h2 class="fw-bold mb-0"><?= $total_putra ?></h2>
                </div>
                <h6 class="mb-0 fw-semibold">Santri Putra</h6>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-info text-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <i class="bi bi-gender-female fs-1 opacity-50"></i>
                    <h2 class="fw-bold mb-0"><?= $total_putri ?></h2>
                </div>
                <h6 class="mb-0 fw-semibold">Santri Putri</h6>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-warning text-dark">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <i class="bi bi-check-circle-fill fs-1 opacity-50"></i>
                    <h2 class="fw-bold mb-0"><?= $santri_aktif ?></h2>
                </div>
                <h6 class="mb-0 fw-semibold">Santri Aktif</h6>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-0">Menu Akademik</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="list-group list-group-flush">
                    <a href="<?= base_url('akademik/santri') ?>" class="list-group-item list-group-item-action border-0 px-0 py-3 d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-mortarboard text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold">Data Santri</h6>
                            <small class="text-muted">Kelola biodata dan status santri</small>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="<?= base_url('akademik/tahun-ajaran') ?>" class="list-group-item list-group-item-action border-0 px-0 py-3 d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-calendar-event text-success"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold">Tahun Ajaran</h6>
                            <small class="text-muted">Pengaturan periode pendidikan</small>
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-0">Statistik Cepat</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <p class="text-muted small">Modul akademik terintegrasi dengan data PPDB dan SPP untuk memudahkan sinkronisasi data santri secara real-time.</p>
                <div class="d-grid mt-4">
                    <a href="<?= base_url('akademik/santri/add') ?>" class="btn btn-outline-primary rounded-pill py-2">
                        <i class="bi bi-person-plus me-2"></i> Input Santri Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
