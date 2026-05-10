<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 text-center p-4 mb-4">
            <h5 class="fw-bold mb-1"><?= $p['nama_lengkap'] ?></h5>
            <p class="text-muted small">ID: <?= $p['nomor_pendaftaran'] ?></p>
            <hr>
            <div class="text-start">
                <p class="mb-1 small fw-bold">No. HP Ortu: <span class="fw-normal"><?= $p['no_hp_ortu'] ?></span></p>
                <p class="mb-1 small fw-bold">Alamat: <span class="fw-normal"><?= $p['alamat'] ?></span></p>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-0">Kelengkapan Berkas Fisik</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Berkas</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($syarat as $s): 
                            $status_berkas = 'Belum Ada';
                            $id_berkas = null;
                            foreach($uploaded as $up) {
                                if($up['jenis_berkas'] == $s['nama_berkas']) {
                                    $status_berkas = $up['status'];
                                    $id_berkas = $up['id'];
                                }
                            }
                        ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?= $s['nama_berkas'] ?></div>
                                <?= $s['is_wajib'] ? '<span class="text-danger small">*Wajib</span>' : '<span class="text-muted small">Opsional</span>' ?>
                            </td>
                            <td>
                                <?php if($status_berkas == 'Valid'): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Sudah Ada</span>
                                <?php elseif($status_berkas == 'Invalid'): ?>
                                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Tidak Sesuai</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Belum Ada</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form action="<?= base_url('ppdb/berkas/update-status') ?>" method="POST" class="d-flex gap-2">
                                    <input type="hidden" name="id_pendaftar" value="<?= $p['id'] ?>">
                                    <input type="hidden" name="jenis_berkas" value="<?= $s['nama_berkas'] ?>">
                                    <input type="hidden" name="id_berkas" value="<?= $id_berkas ?>">
                                    
                                    <select name="status" class="form-select form-select-sm border-0 bg-body-tertiary shadow-sm" onchange="this.form.submit()">
                                        <option value="Pending" <?= $status_berkas == 'Pending' ? 'selected' : '' ?>>-- Pilih --</option>
                                        <option value="Valid" <?= $status_berkas == 'Valid' ? 'selected' : '' ?>>Ada</option>
                                        <option value="Invalid" <?= $status_berkas == 'Invalid' ? 'selected' : '' ?>>Tidak Sesuai</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tombol Finalisasi -->
        <div class="card border-0 shadow-sm rounded-4 mt-4 bg-light">
            <div class="card-body p-4 text-center">
                <h6 class="fw-bold mb-2">Semua berkas sudah lengkap?</h6>
                <p class="small text-muted mb-3">Klik tombol di bawah untuk menyelesaikan proses administrasi dan mendaftarkan santri secara resmi.</p>
                <button type="button" 
                    class="btn btn-success rounded-pill px-5 shadow fw-bold"
                    data-bs-toggle="modal" 
                    data-bs-target="#statusModal"
                    data-href="<?= base_url('ppdb/pendaftar/status/'.$p['id'].'/terdaftar') ?>"
                    data-title="Finalisasi Pendaftaran?"
                    data-message="Pendaftar akan resmi menjadi Santri Aktif. Data akan dipindahkan ke modul Akademik."
                    data-color="success"
                    data-icon="bi-award-fill">
                    <i class="bi bi-person-check-fill me-1"></i> Selesaikan Berkas & Daftarkan Santri
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
