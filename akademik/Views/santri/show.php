<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
            <div class="card-header bg-primary p-4 text-white border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white rounded shadow-sm overflow-hidden" style="width: 80px; height: 100px;">
                            <?php if($santri['foto']): ?>
                                <img src="<?= base_url('uploads/santri/'.$santri['foto']) ?>" class="w-100 h-100" style="object-fit: cover;">
                            <?php else: ?>
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                    <i class="bi bi-person-fill text-muted fs-1"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0"><?= $santri['nama_lengkap'] ?></h4>
                            <p class="mb-0 opacity-75">NISN: <?= $santri['nisn'] ?: '-' ?></p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-white text-primary px-3 py-2 fs-6 rounded-pill"><?= $santri['status_santri'] ?></span>
                        <?php $qrData = $santri['nisn'] ?: ($santri['nis'] ?: 'ID-'.$santri['id']); ?>
                        <div class="bg-white p-1 rounded shadow-sm text-center" title="Scan QR: <?= esc($qrData) ?>">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=<?= urlencode($qrData) ?>" width="60" height="60" alt="QR Code">
                            <div style="font-size: 9px; color: #666; margin-top: 2px; line-height: 1;" class="fw-bold">QR NISN</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-5">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold">Jenis Kelamin</label>
                        <p class="fs-5 fw-semibold"><?= $santri['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold">Tempat, Tanggal Lahir</label>
                        <p class="fs-5 fw-semibold"><?= $santri['tempat_lahir'] ?>, <?= date('d F Y', strtotime($santri['tanggal_lahir'])) ?></p>
                    </div>
                    <div class="col-md-12">
                        <hr class="my-2 opacity-10">
                    </div>
                    <div class="col-md-12">
                        <label class="text-muted small text-uppercase fw-bold">Alamat Lengkap</label>
                        <p class="fs-5 fw-semibold"><?= $santri['alamat'] ?: 'Alamat belum diisi.' ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold">Nomor HP / Kontak</label>
                        <p class="fs-5 fw-semibold"><?= $santri['no_hp'] ?: '-' ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold">Tahun Terdaftar</label>
                        <p class="fs-5 fw-semibold"><?= $santri['nama_tahun_ajaran'] ?? ($santri['id_tahun_ajaran'] ?? '-') ?></p>
                    </div>
                </div>

                <div class="mt-5 d-flex gap-2">
                    <a href="<?= base_url('akademik/santri') ?>" class="btn btn-light px-4 py-2 fw-semibold border">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <a href="<?= base_url('akademik/santri/edit/'.$santri['id']) ?>" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
                        <i class="bi bi-pencil-square"></i> Edit Profil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
