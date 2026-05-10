<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 bg-primary bg-gradient text-white p-4 mb-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1"><?= $j['nama_tes'] ?></h4>
                    <p class="mb-0 opacity-75"><i class="bi bi-geo-alt me-1"></i> <?= $j['tempat'] ?> | <i class="bi bi-calendar3 me-1"></i> <?= date('d M Y', strtotime($j['tanggal'])) ?> | <i class="bi bi-clock me-1"></i> <?= $j['jam'] ?></p>
                </div>
                <div class="text-end">
                    <div class="small opacity-75">Kuota</div>
                    <h3 class="fw-bold mb-0"><?= count($mapping) ?> / <?= $j['kuota'] ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-0">Daftar Pendaftar (Status: Pending)</h5>
                <p class="text-muted small mb-0">Klik "Jadwalkan" untuk memasukkan santri ke jadwal ini, lalu tentukan hasilnya.</p>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nomer / Nama Santri</th>
                                <th>No. HP Ortu</th>
                                <th class="text-center">Status Plotting</th>
                                <th class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($pendaftar as $p): 
                                $is_scheduled = isset($mapping[$p['id']]);
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?= $p['nama_lengkap'] ?></div>
                                    <div class="small text-muted"><?= $p['nomor_pendaftaran'] ?></div>
                                </td>
                                <td><?= $p['no_hp_ortu'] ?></td>
                                <td class="text-center">
                                    <?php if($is_scheduled): ?>
                                        <span class="badge bg-success rounded-pill px-3"><i class="bi bi-calendar-check me-1"></i> Terjadwal</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border rounded-pill px-3">Belum Terjadwal</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if(!$is_scheduled): ?>
                                        <form action="<?= base_url('ppdb/jadwal/add-peserta') ?>" method="POST">
                                            <input type="hidden" name="id_jadwal" value="<?= $j['id'] ?>">
                                            <input type="hidden" name="id_pendaftar" value="<?= $p['id'] ?>">
                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm fw-bold">
                                                <i class="bi bi-calendar-plus me-1"></i> Jadwalkan
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <div class="d-flex gap-3 justify-content-center">
                                            <button type="button" 
                                                class="btn btn-success btn-sm rounded-pill px-3 shadow-sm fw-bold"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#statusModal"
                                                data-href="<?= base_url('ppdb/pendaftar/status/'.$p['id'].'/lulus') ?>"
                                                data-title="Luluskan Santri?"
                                                data-message="Santri akan dinyatakan LULUS dan otomatis terdaftar di database Akademik."
                                                data-color="success"
                                                data-icon="bi-check-circle-fill">
                                                <i class="bi bi-check-lg me-1"></i> Lulus
                                            </button>

                                            <button type="button" 
                                                class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm fw-bold"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#statusModal"
                                                data-href="<?= base_url('ppdb/pendaftar/status/'.$p['id'].'/gagal') ?>"
                                                data-title="Gagalkan Santri?"
                                                data-message="Santri akan dinyatakan TIDAK LULUS seleksi PPDB."
                                                data-color="danger"
                                                data-icon="bi-x-circle-fill">
                                                <i class="bi bi-x-lg me-1"></i> Gagal
                                            </button>
                                            
                                            <a href="<?= base_url('ppdb/jadwal/remove-peserta/'.$mapping[$p['id']]) ?>" class="btn btn-link btn-sm text-muted text-decoration-none p-0 ms-2" title="Batalkan Jadwal">
                                                <i class="bi bi-trash fs-5"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($pendaftar)): ?>
                                <tr>
                                    <td colspan="4" class="text-center p-5 text-muted">Tidak ada pendaftar dengan status Pending.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
