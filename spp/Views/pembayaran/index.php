<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Riwayat Pembayaran SPP</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th>Nama Santri</th>
                                <th>Metode</th>
                                <th>Nominal</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pembayaran as $p): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?= date('d/m/Y', strtotime($p['tanggal_bayar'])) ?></div>
                                    <small class="text-muted"><?= date('H:i', strtotime($p['created_at'])) ?></small>
                                </td>
                                <td><?= $p['nama_santri'] ?></td>
                                <td><span class="badge bg-info text-white"><?= $p['metode_pembayaran'] ?></span></td>
                                <td class="fw-bold text-success">Rp <?= number_format($p['nominal_bayar'], 0, ',', '.') ?></td>
                                <td><?= $p['keterangan'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($pembayaran)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat pembayaran.</td>
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
