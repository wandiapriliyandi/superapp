<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row g-3">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h3 class="fw-bold text-primary mb-4"><?= $title ?></h3>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="p-3 bg-primary bg-opacity-10 border border-primary border-opacity-10 rounded-4 text-center">
                        <div class="small text-primary fw-bold mb-1">TOTAL IZIN</div>
                        <h2 class="fw-bold mb-0"><?= count($perijinan) ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-warning bg-opacity-10 border border-warning border-opacity-10 rounded-4 text-center">
                        <div class="small text-warning fw-bold mb-1">PENDING</div>
                        <h2 class="fw-bold mb-0">
                            <?= count(array_filter($perijinan, function($p){ return $p['status'] == 'Pending'; })) ?>
                        </h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-success bg-opacity-10 border border-success border-opacity-10 rounded-4 text-center">
                        <div class="small text-success fw-bold mb-1">DI PESANTREN</div>
                        <h2 class="fw-bold mb-0">
                            <?= count(array_filter($perijinan, function($p){ return $p['status'] == 'Kembali'; })) ?>
                        </h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-danger bg-opacity-10 border border-danger border-opacity-10 rounded-4 text-center">
                        <div class="small text-danger fw-bold mb-1">TERLAMBAT</div>
                        <h2 class="fw-bold mb-0">
                            <?= count(array_filter($perijinan, function($p){ return $p['is_terlambat']; })) ?>
                        </h2>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Santri</th>
                            <th>Jenis Izin</th>
                            <th>Mulai</th>
                            <th>Estimasi Kembali</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($perijinan as $p): ?>
                        <tr>
                            <td>
                                <strong><?= $p['nama_santri'] ?></strong><br>
                                <small class="text-muted"><?= $p['nis'] ?></small>
                            </td>
                            <td><?= $p['jenis_izin'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($p['tanggal_mulai'])) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($p['tanggal_selesai'])) ?></td>
                            <td>
                                <span class="badge bg-<?= $p['is_terlambat'] ? 'danger' : 'light text-dark' ?> rounded-pill">
                                    <?= $p['status'] ?> <?= $p['is_terlambat'] ? '(Terlambat)' : '' ?>
                                </span>
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
