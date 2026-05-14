<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm" style="border-radius: 20px;">
    <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-1">Data Asatidz & Staff</h5>
            <p class="text-muted small mb-0">Manajemen seluruh sumber daya manusia di pesantren</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('kepegawaian/pegawai/export') ?>" class="btn btn-light rounded-pill px-3">
                <i class="bi bi-download me-2"></i> Export
            </a>
            <a href="<?= base_url('kepegawaian/pegawai/add') ?>" class="btn btn-primary rounded-pill px-3">
                <i class="bi bi-plus-lg me-2"></i> Tambah SDM
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">NIK & Nama</th>
                        <th>Departemen</th>
                        <th>Jabatan</th>
                        <th>Status</th>
                        <th>Kontak</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($pegawai as $p): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <img src="<?= base_url('uploads/pegawai/' . $p['foto']) ?>" class="rounded-circle me-3" width="45" height="45" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($p['nama_lengkap']) ?>&background=random'">
                                <div>
                                    <div class="fw-bold text-dark"><?= $p['nama_lengkap'] ?></div>
                                    <div class="small text-muted font-monospace"><?= $p['nik'] ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold"><?= $p['nama_departemen'] ?></div>
                        </td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-medium">
                                <?= $p['nama_jabatan'] ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                                $status_class = 'bg-secondary';
                                if($p['status_pegawai'] == 'Tetap') $status_class = 'bg-success';
                                elseif($p['status_pegawai'] == 'Kontrak') $status_class = 'bg-info';
                                elseif($p['status_pegawai'] == 'Probation') $status_class = 'bg-warning';
                            ?>
                            <span class="badge <?= $status_class ?> bg-opacity-10 <?= str_replace('bg-', 'text-', $status_class) ?> px-3 py-2 rounded-pill fw-medium">
                                <?= $p['status_pegawai'] ?>
                            </span>
                        </td>
                        <td>
                            <div class="small mb-1"><i class="bi bi-whatsapp me-2 text-success"></i><?= $p['no_hp'] ?></div>
                            <div class="small text-muted"><i class="bi bi-envelope me-2"></i><?= $p['email'] ?></div>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px;">
                                    <li><a class="dropdown-item py-2" href="#"><i class="bi bi-eye me-2 text-primary"></i> Detail Profil</a></li>
                                    <li><a class="dropdown-item py-2" href="<?= base_url('kepegawaian/pegawai/edit/' . $p['id']) ?>"><i class="bi bi-pencil me-2 text-warning"></i> Edit Data</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item py-2 text-danger" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#deleteModal" data-href="<?= base_url('kepegawaian/pegawai/delete/' . $p['id']) ?>"><i class="bi bi-trash me-2"></i> Hapus</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($pegawai)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <img src="https://illustrations.popsy.co/gray/data-analysis.svg" alt="Empty" style="max-height: 150px;" class="mb-3 d-block mx-auto">
                            <p class="text-muted">Data Asatidz & Staff masih kosong.</p>
                            <a href="<?= base_url('kepegawaian/pegawai/add') ?>" class="btn btn-primary rounded-pill px-4">Tambah Sekarang</a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
