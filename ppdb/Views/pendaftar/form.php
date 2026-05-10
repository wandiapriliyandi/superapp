<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">Form Input Calon Santri Manual</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('ppdb/pendaftar/save') ?>" method="POST">
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control shadow-sm" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select shadow-sm" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">No. HP Orang Tua</label>
                            <input type="text" name="no_hp_ortu" class="form-control shadow-sm" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control shadow-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control shadow-sm" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control border-0 shadow-sm" rows="3" placeholder="Alamat lengkap calon santri..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-primary">Sponsor / Rekomendasi</label>
                        <input type="text" name="sponsor" class="form-control border-0 shadow-sm" placeholder="Siapa yang membawa/merekomendasikan?">
                        <div class="form-text small">Kosongkan jika tidak ada.</div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status Awal Seleksi</label>
                            <select name="status_seleksi" class="form-select shadow-sm">
                                <option value="Pending">Pending</option>
                                <option value="Lulus">Lulus</option>
                                <option value="Tidak Lulus">Tidak Lulus</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tahun Ajaran</label>
                            <select name="id_tahun_ajaran" class="form-select shadow-sm" required>
                                <?php foreach($tahun_ajaran as $ta): ?>
                                <option value="<?= $ta['id'] ?>" <?= $ta['status'] == 'Aktif' ? 'selected' : '' ?>><?= $ta['tahun_ajaran'] ?> (<?= $ta['semester'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">💾 Simpan Data Pendaftar</button>
                        <a href="<?= base_url('ppdb/pendaftar') ?>" class="btn btn-light px-4 border">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
