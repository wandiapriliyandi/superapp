<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div>
                <h3 class="fw-bold mb-1 text-indigo"><i class="bi bi-pencil-square me-2"></i>Ujian & Tugas Online E-Learning</h3>
                <p class="text-muted small mb-0">Atur jadwal pelaksanaan ujian, kuis interaktif, serta penugasan mandiri secara daring untuk para santri.</p>
            </div>
            <a href="<?= base_url('e-learning/ujian/add') ?>" class="btn btn-indigo px-4 py-2 fw-semibold rounded-pill shadow-sm text-white" style="background-color: #6610f2; border-color: #6610f2;">
                <i class="bi bi-plus-circle me-2"></i>Buat Jadwal Ujian Baru
            </a>
        </div>
        
        <!-- Navigasi Cepat Submodul E-Learning -->
        <div class="d-flex gap-2 pb-2 border-bottom overflow-auto">
            <a href="<?= base_url('e-learning/materi') ?>" class="btn btn-sm btn-outline-primary rounded-pill fw-medium px-3">
                <i class="bi bi-book me-1"></i>Materi Belajar
            </a>
            <a href="<?= base_url('e-learning/ujian') ?>" class="btn btn-sm text-white rounded-pill fw-bold px-3" style="background-color: #6610f2;">
                <i class="bi bi-pencil-square me-1"></i>Ujian Online
            </a>
            <a href="<?= base_url('e-learning/skill') ?>" class="btn btn-sm btn-outline-warning text-dark border-warning rounded-pill fw-medium px-3 bg-warning bg-opacity-10">
                <i class="bi bi-award-fill text-warning me-1"></i>Pelatihan Skill & TOEFL
            </a>
        </div>
    </div>
</div>

<!-- Form Filter & Export -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-body p-4 bg-light bg-opacity-50">
        <form action="" method="get" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-secondary">Mata Pelajaran</label>
                <select name="mata_pelajaran" class="form-select border-0 shadow-sm rounded-3">
                    <option value="">-- Semua Mapel --</option>
                    <option value="Fiqih" <?= service('request')->getGet('mata_pelajaran') == 'Fiqih' ? 'selected' : '' ?>>Fiqih</option>
                    <option value="Bahasa Arab" <?= service('request')->getGet('mata_pelajaran') == 'Bahasa Arab' ? 'selected' : '' ?>>Bahasa Arab</option>
                    <option value="Sejarah Islam" <?= service('request')->getGet('mata_pelajaran') == 'Sejarah Islam' ? 'selected' : '' ?>>Sejarah Islam</option>
                    <option value="Aqidah Akhlak" <?= service('request')->getGet('mata_pelajaran') == 'Aqidah Akhlak' ? 'selected' : '' ?>>Aqidah Akhlak</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-secondary">Tingkatan Kelas</label>
                <select name="kelas" class="form-select border-0 shadow-sm rounded-3">
                    <option value="">-- Semua Kelas --</option>
                    <option value="Kelas 10" <?= service('request')->getGet('kelas') == 'Kelas 10' ? 'selected' : '' ?>>Kelas 10</option>
                    <option value="Kelas 11" <?= service('request')->getGet('kelas') == 'Kelas 11' ? 'selected' : '' ?>>Kelas 11</option>
                    <option value="Kelas 12" <?= service('request')->getGet('kelas') == 'Kelas 12' ? 'selected' : '' ?>>Kelas 12</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-secondary">Status Pelaksanaan</label>
                <select name="status" class="form-select border-0 shadow-sm rounded-3">
                    <option value="">-- Semua Status --</option>
                    <option value="Aktif" <?= service('request')->getGet('status') == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="Draf" <?= service('request')->getGet('status') == 'Draf' ? 'selected' : '' ?>>Draf</option>
                    <option value="Selesai" <?= service('request')->getGet('status') == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-50 rounded-3 shadow-sm fw-semibold">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
                <div class="dropdown w-50">
                    <button class="btn btn-success dropdown-toggle w-100 rounded-3 shadow-sm fw-semibold" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-cloud-download me-1"></i> Export
                    </button>
                    <ul class="dropdown-menu shadow border-0 rounded-3">
                        <li><a class="dropdown-item py-2" href="<?= base_url('e-learning/ujian/export-excel?' . http_build_query(service('request')->getGet())) ?>"><i class="bi bi-file-earmark-excel text-success me-2"></i>Excel (.xls)</a></li>
                        <li><a class="dropdown-item py-2" href="<?= base_url('e-learning/ujian/export-word?' . http_build_query(service('request')->getGet())) ?>"><i class="bi bi-file-earmark-word text-primary me-2"></i>Word (.doc)</a></li>
                        <li><a class="dropdown-item py-2" href="<?= base_url('e-learning/ujian/export-pdf?' . http_build_query(service('request')->getGet())) ?>" target="_blank"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Cetak PDF</a></li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Daftar Ujian / Tugas -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-uppercase fs-7" style="letter-spacing: 0.5px;">
                <tr>
                    <th class="py-3 ps-4">No</th>
                    <th class="py-3">Judul Ujian / Penugasan</th>
                    <th class="py-3">Mata Pelajaran</th>
                    <th class="py-3">Sasaran Kelas</th>
                    <th class="py-3">Waktu & Durasi</th>
                    <th class="py-3 text-center">Status</th>
                    <th class="py-3 text-center pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($ujian)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada jadwal ujian atau penugasan yang terdaftar.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach($ujian as $u): ?>
                        <tr>
                            <td class="ps-4 fw-medium text-muted"><?= $no++ ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($u['judul']) ?></div>
                                <div class="small text-muted text-truncate" style="max-width: 300px;">
                                    <?= esc($u['deskripsi']) ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <?= esc($u['mata_pelajaran']) ?>
                                </span>
                            </td>
                            <td class="fw-medium text-secondary"><?= esc($u['kelas']) ?></td>
                            <td>
                                <div class="small fw-semibold text-dark">
                                    <i class="bi bi-stopwatch me-1 text-danger"></i><?= esc($u['durasi_menit']) ?> Menit
                                </div>
                                <div class="fs-8 text-muted">
                                    <?= date('d M Y, H:i', strtotime($u['tgl_mulai'])) ?> s/d <br>
                                    <?= date('d M Y, H:i', strtotime($u['tgl_selesai'])) ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php if($u['status'] === 'Aktif'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill fw-bold">AKTIF</span>
                                <?php elseif($u['status'] === 'Draf'): ?>
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-1 rounded-pill fw-bold">DRAF</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1 rounded-pill fw-bold">SELESAI</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="<?= base_url('e-learning/ujian/show/' . $u['id']) ?>" class="btn btn-sm btn-outline-primary rounded-circle shadow-sm" title="Lihat Rincian">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" data-href="<?= base_url('e-learning/ujian/delete/' . $u['id']) ?>" title="Hapus Ujian">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .text-indigo { color: #6610f2; }
    .fs-7 { font-size: 0.85rem; }
    .fs-8 { font-size: 0.75rem; }
</style>
<?= $this->endSection() ?>
