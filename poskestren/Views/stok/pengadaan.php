<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 p-4">
                <div class="d-flex align-items-center">
                    <a href="<?= base_url('poskestren/obat') ?>" class="btn btn-light rounded-circle me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h5 class="fw-bold mb-0">Pengadaan Obat</h5>
                        <p class="text-muted small mb-0">Stok masuk — beli / terima dari apotek atau gudang</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-4 pt-0">
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger rounded-4"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <form action="<?= base_url('poskestren/stok/pengadaan/simpan') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">OBAT</label>
                        <select name="obat_id" class="form-select border-0 bg-light rounded-3 py-2" required>
                            <option value="">Pilih obat...</option>
                            <?php foreach ($obat as $o) : ?>
                                <option value="<?= $o['id'] ?>" <?= old('obat_id') == $o['id'] ? 'selected' : '' ?>>
                                    <?= esc($o['nama_obat']) ?> — stok sekarang: <?= (int) $o['stok'] ?> <?= esc($o['satuan']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">JUMLAH MASUK</label>
                        <input type="number" name="jumlah" min="1" class="form-control border-0 bg-light rounded-3 py-2" value="<?= old('jumlah', 1) ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">KETERANGAN</label>
                        <textarea name="keterangan" class="form-control border-0 bg-light rounded-3" rows="2" placeholder="Mis: Pembelian Apotek X, tanggal 04/06/2026"><?= old('keterangan') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-success rounded-pill px-5 py-3 w-100 fw-bold">
                        <i class="bi bi-box-arrow-in-down me-2"></i> Catat Pengadaan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
