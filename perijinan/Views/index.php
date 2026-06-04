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
                                            <?php if ($p['status'] == 'Kembali' && !empty($p['waktu_kembali'])) : ?>
                                                <br><span class="text-success fw-bold">Kembali:</span> <?= date('d M Y H:i', strtotime($p['waktu_kembali'])) ?>
                                            <?php endif; ?>
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
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm" 
                                                type="button" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#actionModal"
                                                data-id="<?= $p['id'] ?>"
                                                data-nama="<?= htmlspecialchars($p['nama_santri'], ENT_QUOTES, 'UTF-8') ?>"
                                                data-status="<?= $p['status'] ?>">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
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

<?= $this->section('modals') ?>
<!-- Modal Pilihan Aksi Dinamis -->
<div class="modal fade" id="actionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" id="actionModalTitle">Pilihan Aksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">Pilih aksi untuk santri: <strong id="actionModalSantriName" class="text-dark"></strong></p>
                <div class="d-grid gap-2" id="actionModalButtons">
                    <!-- Tombol-tombol aksi akan diisi secara dinamis oleh JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reject Dinamis -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form id="rejectForm" action="" method="post">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold">Tolak Perijinan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan Penolakan</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Masukkan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light px-4 py-2 fw-semibold" data-bs-dismiss="modal" style="border-radius: 12px;">Batal</button>
                    <button type="submit" class="btn btn-danger px-4 py-2 fw-semibold" style="border-radius: 12px;">Tolak Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        const actionModal = document.getElementById('actionModal');
        if (actionModal) {
            actionModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama');
                const status = button.getAttribute('data-status');
                
                // Set nama santri
                document.getElementById('actionModalSantriName').innerText = nama;
                
                // Kontainer tombol
                const container = document.getElementById('actionModalButtons');
                container.innerHTML = ''; // Kosongkan terlebih dahulu
                
                let buttonsHTML = '';
                const baseUrl = '<?= base_url() ?>';
                
                if (status === 'Pending') {
                    buttonsHTML += `
                        <a href="${baseUrl}perijinan/approve/${id}" class="btn btn-outline-success py-2 text-start px-3 d-flex align-items-center rounded-3 mb-2 fw-semibold">
                            <i class="bi bi-check-circle me-3 fs-5"></i> Setujui Perizinan
                        </a>
                        <button type="button" class="btn btn-outline-danger py-2 text-start px-3 d-flex align-items-center rounded-3 mb-2 fw-semibold btn-reject-trigger" data-id="${id}">
                            <i class="bi bi-x-circle me-3 fs-5"></i> Tolak Perizinan
                        </button>
                    `;
                } else if (status === 'Disetujui') {
                    buttonsHTML += `
                        <a href="${baseUrl}perijinan/aktifkan/${id}" class="btn btn-outline-primary py-2 text-start px-3 d-flex align-items-center rounded-3 mb-2 fw-semibold">
                            <i class="bi bi-door-open me-3 fs-5"></i> Mulai Berangkat (Keluar)
                        </a>
                    `;
                } else if (status === 'Aktif') {
                    buttonsHTML += `
                        <a href="${baseUrl}perijinan/kembali/${id}" class="btn btn-outline-success py-2 text-start px-3 d-flex align-items-center rounded-3 mb-2 fw-semibold">
                            <i class="bi bi-house-door me-3 fs-5"></i> Konfirmasi Kembali (Pulang)
                        </a>
                    `;
                }
                
                // Selalu ada opsi Cetak Surat Izin dan Hapus
                buttonsHTML += `
                    <a href="#" class="btn btn-outline-secondary py-2 text-start px-3 d-flex align-items-center rounded-3 mb-2 fw-semibold">
                        <i class="bi bi-printer me-3 fs-5"></i> Cetak Surat Izin
                    </a>
                    <button type="button" class="btn btn-danger py-2 text-start px-3 d-flex align-items-center rounded-3 fw-semibold btn-delete-trigger" data-id="${id}">
                        <i class="bi bi-trash me-3 fs-5"></i> Hapus Data Perizinan
                    </button>
                `;
                
                container.innerHTML = buttonsHTML;
                
                // Handler tombol Tolak di dalam modal aksi
                $('.btn-reject-trigger').off('click').on('click', function() {
                    const rejectId = $(this).attr('data-id');
                    const modalActionInstance = bootstrap.Modal.getInstance(actionModal);
                    modalActionInstance.hide();
                    
                    setTimeout(() => {
                        $('#rejectForm').attr('action', `${baseUrl}perijinan/reject/${rejectId}`);
                        const rejectModalInstance = new bootstrap.Modal(document.getElementById('rejectModal'));
                        rejectModalInstance.show();
                    }, 350);
                });
                
                // Handler tombol Hapus di dalam modal aksi
                $('.btn-delete-trigger').off('click').on('click', function() {
                    const deleteId = $(this).attr('data-id');
                    const modalActionInstance = bootstrap.Modal.getInstance(actionModal);
                    modalActionInstance.hide();
                    
                    setTimeout(() => {
                        $('#confirmDeleteBtn').attr('href', `${baseUrl}perijinan/hapus/${deleteId}`);
                        const deleteModalInstance = new bootstrap.Modal(document.getElementById('deleteModal'));
                        deleteModalInstance.show();
                    }, 350);
                });
            });
        }
    });
</script>
<?= $this->endSection() ?>
