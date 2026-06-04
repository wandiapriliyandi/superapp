<?php
use Poskestren\Models\StokMutasiModel;
?>
<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <a href="<?= base_url('poskestren/obat') ?>" class="btn btn-light rounded-circle me-2">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h3 class="fw-bold text-dark d-inline-block mb-0">Kartu Stok</h3>
                <p class="text-muted small mb-0 ms-5 ps-2">Riwayat masuk, keluar, dan stok setelah setiap transaksi</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('poskestren/stok/pengadaan') ?>" class="btn btn-success rounded-pill px-3">
                    <i class="bi bi-box-arrow-in-down me-1"></i> Pengadaan
                </a>
                <a href="<?= base_url('poskestren/stok/keluar') ?>" class="btn btn-outline-danger rounded-pill px-3">
                    <i class="bi bi-trash me-1"></i> Barang Keluar
                </a>
            </div>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label fw-bold small text-muted">FILTER OBAT</label>
                <select name="obat_id" class="form-select border-0 bg-light rounded-3" onchange="this.form.submit()">
                    <option value="">Semua obat</option>
                    <?php foreach ($obat as $o) : ?>
                        <option value="<?= $o['id'] ?>" <?= ($filter['obat_id'] ?? '') == $o['id'] ? 'selected' : '' ?>>
                            <?= esc($o['nama_obat']) ?> (stok: <?= (int) $o['stok'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">Waktu</th>
                        <th class="py-3">Obat</th>
                        <th class="py-3">Jenis</th>
                        <th class="py-3 text-center">±</th>
                        <th class="py-3 text-center">Stok</th>
                        <th class="py-3">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($mutasi)) : ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Belum ada mutasi stok.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($mutasi as $m) : ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="small fw-bold"><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></div>
                                </td>
                                <td><?= esc($m['nama_obat']) ?></td>
                                <td>
                                    <span class="badge bg-light text-dark rounded-pill">
                                        <?= StokMutasiModel::labelJenis($m['jenis'], $m['tipe']) ?>
                                    </span>
                                    <?php if ($m['referensi_tipe'] === 'kunjungan' && $m['referensi_id']) : ?>
                                        <a href="<?= base_url('poskestren/kunjungan/detail/' . $m['referensi_id']) ?>" class="small text-primary d-block">Kunjungan #<?= $m['referensi_id'] ?></a>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center fw-bold <?= $m['tipe'] === 'masuk' ? 'text-success' : 'text-danger' ?>">
                                    <?= $m['tipe'] === 'masuk' ? '+' : '-' ?><?= (int) $m['jumlah'] ?>
                                </td>
                                <td class="text-center small text-muted">
                                    <?= (int) $m['stok_sebelum'] ?> → <strong><?= (int) $m['stok_sesudah'] ?></strong>
                                </td>
                                <td class="pe-4"><?= esc($m['keterangan'] ?: '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
