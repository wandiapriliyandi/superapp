<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Pemetaan Tarif & Kesepakatan Bayaran</h5>
                <p class="text-muted small mb-0">Pilih santri untuk mengatur jenis tagihan yang harus dibayar setiap bulannya.</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="table-mapping">
                        <thead>
                            <tr>
                                <th>Santri</th>
                                <th>Jenis Kelamin</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($santri as $s): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?= $s['nama_lengkap'] ?></div>
                                    <small class="text-muted">NISN: <?= $s['nisn'] ?></small>
                                </td>
                                <td><?= $s['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?= base_url('keuangan/mapping/santri/' . $s['id']) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 me-1">
                                            <i class="bi bi-gear me-1"></i> Atur Tarif
                                        </a>
                                        <a href="<?= base_url('keuangan/tagihan/generate-santri/' . $s['id']) ?>" class="btn btn-primary btn-sm rounded-pill px-3" title="Generate tagihan bulan ini">
                                            <i class="bi bi-magic me-1"></i> Generate
                                        </a>
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
</div>
<?= $this->endSection() ?>
