<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4">
            <h4 class="fw-bold mb-4">🆕 Tambah Tahun Ajaran</h4>

            <form action="<?= base_url('akademik/tahun-ajaran/save') ?>" method="post">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" class="form-control" placeholder="Contoh: 2027/2028" required>
                    <div class="form-text">Gunakan format YYYY/YYYY</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Semester</label>
                    <select name="semester" class="form-select">
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Tanggal Mulai</label>
                        <input type="date" name="tgl_mulai" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Tanggal Selesai</label>
                        <input type="date" name="tgl_selesai" class="form-control" required>
                    </div>
                </div>

                <hr class="my-4">
                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('akademik/tahun-ajaran') ?>" class="btn btn-light px-4">Batal</a>
                    <button type="submit" class="btn btn-primary px-5">Simpan Tahun Ajaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
