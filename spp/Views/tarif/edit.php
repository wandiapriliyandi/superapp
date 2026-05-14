<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Edit Tarif SPP</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('spp/tarif/update/' . $tarif['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Tahun Ajaran</label>
                        <select name="id_tahun_akademik" class="form-select rounded-3">
                            <option value="">-- Semua Tahun Ajaran --</option>
                            <?php foreach($ta as $item): ?>
                                <option value="<?= $item['id'] ?>" <?= $item['id'] == $tarif['id_tahun_akademik'] ? 'selected' : '' ?>>
                                    <?= $item['nama_tahun'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Tarif</label>
                        <input type="text" name="nama_tarif" class="form-control rounded-3" value="<?= $tarif['nama_tarif'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe Tarif</label>
                        <select name="tipe" class="form-select rounded-3" required>
                            <option value="Bulanan" <?= $tarif['tipe'] == 'Bulanan' ? 'selected' : '' ?>>Bulanan</option>
                            <option value="Tahunan" <?= $tarif['tipe'] == 'Tahunan' ? 'selected' : '' ?>>Tahunan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nominal (Rp)</label>
                        <input type="number" name="nominal" class="form-control rounded-3" value="<?= (int)$tarif['nominal'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control rounded-3" rows="3"><?= $tarif['keterangan'] ?></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('spp/tarif') ?>" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
