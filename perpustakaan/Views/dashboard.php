<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white h-100 overflow-hidden position-relative">
            <div class="card-body p-4 position-relative" style="z-index: 1;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <i class="bi bi-person-badge fs-1 opacity-50"></i>
                    <h2 class="fw-bold mb-0"><?= $count_putra ?></h2>
                </div>
                <h5 class="mb-1 fw-bold">Perpustakaan Putra</h5>
                <p class="mb-4 opacity-75 small">Koleksi buku fisik untuk santri putra.</p>
                <a href="<?= base_url('perpustakaan/list/putra') ?>" class="btn btn-light rounded-pill btn-sm px-4 fw-bold text-primary">Lihat Koleksi</a>
            </div>
            <i class="bi bi-book position-absolute" style="font-size: 10rem; right: -20px; bottom: -30px; opacity: 0.1;"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-danger text-white h-100 overflow-hidden position-relative">
            <div class="card-body p-4 position-relative" style="z-index: 1;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <i class="bi bi-person-heart fs-1 opacity-50"></i>
                    <h2 class="fw-bold mb-0"><?= $count_putri ?></h2>
                </div>
                <h5 class="mb-1 fw-bold">Perpustakaan Putri</h5>
                <p class="mb-4 opacity-75 small">Koleksi buku fisik untuk santri putri.</p>
                <a href="<?= base_url('perpustakaan/list/putri') ?>" class="btn btn-light rounded-pill btn-sm px-4 fw-bold text-danger">Lihat Koleksi</a>
            </div>
            <i class="bi bi-journals position-absolute" style="font-size: 10rem; right: -20px; bottom: -30px; opacity: 0.1;"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-dark text-white h-100 overflow-hidden position-relative">
            <div class="card-body p-4 position-relative" style="z-index: 1;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <i class="bi bi-cloud-arrow-down fs-1 opacity-50"></i>
                    <h2 class="fw-bold mb-0"><?= $count_digital ?></h2>
                </div>
                <h5 class="mb-1 fw-bold">Perpustakaan Digital</h5>
                <p class="mb-4 opacity-75 small">Akses E-Book, PDF, dan materi digital.</p>
                <a href="<?= base_url('perpustakaan/list/digital') ?>" class="btn btn-primary rounded-pill btn-sm px-4 fw-bold">Buka Digital</a>
            </div>
            <i class="bi bi-laptop position-absolute" style="font-size: 10rem; right: -20px; bottom: -30px; opacity: 0.1;"></i>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 p-4 pb-0">
                <h5 class="fw-bold mb-0">Menu Cepat Perpustakaan</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="<?= base_url('perpustakaan/tambah/putra') ?>" class="text-decoration-none">
                            <div class="p-3 border rounded-4 hover-shadow transition-all d-flex align-items-center bg-light bg-opacity-50">
                                <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3 text-primary">
                                    <i class="bi bi-plus-circle-dotted fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">Tambah Buku Putra</h6>
                                    <small class="text-muted">Input koleksi baru putra</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="<?= base_url('perpustakaan/tambah/putri') ?>" class="text-decoration-none">
                            <div class="p-3 border rounded-4 hover-shadow transition-all d-flex align-items-center bg-light bg-opacity-50">
                                <div class="bg-danger bg-opacity-10 p-3 rounded-3 me-3 text-danger">
                                    <i class="bi bi-plus-circle-dotted fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">Tambah Buku Putri</h6>
                                    <small class="text-muted">Input koleksi baru putri</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="<?= base_url('perpustakaan/tambah/digital') ?>" class="text-decoration-none">
                            <div class="p-3 border rounded-4 hover-shadow transition-all d-flex align-items-center bg-light bg-opacity-50">
                                <div class="bg-dark bg-opacity-10 p-3 rounded-3 me-3 text-dark">
                                    <i class="bi bi-file-earmark-pdf fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">Upload E-Book</h6>
                                    <small class="text-muted">Kelola pustaka digital</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="<?= base_url('perpustakaan/pengaturan') ?>" class="text-decoration-none">
                            <div class="p-3 border rounded-4 hover-shadow transition-all d-flex align-items-center bg-light bg-opacity-50">
                                <div class="<?= $drive_ready ? 'bg-success' : 'bg-warning' ?> bg-opacity-10 p-3 rounded-3 me-3 text-<?= $drive_ready ? 'success' : 'warning' ?>">
                                    <i class="bi bi-cloud-upload fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">Pengaturan Cloud</h6>
                                    <small class="<?= $drive_ready ? 'text-success' : 'text-muted' ?>">
                                        <?= $drive_ready ? 'Google Drive Aktif' : 'Konfigurasi Google Drive' ?>
                                    </small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 p-4 pb-0">
                <h5 class="fw-bold mb-0">Statistik Kategori</h5>
            </div>
            <div class="card-body p-4">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item border-0 d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-circle-fill text-primary me-2 small"></i> Kitab Kuning</span>
                        <span class="badge bg-light text-dark rounded-pill">240</span>
                    </li>
                    <li class="list-group-item border-0 d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-circle-fill text-success me-2 small"></i> Sains & Tekno</span>
                        <span class="badge bg-light text-dark rounded-pill">150</span>
                    </li>
                    <li class="list-group-item border-0 d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-circle-fill text-warning me-2 small"></i> Sastra & Novel</span>
                        <span class="badge bg-light text-dark rounded-pill">85</span>
                    </li>
                    <li class="list-group-item border-0 d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-circle-fill text-info me-2 small"></i> Sejarah</span>
                        <span class="badge bg-light text-dark rounded-pill">112</span>
                    </li>
                </ul>
                <div class="mt-4 pt-3 border-top">
                    <p class="small text-muted mb-0">Terakhir diperbarui: <br><strong><?= date('d M Y H:i') ?></strong></p>
                </div>
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
