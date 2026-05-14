<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <a href="<?= base_url('poskestren') ?>" class="btn btn-light rounded-circle me-2">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h3 class="fw-bold text-dark d-inline-block mb-0">Data Kunjungan Santri</h3>
            </div>
            <a href="<?= base_url('poskestren/kunjungan/tambah') ?>" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-plus-lg me-2"></i> Tambah Kunjungan
            </a>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">No</th>
                        <th class="py-3">Waktu Kunjungan</th>
                        <th class="py-3">Santri</th>
                        <th class="py-3">Keluhan</th>
                        <th class="py-3">Diagnosa</th>
                        <th class="py-3">Status</th>
                        <th class="pe-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kunjungan)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Belum ada data kunjungan.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($kunjungan as $idx => $k): ?>
                        <tr>
                            <td class="ps-4"><?= $idx + 1 ?></td>
                            <td>
                                <div class="fw-bold"><?= date('d/m/Y', strtotime($k['tgl_kunjungan'])) ?></div>
                                <div class="text-muted small"><?= date('H:i', strtotime($k['tgl_kunjungan'])) ?></div>
                            </td>
                            <td>
                                <div class="fw-bold"><?= $k['nama_santri'] ?></div>
                                <div class="text-muted small"><?= $k['nis'] ?></div>
                            </td>
                            <td><?= substr($k['keluhan'], 0, 100) ?></td>
                            <td><?= $k['diagnosa'] ?: '-' ?></td>
                            <td>
                                <?php 
                                    $badge_class = 'bg-success';
                                    if($k['status'] == 'Observasi') $badge_class = 'bg-warning';
                                    if($k['status'] == 'Rujuk') $badge_class = 'bg-danger';
                                ?>
                                <span class="badge <?= $badge_class ?> bg-opacity-10 text-<?= str_replace('bg-', '', $badge_class) ?> rounded-pill">
                                    <?= $k['status'] ?>
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="<?= base_url('poskestren/kunjungan/detail/' . $k['id']) ?>" class="btn btn-sm btn-info text-white rounded-pill px-3">
                                    <i class="bi bi-info-circle me-1"></i> Detail
                                </a>
                                <a href="<?= base_url('poskestren/kunjungan/hapus/' . $k['id']) ?>" class="btn btn-sm btn-light text-danger rounded-pill px-3 ms-1" onclick="return confirm('Hapus data ini?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
