<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-0"><?= $title ?></h5>
            </div>
            <div class="card-body p-4 pt-0">
                <form action="<?= isset($mapel) ? base_url('akademik/mapel/update/' . $mapel['id']) : base_url('akademik/mapel/store') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Mapel</label>
                        <input type="text" name="kode_mapel" class="form-control rounded-3" value="<?= $mapel['kode_mapel'] ?? '' ?>" placeholder="Contoh: MP-001" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Mata Pelajaran</label>
                        <input type="text" name="nama_mapel" class="form-control rounded-3" value="<?= $mapel['nama_mapel'] ?? '' ?>" placeholder="Contoh: Matematika" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Kelompok</label>
                        <select name="kelompok" class="form-select rounded-3" required>
                            <option value="Nasional" <?= (isset($mapel) && $mapel['kelompok'] == 'Nasional') ? 'selected' : '' ?>>Nasional</option>
                            <option value="Muatan Lokal" <?= (isset($mapel) && $mapel['kelompok'] == 'Muatan Lokal') ? 'selected' : '' ?>>Muatan Lokal</option>
                            <option value="Pesantren" <?= (isset($mapel) && $mapel['kelompok'] == 'Pesantren') ? 'selected' : '' ?>>Pesantren</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Data</button>
                        <a href="<?= base_url('akademik/mapel') ?>" class="btn btn-light rounded-pill px-4">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
