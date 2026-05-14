<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <i class="bi bi-people-fill fs-1 opacity-50"></i>
                    <h2 class="fw-bold mb-0"><?= $total_santri ?></h2>
                </div>
                <h6 class="mb-0 fw-semibold">Total Santri</h6>
                <small class="opacity-75"><?= $santri_aktif ?> Santri Aktif</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-success text-white h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <i class="bi bi-door-open-fill fs-1 opacity-50"></i>
                    <h2 class="fw-bold mb-0"><?= $total_kelas ?? 0 ?></h2>
                </div>
                <h6 class="mb-0 fw-semibold">Total Kelas</h6>
                <small class="opacity-75">Tersedia di Sistem</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-info text-white h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <i class="bi bi-book-half fs-1 opacity-50"></i>
                    <h2 class="fw-bold mb-0"><?= $total_mapel ?? 0 ?></h2>
                </div>
                <h6 class="mb-0 fw-semibold">Mata Pelajaran</h6>
                <small class="opacity-75">Kurikulum Aktif</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-warning text-dark h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <i class="bi bi-calendar-check-fill fs-1 opacity-50"></i>
                    <h2 class="fw-bold mb-0"><?= $total_jadwal ?? 0 ?></h2>
                </div>
                <h6 class="mb-0 fw-semibold">Jadwal Aktif</h6>
                <small class="opacity-75 text-dark text-opacity-75">Sesi per Minggu</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-0">Manajemen Akademik</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="<?= base_url('akademik/santri') ?>" class="text-decoration-none">
                            <div class="p-3 border rounded-4 hover-shadow transition-all d-flex align-items-center bg-light bg-opacity-50">
                                <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3 text-primary">
                                    <i class="bi bi-mortarboard fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">Data Santri</h6>
                                    <small class="text-muted">Kelola biodata santri</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="<?= base_url('akademik/kelas') ?>" class="text-decoration-none">
                            <div class="p-3 border rounded-4 hover-shadow transition-all d-flex align-items-center bg-light bg-opacity-50">
                                <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3 text-success">
                                    <i class="bi bi-door-open fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">Manajemen Kelas</h6>
                                    <small class="text-muted">Rombel & Wali Kelas</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="<?= base_url('akademik/mapel') ?>" class="text-decoration-none">
                            <div class="p-3 border rounded-4 hover-shadow transition-all d-flex align-items-center bg-light bg-opacity-50">
                                <div class="bg-info bg-opacity-10 p-3 rounded-3 me-3 text-info">
                                    <i class="bi bi-journal-text fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">Mata Pelajaran</h6>
                                    <small class="text-muted">Kelola kurikulum</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="<?= base_url('akademik/jadwal') ?>" class="text-decoration-none">
                            <div class="p-3 border rounded-4 hover-shadow transition-all d-flex align-items-center bg-light bg-opacity-50">
                                <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3 text-warning">
                                    <i class="bi bi-calendar3 fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">Jadwal Pelajaran</h6>
                                    <small class="text-muted">Atur waktu KBM</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="<?= base_url('akademik/presensi') ?>" class="text-decoration-none">
                            <div class="p-3 border rounded-4 hover-shadow transition-all d-flex align-items-center bg-light bg-opacity-50">
                                <div class="bg-danger bg-opacity-10 p-3 rounded-3 me-3 text-danger">
                                    <i class="bi bi-person-check fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">Presensi Santri</h6>
                                    <small class="text-muted">Absensi harian KBM</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="<?= base_url('akademik/nilai') ?>" class="text-decoration-none">
                            <div class="p-3 border rounded-4 hover-shadow transition-all d-flex align-items-center bg-light bg-opacity-50">
                                <div class="bg-secondary bg-opacity-10 p-3 rounded-3 me-3 text-secondary">
                                    <i class="bi bi-clipboard-data fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">Input Nilai & Rapor</h6>
                                    <small class="text-muted">Penilaian hasil belajar</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-0">Tahun Ajaran</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="alert alert-primary border-0 rounded-4 mb-4">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="bi bi-info-circle-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Periode Aktif</h6>
                            <p class="mb-0 small">Semester Ganjil 2024/2025</p>
                        </div>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <a href="<?= base_url('akademik/tahun-ajaran') ?>" class="btn btn-outline-primary rounded-pill py-2">
                        <i class="bi bi-gear me-2"></i> Pengaturan Periode
                    </a>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm rounded-4 bg-dark text-white overflow-hidden">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Informasi Sistem</h5>
                <p class="small text-white text-opacity-75 mb-4">Pastikan data wali kelas dan guru pengampu sudah terdaftar di modul Kepegawaian sebelum mengatur jadwal pelajaran.</p>
                <img src="<?= base_url('assets/img/academic-vector.png') ?>" alt="" class="img-fluid opacity-25" style="position: absolute; right: -20px; bottom: -20px; width: 150px;">
            </div>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important;
        transform: translateY(-2px);
    }
    .transition-all {
        transition: all 0.3s ease;
    }
</style>
<?= $this->endSection() ?>

