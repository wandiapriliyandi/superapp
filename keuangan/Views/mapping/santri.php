<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body text-center p-4">
                <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="bi bi-person-fill text-primary fs-1"></i>
                </div>
                <h5 class="fw-bold mb-1"><?= $santri['nama_lengkap'] ?></h5>
                <p class="text-muted small"><?= $santri['nisn'] ?></p>
                <hr>
                <div class="d-grid gap-2">
                    <a href="<?= base_url('keuangan/tagihan/generate-santri/' . $santri['id']) ?>" class="btn btn-primary rounded-pill">
                        <i class="bi bi-magic me-1"></i> Generate Tagihan Bln Ini
                    </a>
                    <a href="<?= base_url('keuangan/mapping/print/' . $santri['id']) ?>" target="_blank" class="btn btn-outline-dark rounded-pill">
                        <i class="bi bi-printer me-1"></i> Cetak PDF/Langsung
                    </a>
                    <a href="<?= base_url('keuangan/mapping/export-word/' . $santri['id']) ?>" class="btn btn-info text-white rounded-pill">
                        <i class="bi bi-file-earmark-word me-1"></i> Ekspor ke Word
                    </a>
                    <a href="<?= base_url('keuangan/mapping') ?>" class="btn btn-light rounded-pill">Kembali</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Daftar Tarif yang Berlaku</h5>
                <p class="text-muted small mb-0">Centang jenis pembayaran yang harus dibayar oleh santri ini.</p>
            </div>
            <div class="card-body">
                <form action="<?= base_url('keuangan/mapping/save') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="santri_id" value="<?= $santri['id'] ?>">
                    
                    <div class="list-group list-group-flush mb-4">
                        <?php foreach ($tarif as $t): ?>
                        <label class="list-group-item d-flex justify-content-between align-items-center py-3 border-0 rounded-3 mb-2 bg-body-tertiary">
                            <div class="d-flex align-items-center">
                                <input class="form-check-input me-3" type="checkbox" name="tarif_ids[]" value="<?= $t['id'] ?>" <?= in_array($t['id'], $current) ? 'checked' : '' ?>>
                                <div>
                                    <div class="fw-bold"><?= $t['nama_tarif'] ?></div>
                                    <div class="mb-1">
                                        <span class="badge bg-light text-dark border small"><?= $t['tipe'] ?></span>
                                        <?php if($t['nama_tahun']): ?>
                                            <span class="badge bg-light text-muted border small"><?= $t['nama_tahun'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted d-block"><?= $t['keterangan'] ?: 'Tidak ada keterangan' ?></small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="d-block fw-bold text-primary">Rp <?= number_format($t['nominal'], 0, ',', '.') ?></span>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2 fw-bold rounded-pill">
                            <i class="bi bi-save me-1"></i> Simpan Kesepakatan Bayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
