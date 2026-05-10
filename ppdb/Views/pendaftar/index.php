<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Daftar Calon Santri</h5>
        <div class="d-flex gap-2">
            <a href="<?= base_url('ppdb/pendaftar/add') ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Pendaftar Baru
            </a>
            <div class="dropdown">
                <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i> Export Data
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                    <li><a class="dropdown-item" href="<?= base_url('ppdb/pendaftar/export-excel?' . http_build_query(service('request')->getGet())) ?>"><i class="bi bi-file-earmark-excel me-2 text-success"></i> Excel (.xlsx)</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('ppdb/pendaftar/export-word?' . http_build_query(service('request')->getGet())) ?>"><i class="bi bi-file-earmark-word me-2 text-primary"></i> Word (.doc)</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('ppdb/pendaftar/export-pdf?' . http_build_query(service('request')->getGet())) ?>" target="_blank"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i> PDF / Cetak</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-body p-4">
        <!-- Filter Form -->
        <form action="" method="GET" class="row g-3 mb-4 p-3 bg-body-tertiary rounded shadow-sm border">
            <div class="col-md-8">
                <label class="form-label fw-bold">Filter Status Seleksi</label>
                <select name="status" class="form-select border-0 shadow-sm">
                    <option value="">Semua Status</option>
                    <option value="Pending" <?= service('request')->getGet('status') == 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Lulus" <?= service('request')->getGet('status') == 'Lulus' ? 'selected' : '' ?>>Lulus</option>
                    <option value="Tidak Lulus" <?= service('request')->getGet('status') == 'Tidak Lulus' ? 'selected' : '' ?>>Tidak Lulus</option>
                    <option value="Santri Terdaftar" <?= service('request')->getGet('status') == 'Santri Terdaftar' ? 'selected' : '' ?>>Santri Terdaftar</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 shadow-sm">🔍 Terapkan Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No. Daftar</th>
                        <th>Nama Lengkap</th>
                        <th>JK</th>
                        <th>Tgl Daftar</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($pendaftar as $p): ?>
                    <tr>
                        <td class="fw-bold text-primary"><?= $p['nomor_pendaftaran'] ?></td>
                        <td><?= $p['nama_lengkap'] ?></td>
                        <td><?= $p['jenis_kelamin'] ?></td>
                        <td><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
                        <td>
                            <?php 
                                $badge = 'secondary';
                                if($p['status_seleksi'] == 'Lulus') $badge = 'success';
                                if($p['status_seleksi'] == 'Tidak Lulus') $badge = 'danger';
                                if($p['status_seleksi'] == 'Pending') $badge = 'warning';
                                if($p['status_seleksi'] == 'Santri Terdaftar') $badge = 'primary';
                            ?>
                            <span class="badge bg-<?= $badge ?>"><?= $p['status_seleksi'] ?></span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-3">
                                <!-- Tombol Bayar -->
                                <a href="<?= base_url('ppdb/pendaftar/pembayaran/'.$p['id']) ?>" 
                                   class="btn btn-sm btn-success text-white d-flex align-items-center gap-1 rounded-pill px-3 shadow-sm" title="Bayar Pendaftaran">
                                    <i class="bi bi-cash-coin"></i> <span>Bayar</span>
                                </a>

                                <!-- Tombol History -->
                                <a href="<?= base_url('ppdb/pendaftar/show/'.$p['id']) ?>" 
                                   class="btn btn-sm btn-info text-white d-flex align-items-center gap-1 rounded-pill px-3 shadow-sm" title="Riwayat Santri">
                                    <i class="bi bi-eye"></i> <span>History</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($pendaftar)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Tidak ada data pendaftar.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
