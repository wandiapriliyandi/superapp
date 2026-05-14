<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <a href="<?= base_url('poskestren') ?>" class="btn btn-light rounded-circle me-2">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h3 class="fw-bold text-dark d-inline-block mb-0">Manajemen Obat</h3>
            </div>
            <a href="<?= base_url('poskestren/obat/tambah') ?>" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-plus-lg me-2"></i> Tambah Obat
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
                        <th class="py-3">Nama Obat</th>
                        <th class="py-3">Stok</th>
                        <th class="py-3">Satuan</th>
                        <th class="py-3">Deskripsi</th>
                        <th class="pe-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($obat)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada data obat.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($obat as $idx => $o): ?>
                        <tr>
                            <td class="ps-4"><?= $idx + 1 ?></td>
                            <td><div class="fw-bold"><?= $o['nama_obat'] ?></div></td>
                            <td>
                                <span class="badge <?= $o['stok'] < 10 ? 'bg-danger' : 'bg-success' ?> bg-opacity-10 text-<?= $o['stok'] < 10 ? 'danger' : 'success' ?> rounded-pill px-3">
                                    <?= $o['stok'] ?>
                                </span>
                            </td>
                            <td><?= $o['satuan'] ?></td>
                            <td><?= $o['deskripsi'] ?: '-' ?></td>
                            <td class="pe-4 text-end">
                                <a href="<?= base_url('poskestren/obat/edit/' . $o['id']) ?>" class="btn btn-sm btn-light text-primary rounded-pill px-3">
                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                </a>
                                <a href="<?= base_url('poskestren/obat/hapus/' . $o['id']) ?>" class="btn btn-sm btn-light text-danger rounded-pill px-3 ms-1" onclick="return confirm('Hapus obat ini?')">
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
