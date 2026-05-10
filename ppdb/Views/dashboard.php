<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white h-100 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 op-7">Total Pendaftar</h6>
                    <h2 class="mb-0 fw-bold"><?= $stats['total'] ?></h2>
                </div>
                <div class="fs-1 op-5"><i class="bi bi-people"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-dark h-100 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 op-7">Pending Seleksi</h6>
                    <h2 class="mb-0 fw-bold"><?= $stats['pending'] ?></h2>
                </div>
                <div class="fs-1 op-5"><i class="bi bi-hourglass-split"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white h-100 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 op-7">Lulus Seleksi</h6>
                    <h2 class="mb-0 fw-bold"><?= $stats['lulus'] ?></h2>
                </div>
                <div class="fs-1 op-5"><i class="bi bi-check-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-danger text-white h-100 p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 op-7">Tidak Lulus</h6>
                    <h2 class="mb-0 fw-bold"><?= $stats['tidak_lulus'] ?></h2>
                </div>
                <div class="fs-1 op-5"><i class="bi bi-x-circle"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0">Informasi Gelombang Saat Ini</h5>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nama Gelombang</th>
                        <th>Kuota</th>
                        <th>Tgl Buka</th>
                        <th>Tgl Tutup</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($gelombang as $g): ?>
                    <tr>
                        <td class="fw-bold"><?= $g['gelombang'] ?></td>
                        <td><?= $g['kuota'] ?> Santri</td>
                        <td><?= date('d M Y', strtotime($g['tgl_buka'])) ?></td>
                        <td><?= date('d M Y', strtotime($g['tgl_tutup'])) ?></td>
                        <td>
                            <span class="badge bg-<?= $g['status'] == 'Buka' ? 'success' : 'danger' ?>">
                                <?= $g['status'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($gelombang)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada data gelombang.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
