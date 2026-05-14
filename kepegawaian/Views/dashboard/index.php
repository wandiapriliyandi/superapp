<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-4 me-3">
                        <i class="bi bi-people-fill text-primary fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Asatidz & Staff</h6>
                        <h2 class="fw-bold mb-0"><?= $total_pegawai ?></h2>
                    </div>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar" role="progressbar" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-4 me-3">
                        <i class="bi bi-calendar-check text-success fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Hadir Hari Ini</h6>
                        <h2 class="fw-bold mb-0"><?= $hadir_hari_ini ?></h2>
                    </div>
                </div>
                <div class="progress" style="height: 6px;">
                    <?php $perc = ($total_pegawai > 0) ? ($hadir_hari_ini / $total_pegawai) * 100 : 0; ?>
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $perc ?>%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-4 me-3">
                        <i class="bi bi-envelope-paper-fill text-warning fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Cuti Menunggu Approval</h6>
                        <h2 class="fw-bold mb-0"><?= $cuti_pending ?></h2>
                    </div>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 50%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">SDM Baru Bergabung</h5>
                <a href="<?= base_url('kepegawaian/pegawai') ?>" class="btn btn-sm btn-light rounded-pill px-3">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Nama Lengkap</th>
                                <th>Unit / Departemen</th>
                                <th>Jabatan</th>
                                <th>Status</th>
                                <th class="pe-4 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($pegawai_terbaru as $p): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="<?= base_url('uploads/pegawai/' . $p['foto']) ?>" class="rounded-circle me-3" width="40" height="40" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($p['nama_lengkap']) ?>&background=random'">
                                        <div>
                                            <div class="fw-bold"><?= $p['nama_lengkap'] ?></div>
                                            <div class="small text-muted"><?= $p['nik'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= $p['nama_departemen'] ?></td>
                                <td><span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill"><?= $p['nama_jabatan'] ?></span></td>
                                <td>
                                    <?php 
                                        $color = 'secondary';
                                        if($p['status_pegawai'] == 'Tetap') $color = 'primary';
                                        elseif($p['status_pegawai'] == 'Kontrak') $color = 'warning';
                                    ?>
                                    <span class="badge bg-<?= $color ?> bg-opacity-10 text-<?= $color ?> px-3 py-2 rounded-pill"><?= $p['status_pegawai'] ?></span>
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="<?= base_url('kepegawaian/pegawai/edit/' . $p['id']) ?>" class="btn btn-sm btn-light rounded-pill"><i class="bi bi-pencil"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($pegawai_terbaru)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Belum ada data SDM.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
