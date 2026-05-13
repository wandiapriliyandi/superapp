<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Input Pembayaran SPP</h5>
            </div>
            <div class="card-body">
                <div class="p-3 bg-light rounded-4 mb-4">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <small class="text-muted d-block">Nama Santri</small>
                            <span class="fw-bold"><?= $tagihan['nama_santri'] ?></span>
                        </div>
                        <div class="col-6 mb-2 text-end">
                            <small class="text-muted d-block">Jenis Tagihan</small>
                            <span class="fw-bold"><?= $tagihan['nama_tarif'] ?></span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Nominal Tagihan</small>
                            <span class="fw-bold">Rp <?= number_format($tagihan['nominal_tagihan'], 0, ',', '.') ?></span>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted d-block text-danger">Sisa Tagihan</small>
                            <span class="fw-bold text-danger">Rp <?= number_format($sisa, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>

                <form action="<?= base_url('keuangan/pembayaran/save') ?>" method="post">
                    <input type="hidden" name="tagihan_id" value="<?= $tagihan['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label">Nominal Bayar (Rp)</label>
                        <input type="number" name="nominal_bayar" class="form-control rounded-3" value="<?= $sisa ?>" max="<?= $sisa ?>" required>
                        <small class="text-muted">Masukkan jumlah yang dibayarkan saat ini.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Metode Pembayaran</label>
                        <select name="metode_pembayaran" class="form-select rounded-3">
                            <option value="Cash">Tunai / Cash</option>
                            <option value="Transfer">Transfer Bank</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Keterangan / Catatan</label>
                        <textarea name="keterangan" class="form-control rounded-3" rows="2" placeholder="Catatan opsional..."></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('keuangan/tagihan') ?>" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-success rounded-pill px-4">Konfirmasi Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
