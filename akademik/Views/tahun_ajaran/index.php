<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">📅 Master Tahun Ajaran</h4>
                <a href="<?= base_url('akademik/tahun-ajaran/add') ?>" class="btn btn-primary">Tambah Tahun Ajaran</a>
            </div>

            <!-- Form Filter & Export -->
            <form action="" method="get" class="row g-3 mb-4 p-3 bg-body-tertiary rounded shadow-sm mx-0">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Semester</label>
                    <select name="semester" class="form-select">
                        <option value="">-- Semua --</option>
                        <option value="Ganjil" <?= service('request')->getGet('semester') == 'Ganjil' ? 'selected' : '' ?>>Ganjil</option>
                        <option value="Genap" <?= service('request')->getGet('semester') == 'Genap' ? 'selected' : '' ?>>Genap</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">-- Semua --</option>
                        <option value="Aktif" <?= service('request')->getGet('status') == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="Tidak Aktif" <?= service('request')->getGet('status') == 'Tidak Aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-secondary w-100">🔍 Filter</button>
                    
                    <div class="dropdown w-100">
                        <button class="btn btn-success dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                            📥 Export
                        </button>
                        <ul class="dropdown-menu shadow">
                            <li><a class="dropdown-item" href="<?= base_url('akademik/tahun-ajaran/export-excel?' . http_build_query(service('request')->getGet())) ?>">📄 Excel (.xls)</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('akademik/tahun-ajaran/export-word?' . http_build_query(service('request')->getGet())) ?>">📝 Word (.doc)</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('akademik/tahun-ajaran/export-pdf?' . http_build_query(service('request')->getGet())) ?>" target="_blank">📕 PDF (Print)</a></li>
                        </ul>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Durasi</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($ta as $item): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="fw-bold"><?= $item['tahun_ajaran'] ?></td>
                            <td><?= $item['semester'] ?></td>
                            <td class="small text-muted">
                                <?= date('d M Y', strtotime($item['tgl_mulai'])) ?> - 
                                <?= date('d M Y', strtotime($item['tgl_selesai'])) ?>
                            </td>
                            <td class="text-center">
                                <?php if($item['status'] == 'Aktif'): ?>
                                    <span class="badge bg-success px-3 py-2">AKTIF</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary px-3 py-2">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <?php if($item['status'] == 'Tidak Aktif'): ?>
                                        <a href="<?= base_url('akademik/tahun-ajaran/set-active/' . $item['id']) ?>" 
                                           class="btn btn-sm btn-outline-primary rounded shadow-sm"
                                           onclick="return confirm('Aktifkan tahun ajaran ini?')">
                                           Aktifkan
                                        </a>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-outline-secondary rounded shadow-sm">Edit</button>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger rounded shadow-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal" 
                                            data-href="<?= base_url('akademik/tahun-ajaran/delete/'.$item['id']) ?>">
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
</div>
<?= $this->endSection() ?>
