<?php 
/** @var array $perijinan */
/** @var string $title */
?>
<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-primary mb-0"><?= $title ?></h3>
                    <p class="text-muted small">Kelola izin keluar masuk santri dengan mudah dan terpantau.</p>
                </div>
                <a href="<?= base_url('perijinan/tambah') ?>" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm">
                    <i class="bi bi-plus-circle me-2"></i> Ajukan Izin Baru
                </a>
            </div>

            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 rounded-start">No</th>
                            <th class="border-0">Santri</th>
                            <th class="border-0">Jenis Izin</th>
                            <th class="border-0">Waktu Izin</th>
                            <th class="border-0">Status</th>
                            <th class="border-0 rounded-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($perijinan)) : ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <img src="https://illustrations.popsy.co/blue/waiting-for-notification.svg" alt="Empty" style="height: 150px;" class="mb-3">
                                    <p class="mb-0">Belum ada pengajuan izin saat ini.</p>
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php $no = 1; foreach ($perijinan as $p) : ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary-subtle text-primary rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                                <?= strtoupper(substr($p['nama_santri'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold"><?= $p['nama_santri'] ?></div>
                                                <div class="small text-muted"><?= $p['nis'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill">
                                            <?= $p['jenis_izin'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <span class="text-muted">Dari:</span> <?= date('d M Y H:i', strtotime($p['tanggal_mulai'])) ?><br>
                                            <span class="text-muted">Sampai:</span> <?= date('d M Y H:i', strtotime($p['tanggal_selesai'])) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php 
                                        $statusClass = [
                                            'Pending'   => 'bg-warning text-dark',
                                            'Disetujui' => 'bg-success text-white',
                                            'Ditolak'   => 'bg-danger text-white',
                                            'Aktif'     => 'bg-primary text-white',
                                            'Kembali'   => 'bg-secondary text-white'
                                        ];
                                        ?>
                                        <div class="d-flex flex-column gap-1">
                                            <span class="badge <?= $statusClass[$p['status']] ?? 'bg-light text-dark' ?> px-3 py-2 rounded-pill shadow-sm">
                                                <?= $p['status'] ?>
                                            </span>
                                            <?php if ($p['is_terlambat']) : ?>
                                                <span class="badge bg-danger px-3 py-2 rounded-pill shadow-sm animate-pulse">
                                                    <i class="bi bi-alarm-fill me-1"></i> TERLAMBAT
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                                <?php if ($p['status'] == 'Pending') : ?>
                                                    <li><a class="dropdown-item py-2" href="<?= base_url('perijinan/approve/'.$p['id']) ?>"><i class="bi bi-check-lg text-success me-2"></i> Setujui</a></li>
                                                    <li><button class="dropdown-item py-2" data-bs-toggle="modal" data-bs-target="#modalReject<?= $p['id'] ?>"><i class="bi bi-x-lg text-danger me-2"></i> Tolak</button></li>
                                                <?php endif; ?>
                                                
                                                <?php if ($p['status'] == 'Disetujui') : ?>
                                                    <li><a class="dropdown-item py-2" href="<?= base_url('perijinan/aktifkan/'.$p['id']) ?>"><i class="bi bi-door-open text-primary me-2"></i> Mulai Berangkat</a></li>
                                                <?php endif; ?>

                                                <?php if ($p['status'] == 'Aktif') : ?>
                                                    <li><a class="dropdown-item py-2" href="<?= base_url('perijinan/kembali/'.$p['id']) ?>"><i class="bi bi-house-door text-success me-2"></i> Konfirmasi Kembali</a></li>
                                                <?php endif; ?>

                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item py-2" href="#"><i class="bi bi-printer text-muted me-2"></i> Cetak Surat Izin</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="<?= base_url('perijinan/hapus/'.$p['id']) ?>" onclick="return confirm('Hapus data ini?')"><i class="bi bi-trash me-2"></i> Hapus</a></li>
                                            </ul>
                                        </div>

                                        <!-- Modal Reject -->
                                        <div class="modal fade" id="modalReject<?= $p['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <form action="<?= base_url('perijinan/reject/'.$p['id']) ?>" method="post">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold">Tolak Perijinan</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Alasan Penolakan</label>
                                                                <textarea name="catatan" class="form-control" rows="3" placeholder="Masukkan alasan penolakan..." required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger">Tolak Sekarang</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
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

<style>
    .avatar-sm { font-size: 14px; }
    .bg-primary-subtle { background-color: #e0eaff; }
    .bg-info-subtle { background-color: #e0f2ff; }
    .dropdown-item:hover { background-color: #f8f9fa; }
    .card { transition: transform 0.2s; }
    .animate-pulse {
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.7; transform: scale(0.95); }
        100% { opacity: 1; transform: scale(1); }
    }
</style>
<?= $this->endSection() ?>
