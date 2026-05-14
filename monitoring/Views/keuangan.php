<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex align-items-center">
            <a href="<?= base_url('monitoring') ?>" class="btn btn-light rounded-circle me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h3 class="fw-bold text-dark mb-0"><?= $title ?></h3>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-4">20 Transaksi Terakhir (Jurnal Umum)</h6>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Nomor Jurnal</th>
                        <th>Keterangan</th>
                        <th>Jenis</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($jurnal as $j): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($j['tanggal'])) ?></td>
                        <td><span class="badge bg-light text-dark fw-bold border"><?= $j['nomor_jurnal'] ?></span></td>
                        <td><?= $j['keterangan'] ?></td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">
                                <?= $j['jenis_jurnal'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
