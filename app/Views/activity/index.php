<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">📜 Riwayat Aktivitas Sistem</h5>
                <button onclick="window.location.reload()" class="btn btn-light btn-sm border">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu</th>
                                <th>Pengguna</th>
                                <th>Modul</th>
                                <th>Aktivitas</th>
                                <th>Detail</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($logs)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">Belum ada rekaman aktivitas.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($logs as $log): ?>
                                <tr>
                                    <td class="small text-muted">
                                        <?= date('d/m/y H:i:s', strtotime($log['created_at'])) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-dark"><?= $log['user'] ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= $log['module'] ?></span>
                                    </td>
                                    <td class="fw-bold"><?= $log['activity'] ?></td>
                                    <td class="small"><?= $log['details'] ?></td>
                                    <td class="small text-muted font-monospace"><?= $log['ip_address'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
