<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Edit Nominal Tagihan</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning rounded-4 border-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Mengubah nominal akan mempengaruhi sisa tagihan santri ini.
                </div>
                
                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold">Santri</label>
                    <p class="fs-5 fw-bold mb-0"><?= $tagihan['nama_santri'] ?></p>
                    <p class="text-muted small">Periode: <?= $tagihan['bulan'] == 0 ? 'Tahunan' : $tagihan['bulan'] ?> / <?= $tagihan['tahun'] ?> (<?= $tagihan['nama_tarif'] ?>)</p>
                </div>

                <form action="<?= base_url('keuangan/tagihan/update/' . $tagihan['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nominal Tagihan (Rp)</label>
                        <input type="number" name="nominal_tagihan" class="form-control form-control-lg rounded-3 border-0 bg-light" value="<?= (int)$tagihan['nominal_tagihan'] ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="text-muted small text-uppercase fw-bold d-block mb-2">Info Pembayaran Saat Ini</label>
                        <div class="d-flex justify-content-between p-3 bg-light rounded-3">
                            <span>Sudah Terbayar:</span>
                            <span class="fw-bold text-success">Rp <?= number_format($tagihan['total_terbayar'], 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between gap-2">
                        <a href="<?= base_url('keuangan/tagihan') ?>" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
