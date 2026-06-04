<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h3 class="fw-bold text-dark mb-1">Dashboard Poskestren</h3>
                <p class="text-muted">Pantau kesehatan santri dan stok obat secara real-time.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('poskestren/kunjungan/tambah') ?>" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-plus-lg me-2"></i> Kunjungan Baru
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-4 p-3">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                    <span class="badge bg-light text-dark rounded-pill">Total</span>
                </div>
                <h2 class="fw-bold mb-1"><?= $stats['total_kunjungan'] ?></h2>
                <p class="text-muted small mb-0">Total Kunjungan</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-4 p-3">
                        <i class="bi bi-calendar-check fs-4"></i>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill">Hari Ini</span>
                </div>
                <h2 class="fw-bold mb-1"><?= $stats['kunjungan_hari_ini'] ?></h2>
                <p class="text-muted small mb-0">Kunjungan Hari Ini</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-4 p-3">
                        <i class="bi bi-capsule fs-4"></i>
                    </div>
                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">Low Stock</span>
                </div>
                <h2 class="fw-bold mb-1"><?= $stats['stok_obat_low'] ?></h2>
                <p class="text-muted small mb-0">Obat Stok Menipis</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-4 p-3">
                        <i class="bi bi-heart-pulse fs-4"></i>
                    </div>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">Observasi</span>
                </div>
                <h2 class="fw-bold mb-1"><?= $stats['santri_sakit'] ?></h2>
                <p class="text-muted small mb-0">Santri Dalam Observasi</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Kolom Kiri: Santri Sedang Sakit -->
    <div class="col-md-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 p-4 pb-2 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1"><i class="bi bi-heart-pulse-fill text-danger me-2"></i>Santri Sedang Sakit</h5>
                    <p class="text-muted small mb-0">Daftar santri aktif dalam perawatan poskestren saat ini</p>
                </div>
                <span class="badge bg-danger rounded-pill px-3 py-1"><?= count($santri_sakit_list) ?> Orang</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th class="ps-4 py-3">Santri</th>
                                <th class="py-3">Mulai Sakit</th>
                                <th class="py-3">Status</th>
                                <th class="pe-4 py-3 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($santri_sakit_list)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-emoji-smile fs-1 text-success d-block mb-2"></i>
                                    Alhamdulillah, saat ini tidak ada santri yang sakit.
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($santri_sakit_list as $k): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-primary"><?= esc($k['nama_santri']) ?></div>
                                        <div class="text-muted small"><?= esc($k['nis']) ?> | <?= esc($k['nama_kelas'] ?: '-') ?></div>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold"><?= date('d/m Y', strtotime($k['tgl_kunjungan'])) ?></div>
                                        <div class="text-muted extra-small"><?= date('H:i', strtotime($k['tgl_kunjungan'])) ?> WIB</div>
                                    </td>
                                    <td>
                                        <?php 
                                            $badge_class = 'bg-warning text-dark';
                                            $status_label = 'Sakit';
                                            if(($k['status'] ?? 'Sakit') == 'Observasi') {
                                                $badge_class = 'bg-danger text-white';
                                                $status_label = 'Observasi';
                                            }
                                        ?>
                                        <span class="badge <?= $badge_class ?> rounded-pill px-3 py-1 small">
                                            <?= $status_label ?>
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="btn-group gap-1">
                                            <a href="<?= base_url('poskestren/kunjungan/detail/' . $k['id']) ?>" class="btn btn-sm btn-light rounded-circle" title="Detail & Update">
                                                <i class="bi bi-pencil-square text-primary"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Kunjungan Terakhir -->
    <div class="col-md-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 p-4 pb-2">
                <h5 class="fw-bold mb-1">Kunjungan Terakhir</h5>
                <p class="text-muted small mb-0">Log aktivitas rekam medis terbaru hari ini</p>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th class="ps-4 py-3">Waktu</th>
                                <th class="py-3">Santri</th>
                                <th class="pe-4 py-3 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_kunjungan)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">Belum ada kunjungan terbaru.</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($recent_kunjungan as $k): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold small"><?= date('d M', strtotime($k['tgl_kunjungan'])) ?></div>
                                        <div class="text-muted extra-small"><?= date('H:i', strtotime($k['tgl_kunjungan'])) ?> WIB</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-truncate" style="max-width: 150px;"><?= esc($k['nama_santri']) ?></div>
                                        <div>
                                            <?php 
                                                $badge_class = 'bg-success';
                                                if($k['status'] == 'Observasi') $badge_class = 'bg-warning text-dark';
                                                if($k['status'] == 'Rujuk') $badge_class = 'bg-danger';
                                                if($k['status'] == 'Sakit') $badge_class = 'bg-info text-dark';
                                            ?>
                                            <span class="badge <?= $badge_class ?> rounded-pill" style="font-size: 0.65rem;">
                                                <?= $k['status'] ?: 'Sakit' ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="<?= base_url('poskestren/kunjungan/detail/' . $k['id']) ?>" class="btn btn-sm btn-light rounded-circle">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 p-3 text-center border-top">
                <a href="<?= base_url('poskestren/kunjungan') ?>" class="btn btn-light rounded-pill px-4 btn-sm fw-bold text-primary">Lihat Semua Kunjungan</a>
            </div>
        </div>
    </div>
</div>

<style>
    .extra-small { font-size: 0.75rem; }
</style>
<?= $this->endSection() ?>
