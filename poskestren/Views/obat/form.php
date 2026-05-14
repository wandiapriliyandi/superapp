<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 p-4">
                <div class="d-flex align-items-center">
                    <a href="<?= base_url('poskestren/obat') ?>" class="btn btn-light rounded-circle me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h5 class="fw-bold mb-0"><?= isset($obat) ? 'Edit Data Obat' : 'Tambah Obat Baru' ?></h5>
                </div>
            </div>
            <div class="card-body p-4 pt-0">
                <form action="<?= isset($obat) ? base_url('poskestren/obat/update/' . $obat['id']) : base_url('poskestren/obat/simpan') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">NAMA OBAT</label>
                        <input type="text" name="nama_obat" class="form-control border-0 bg-light rounded-3 py-2" value="<?= isset($obat) ? $obat['nama_obat'] : '' ?>" placeholder="Mis: Paracetamol" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">SATUAN</label>
                            <select name="satuan" class="form-select border-0 bg-light rounded-3 py-2" required>
                                <option value="Tablet" <?= isset($obat) && $obat['satuan'] == 'Tablet' ? 'selected' : '' ?>>Tablet</option>
                                <option value="Botol" <?= isset($obat) && $obat['satuan'] == 'Botol' ? 'selected' : '' ?>>Botol (Sirup)</option>
                                <option value="Pcs" <?= isset($obat) && $obat['satuan'] == 'Pcs' ? 'selected' : '' ?>>Pcs (Salep/Lainnya)</option>
                                <option value="Strip" <?= isset($obat) && $obat['satuan'] == 'Strip' ? 'selected' : '' ?>>Strip</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">STOK AWAL</label>
                            <input type="number" name="stok" class="form-control border-0 bg-light rounded-3 py-2" value="<?= isset($obat) ? $obat['stok'] : '0' ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">DESKRIPSI (OPSIONAL)</label>
                        <textarea name="deskripsi" class="form-control border-0 bg-light rounded-3" rows="3" placeholder="Keterangan obat..."><?= isset($obat) ? $obat['deskripsi'] : '' ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 w-100 fw-bold shadow-sm">
                        <?= isset($obat) ? 'Update Data Obat' : 'Simpan Obat' ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
