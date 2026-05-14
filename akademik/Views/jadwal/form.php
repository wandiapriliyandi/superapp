<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-0">Tambah Jadwal Pelajaran</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <form action="<?= base_url('akademik/jadwal/store') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tahun Ajaran Aktif</label>
                            <select name="id_tahun_ajaran" class="form-select rounded-3" required>
                                <?php foreach ($tahun_ajaran as $ta): ?>
                                    <option value="<?= $ta['id'] ?>"><?= $ta['tahun_ajaran'] ?> - <?= $ta['semester'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kelas</label>
                            <select name="id_kelas" class="form-select rounded-3" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($kelas as $k): ?>
                                    <option value="<?= $k['id'] ?>"><?= $k['nama_kelas'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mata Pelajaran</label>
                            <select name="id_mapel" class="form-select rounded-3" required>
                                <option value="">-- Pilih Mapel --</option>
                                <?php foreach ($mapel as $m): ?>
                                    <option value="<?= $m['id'] ?>"><?= $m['nama_mapel'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Guru Pengampu</label>
                            <select name="id_guru" class="form-select rounded-3" required>
                                <option value="">-- Pilih Guru --</option>
                                <?php foreach ($teachers as $t): ?>
                                    <option value="<?= $t['id'] ?>"><?= $t['nama_lengkap'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Hari</label>
                            <select name="hari" class="form-select rounded-3" required>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                                <option value="Minggu">Minggu</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Ruangan</label>
                            <input type="text" name="ruangan" class="form-control rounded-3" placeholder="Contoh: Lab Komputer / Kelas 10A">
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Jadwal</button>
                        <a href="<?= base_url('akademik/jadwal') ?>" class="btn btn-light rounded-pill px-4">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
