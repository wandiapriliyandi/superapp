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
                    <a href="<?= base_url('spp/tagihan/generate-santri/' . $santri['id']) ?>" class="btn btn-primary rounded-pill">
                        <i class="bi bi-magic me-1"></i> Generate Tagihan Bln Ini
                    </a>
                    <a href="<?= base_url('spp/mapping/print/' . $santri['id']) ?>" target="_blank" class="btn btn-outline-dark rounded-pill">
                        <i class="bi bi-printer me-1"></i> Cetak PDF/Langsung
                    </a>
                    <a href="<?= base_url('spp/mapping/export-word/' . $santri['id']) ?>" class="btn btn-info text-white rounded-pill">
                        <i class="bi bi-file-earmark-word me-1"></i> Ekspor ke Word
                    </a>
                    <a href="<?= base_url('spp/mapping') ?>" class="btn btn-light rounded-pill">Kembali</a>
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
                <form action="<?= base_url('spp/mapping/save') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="santri_id" value="<?= $santri['id'] ?>">
                    
                    <div class="list-group list-group-flush mb-4">
                        <?php foreach ($tarif as $t): 
                            $m = $mappedData[$t['id']] ?? null;
                        ?>
                        <div class="list-group-item py-3 border-0 rounded-3 mb-2 bg-body-tertiary">
                            <div class="d-flex justify-content-between align-items-start">
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
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="d-block fw-bold text-primary">Rp <?= number_format($t['nominal'], 0, ',', '.') ?></span>
                                </div>
                            </div>
                            
                            <!-- Opsi Beasiswa / Diskon -->
                            <div class="mt-3 ps-5 border-start border-primary border-3 ms-2">
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <label class="small text-muted mb-1 d-block">Potongan / Beasiswa (Rp)</label>
                                        <input type="number" name="nominal_diskon[<?= $t['id'] ?>]" class="form-control form-control-sm" placeholder="0" value="<?= $m ? (int)$m['nominal_diskon'] : 0 ?>">
                                    </div>
                                    <div class="col-md-7">
                                        <label class="small text-muted mb-1 d-block">Ket. Beasiswa (Contoh: KJP)</label>
                                        <input type="text" name="keterangan_diskon[<?= $t['id'] ?>]" class="form-control form-control-sm" placeholder="Contoh: Penerima KJP" value="<?= $m ? $m['keterangan_diskon'] : '' ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
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
