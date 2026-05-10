<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Daftar Santri</h4>
                <a href="<?= base_url('akademik/santri/add') ?>" class="btn btn-primary">Tambah Santri</a>
            </div>

            <!-- Form Filter -->
            <form action="" method="get" class="row g-3 mb-4 p-3 bg-body-tertiary rounded shadow-sm mx-0">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Jenis Kelamin</label>
                    <select name="jk" class="form-select">
                        <option value="">-- Semua --</option>
                        <option value="L" <?= service('request')->getGet('jk') == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= service('request')->getGet('jk') == 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Status Santri</label>
                    <select name="status" class="form-select">
                        <option value="">-- Semua --</option>
                        <option value="Aktif" <?= service('request')->getGet('status') == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="Lulus" <?= service('request')->getGet('status') == 'Lulus' ? 'selected' : '' ?>>Lulus</option>
                        <option value="Mutasi" <?= service('request')->getGet('status') == 'Mutasi' ? 'selected' : '' ?>>Mutasi</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-secondary w-100">🔍 Filter</button>
                    
                    <div class="dropdown w-100">
                        <button class="btn btn-success dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                            📥 Export
                        </button>
                        <ul class="dropdown-menu shadow">
                            <li><a class="dropdown-item" href="<?= base_url('akademik/santri/export-excel?' . http_build_query(service('request')->getGet())) ?>">📄 Excel (.xls)</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('akademik/santri/export-word?' . http_build_query(service('request')->getGet())) ?>">📝 Word (.doc)</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('akademik/santri/export-pdf?' . http_build_query(service('request')->getGet())) ?>" target="_blank">📕 PDF (Print)</a></li>
                        </ul>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NISN</th>
                            <th>Nama Lengkap</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($santri)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada data santri.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach($santri as $s): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $s['nisn'] ?></td>
                                <td class="fw-semibold"><?= $s['nama_lengkap'] ?></td>
                                <td><span class="badge bg-success"><?= $s['status_santri'] ?></span></td>
                                <td>
                                    <div class="d-flex gap-3">
                                        <a href="<?= base_url('akademik/santri/show/'.$s['id']) ?>" class="btn btn-sm btn-info text-white rounded shadow-sm">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                        <a href="<?= base_url('akademik/santri/edit/'.$s['id']) ?>" class="btn btn-sm btn-outline-primary rounded shadow-sm">Edit</a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger rounded shadow-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal" 
                                                data-href="<?= base_url('akademik/santri/delete/'.$s['id']) ?>">
                                            Hapus
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
    </div>
</div>
<?= $this->endSection() ?>
