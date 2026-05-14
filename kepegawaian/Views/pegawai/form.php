<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-1">Formulir SDM Baru</h5>
                <p class="text-muted small mb-0">Lengkapi data profil Asatidz / Staff dengan teliti</p>
            </div>
            <div class="card-body p-4 pt-0">
                <form action="<?= base_url('kepegawaian/pegawai/save') ?>" method="POST" enctype="multipart/form-data">
                    <div class="row g-4">
                        <!-- Informasi Pribadi -->
                        <div class="col-12">
                            <div class="sidebar-heading px-0 mb-3 text-primary fw-bold">1. INFORMASI PRIBADI</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">NIK / No. Induk</label>
                            <input type="text" name="nik" class="form-control" placeholder="Contoh: 12345678" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Lengkap (Sesuai KTP)</label>
                            <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama Lengkap beserta Gelar jika ada" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" placeholder="Kota/Kabupaten">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="L">Laki-laki (Ikhwan)</option>
                                <option value="P">Perempuan (Akhwat)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-whatsapp"></i></span>
                                <input type="text" name="no_hp" class="form-control border-start-0" placeholder="08xxxxxxxx">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Alamat Email</label>
                            <input type="email" name="email" class="form-control" placeholder="example@email.com">
                        </div>

                        <!-- Informasi Kepegawaian -->
                        <div class="col-12 mt-5">
                            <div class="sidebar-heading px-0 mb-3 text-primary fw-bold">2. INFORMASI KEPEGAWAIAN</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Departemen / Unit Kerja</label>
                            <select name="departemen_id" class="form-select" required>
                                <option value="">Pilih Departemen</option>
                                <?php foreach($departemen as $d): ?>
                                <option value="<?= $d['id'] ?>"><?= $d['nama_departemen'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jabatan / Amanah</label>
                            <select name="jabatan_id" class="form-select" required>
                                <option value="">Pilih Jabatan</option>
                                <?php foreach($jabatan as $j): ?>
                                <option value="<?= $j['id'] ?>"><?= $j['nama_jabatan'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status Pegawai</label>
                            <select name="status_pegawai" class="form-select">
                                <option value="Probation">Probation / Percobaan</option>
                                <option value="Tetap">Tetap</option>
                                <option value="Kontrak">Kontrak</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tanggal Masuk (TMT)</label>
                            <input type="date" name="tanggal_masuk" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Foto Profil</label>
                            <input type="file" name="foto" class="form-control">
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <hr class="my-4">
                            <a href="<?= base_url('kepegawaian/pegawai') ?>" class="btn btn-light rounded-pill px-4 me-2">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5">Simpan Data SDM</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
