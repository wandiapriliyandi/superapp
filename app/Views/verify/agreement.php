<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center pt-4">
    <div class="col-md-6 col-sm-12">
        <div class="card border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="card-header bg-primary p-4 text-white text-center border-0">
                <div class="mb-2">
                    <i class="bi bi-shield-check" style="font-size: 3rem;"></i>
                </div>
                <h4 class="fw-bold mb-0">KESEPAKATAN VALID</h4>
                <p class="mb-0 opacity-75">Data Pemetaan Tarif Terverifikasi</p>
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Nama Santri</div>
                        <div class="col-7 fw-bold text-end"><?= $santri['nama_lengkap'] ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">NIS / NISN</div>
                        <div class="col-7 fw-bold text-end"><?= $santri['nis'] ?> / <?= $santri['nisn'] ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Status</div>
                        <div class="col-7 fw-bold text-end text-primary">Aktif</div>
                    </div>
                </div>

                <h6 class="fw-bold mb-3 mt-4 text-dark"><i class="bi bi-list-stars me-2 text-primary"></i>Daftar Kesepakatan Biaya</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Jenis Biaya</th>
                                <th>Tipe</th>
                                <th class="text-end">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($mapping as $m): ?>
                            <tr>
                                <td class="fw-bold text-dark"><?= $m['nama_tarif'] ?></td>
                                <td><?= $m['tipe'] ?></td>
                                <td class="text-end fw-bold">Rp <?= number_format($m['nominal'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info text-center border-0" style="border-radius: 12px; background: rgba(13, 110, 253, 0.05); color: #0d6efd;">
                    <small>Data ini menunjukkan rincian biaya pendidikan yang telah disepakati antara pihak pesantren dan wali santri.</small>
                </div>
            </div>
            <div class="card-footer bg-white p-4 border-0 text-center">
                <p class="mb-0 text-muted small">&copy; <?= date('Y') ?> Pesantren Digital SuperApp</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
