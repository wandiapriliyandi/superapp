<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Daftar Guru & Karyawan</h5>
        <div class="d-flex gap-2">
            <a href="<?= base_url('kepegawaian/karyawan/add') ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Pegawai
            </a>
            <a href="<?= base_url('kepegawaian/karyawan/export-excel') ?>" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </a>
        </div>
    </div>
    <div class="card-body p-4">
        <!-- Filter -->
        <form action="" method="GET" class="row g-3 mb-4 p-3 bg-body-tertiary rounded shadow-sm border">
            <div class="col-md-4">
                <label class="form-label fw-bold small">Jabatan</label>
                <input type="text" name="jabatan" class="form-control form-control-sm" placeholder="Cari jabatan..." value="<?= service('request')->getGet('jabatan') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold small">Status</label>
                <select name="status_pegawai" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="Tetap">Tetap</option>
                    <option value="Kontrak">Kontrak</option>
                    <option value="Honorer">Honorer</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">🔍 Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>NIP / Nama</th>
                        <th>L/P</th>
                        <th>Jabatan</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($karyawan as $k): ?>
                    <tr>
                        <td>
                            <div class="fw-bold"><?= $k['nama_lengkap'] ?></div>
                            <div class="small text-muted">NIP: <?= $k['nip'] ?: '-' ?></div>
                        </td>
                        <td><?= $k['jenis_kelamin'] ?></td>
                        <td><?= $k['jabatan'] ?></td>
                        <td>
                            <span class="badge bg-info text-dark"><?= $k['status_pegawai'] ?></span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="<?= base_url('kepegawaian/karyawan/show/'.$k['id']) ?>" class="btn btn-sm btn-info text-white rounded shadow-sm">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                                <button class="btn btn-sm btn-outline-primary rounded shadow-sm">Edit</button>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger rounded shadow-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal" 
                                        data-href="<?= base_url('kepegawaian/karyawan/delete/'.$k['id']) ?>">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
