<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-0"><?= $title ?></h5>
            </div>
            <div class="card-body p-4 pt-0">
                <form action="<?= isset($kelas) ? base_url('akademik/kelas/update/' . $kelas['id']) : base_url('akademik/kelas/store') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tingkat</label>
                        <select name="tingkat" class="form-select rounded-3" required>
                            <?php for($i=1; $i<=12; $i++): ?>
                                <option value="<?= $i ?>" <?= (isset($kelas) && $kelas['tingkat'] == $i) ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control rounded-3" value="<?= $kelas['nama_kelas'] ?? '' ?>" placeholder="Contoh: 10 IPA 1" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Wali Kelas</label>
                        <select name="id_wali_kelas" class="form-select rounded-3">
                            <option value="">-- Pilih Wali Kelas --</option>
                            <?php foreach ($teachers as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= (isset($kelas) && $kelas['id_wali_kelas'] == $t['id']) ? 'selected' : '' ?>>
                                    <?= $t['nama_lengkap'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Data</button>
                        <a href="<?= base_url('akademik/kelas') ?>" class="btn btn-light rounded-pill px-4">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
