<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div>
                <h3 class="fw-bold mb-1 text-primary"><i class="bi bi-book-half me-2"></i>Materi Pembelajaran E-Learning</h3>
                <p class="text-muted small mb-0">Kelola dan distribusikan bahan ajar, modul, serta video pembelajaran interaktif kepada santri.</p>
            </div>
            <a href="<?= base_url('e-learning/materi/add') ?>" class="btn btn-primary px-4 py-2 fw-semibold rounded-pill shadow-sm">
                <i class="bi bi-cloud-upload me-2"></i>Unggah Materi Baru
            </a>
        </div>
        
        <!-- Navigasi Cepat Submodul E-Learning -->
        <div class="d-flex gap-2 pb-2 border-bottom overflow-auto">
            <a href="<?= base_url('e-learning/materi') ?>" class="btn btn-sm btn-primary rounded-pill fw-bold px-3">
                <i class="bi bi-book me-1"></i>Materi Belajar
            </a>
            <a href="<?= base_url('e-learning/ujian') ?>" class="btn btn-sm btn-outline-secondary rounded-pill fw-medium px-3">
                <i class="bi bi-pencil-square me-1"></i>Ujian Online
            </a>
            <a href="<?= base_url('e-learning/skill') ?>" class="btn btn-sm btn-outline-warning text-dark border-warning rounded-pill fw-medium px-3 bg-warning bg-opacity-10">
                <i class="bi bi-award-fill text-warning me-1"></i>Pelatihan Skill & TOEFL
            </a>
        </div>
    </div>
</div>

<!-- Kotak Filter & Export -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-body p-4 bg-light bg-opacity-50">
        <form action="" method="get" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-secondary">Mata Pelajaran</label>
                <select name="mata_pelajaran" class="form-select border-0 shadow-sm rounded-3">
                    <option value="">-- Semua Mata Pelajaran --</option>
                    <option value="Fiqih" <?= service('request')->getGet('mata_pelajaran') == 'Fiqih' ? 'selected' : '' ?>>Fiqih</option>
                    <option value="Bahasa Arab" <?= service('request')->getGet('mata_pelajaran') == 'Bahasa Arab' ? 'selected' : '' ?>>Bahasa Arab</option>
                    <option value="Sejarah Islam" <?= service('request')->getGet('mata_pelajaran') == 'Sejarah Islam' ? 'selected' : '' ?>>Sejarah Islam</option>
                    <option value="Aqidah Akhlak" <?= service('request')->getGet('mata_pelajaran') == 'Aqidah Akhlak' ? 'selected' : '' ?>>Aqidah Akhlak</option>
                    <option value="Al-Qur'an Hadits" <?= service('request')->getGet('mata_pelajaran') == "Al-Qur'an Hadits" ? 'selected' : '' ?>>Al-Qur'an Hadits</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-secondary">Kelas / Tingkatan</label>
                <select name="kelas" class="form-select border-0 shadow-sm rounded-3">
                    <option value="">-- Semua Kelas --</option>
                    <option value="Kelas 10" <?= service('request')->getGet('kelas') == 'Kelas 10' ? 'selected' : '' ?>>Kelas 10</option>
                    <option value="Kelas 11" <?= service('request')->getGet('kelas') == 'Kelas 11' ? 'selected' : '' ?>>Kelas 11</option>
                    <option value="Kelas 12" <?= service('request')->getGet('kelas') == 'Kelas 12' ? 'selected' : '' ?>>Kelas 12</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-50 rounded-3 shadow-sm fw-semibold">
                    <i class="bi bi-funnel-fill me-1"></i> Filter
                </button>
                <div class="dropdown w-50">
                    <button class="btn btn-success dropdown-toggle w-100 rounded-3 shadow-sm fw-semibold" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-file-earmark-arrow-down me-1"></i> Export
                    </button>
                    <ul class="dropdown-menu shadow-sm border-0 rounded-3">
                        <li><a class="dropdown-item py-2" href="<?= base_url('e-learning/materi/export-excel?' . http_build_query(service('request')->getGet())) ?>"><i class="bi bi-file-earmark-excel text-success me-2"></i>Excel (.xls)</a></li>
                        <li><a class="dropdown-item py-2" href="<?= base_url('e-learning/materi/export-word?' . http_build_query(service('request')->getGet())) ?>"><i class="bi bi-file-earmark-word text-primary me-2"></i>Word (.doc)</a></li>
                        <li><a class="dropdown-item py-2" href="<?= base_url('e-learning/materi/export-pdf?' . http_build_query(service('request')->getGet())) ?>" target="_blank"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Cetak PDF</a></li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Daftar Materi -->
<div class="row g-4">
    <?php if(empty($materi)): ?>
        <div class="col-12 text-center py-5">
            <div class="text-muted mb-3" style="font-size: 4rem;"><i class="bi bi-folder-x"></i></div>
            <h5 class="fw-bold">Belum Ada Materi Belajar</h5>
            <p class="text-muted small">Silakan sesuaikan filter pencarian atau unggah bahan ajar baru untuk memulai.</p>
        </div>
    <?php else: ?>
        <?php foreach($materi as $item): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column hover-lift">
                    <div class="card-header border-0 bg-transparent pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold small">
                            <?= esc($item['mata_pelajaran']) ?>
                        </span>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill small fw-semibold">
                            <?= esc($item['kelas']) ?>
                        </span>
                    </div>
                    <div class="card-body px-4 py-3 flex-grow-1">
                        <h5 class="fw-bold text-dark line-clamp-2 mb-2"><?= esc($item['judul']) ?></h5>
                        <p class="text-muted small line-clamp-3 mb-3"><?= esc($item['deskripsi']) ?></p>
                        
                        <div class="d-flex align-items-center text-muted small mt-auto pt-2 border-top">
                            <i class="bi bi-person-circle me-2 text-primary"></i>
                            <span class="text-truncate fw-medium"><?= esc($item['guru_pengampu'] ?: 'Guru Pengampu') ?></span>
                        </div>
                    </div>
                    <div class="card-footer border-0 bg-light bg-opacity-50 px-4 py-3 rounded-bottom-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-2">
                            <?php if(!empty($item['file_materi'])): ?>
                                <a href="<?= base_url('uploads/materi/' . esc($item['file_materi'])) ?>" class="btn btn-sm btn-outline-success rounded-circle" target="_blank" data-bs-toggle="tooltip" title="Unduh / Lihat File">
                                    <i class="bi bi-download"></i>
                                </a>
                            <?php endif; ?>
                            <?php if(!empty($item['link_video'])): ?>
                                <a href="<?= esc($item['link_video']) ?>" class="btn btn-sm btn-outline-danger rounded-circle" target="_blank" data-bs-toggle="tooltip" title="Tonton Video Pembelajaran">
                                    <i class="bi bi-play-fill"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex gap-1">
                            <a href="<?= base_url('e-learning/materi/show/' . $item['id']) ?>" class="btn btn-sm btn-light fw-medium text-primary px-3 rounded-pill">Lihat Detail</a>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle border-0" data-bs-toggle="modal" data-bs-target="#deleteModal" data-href="<?= base_url('e-learning/materi/delete/' . $item['id']) ?>" title="Hapus Materi">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
    .hover-lift {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .hover-lift:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.06) !important;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<script>
    // Inisialisasi Tooltip Bootstrap
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
<?= $this->endSection() ?>
