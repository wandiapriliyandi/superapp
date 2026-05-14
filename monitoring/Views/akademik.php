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
        <h6 class="fw-bold mb-4">Rata-rata Nilai per Mata Pelajaran</h6>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>Mata Pelajaran</th>
                        <th>Rata-rata Nilai</th>
                        <th>Progress</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($summary as $s): ?>
                    <tr>
                        <td><?= $s['nama_mapel'] ?></td>
                        <td><span class="fw-bold"><?= number_format($s['rata_rata'], 2) ?></span></td>
                        <td style="width: 40%;">
                            <div class="progress" style="height: 10px; border-radius: 5px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $s['rata_rata'] ?>%"></div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
