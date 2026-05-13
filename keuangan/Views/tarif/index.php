<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Daftar Tarif SPP</h5>
                <a href="<?= base_url('keuangan/tarif/add') ?>" class="btn btn-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Tarif
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Tahun Ajaran</th>
                                <th>Nama Tarif</th>
                                <th>Tipe</th>
                                <th>Nominal</th>
                                <th>Keterangan</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tarif as $index => $t): ?>
                            <tr>
                                <td class="ps-4"><?= $index + 1 ?></td>
                                <td><?= $t['nama_tahun'] ?: '<span class="text-muted">-</span>' ?></td>
                                <td class="fw-bold"><?= $t['nama_tarif'] ?></td>
                                <td>
                                    <span class="badge rounded-pill <?= $t['tipe'] == 'Bulanan' ? 'bg-info text-dark' : 'bg-warning text-dark' ?>">
                                        <?= $t['tipe'] ?>
                                    </span>
                                </td>
                                <td>Rp <?= number_format($t['nominal'], 0, ',', '.') ?></td>
                                <td><?= $t['keterangan'] ?></td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="<?= base_url('keuangan/tarif/edit/' . $t['id']) ?>" class="btn btn-sm btn-outline-primary rounded-circle me-1" title="Edit Tarif">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="<?= base_url('keuangan/tarif/delete/' . $t['id']) ?>" class="btn btn-sm btn-outline-danger rounded-circle" onclick="return confirm('Hapus tarif ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($tarif)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada data tarif.</td>
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
