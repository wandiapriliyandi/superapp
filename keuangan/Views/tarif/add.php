<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Tambah Tarif SPP Baru</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('keuangan/tarif/save') ?>" method="post">
                    <div class="mb-3">
                        <label class="form-label">Nama Tarif</label>
                        <input type="text" name="nama_tarif" class="form-control rounded-3" placeholder="Contoh: SPP Bulanan Reguler" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nominal (Rp)</label>
                        <input type="number" name="nominal" class="form-control rounded-3" placeholder="Contoh: 500000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control rounded-3" rows="3"></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('keuangan/tarif') ?>" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Tarif</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
