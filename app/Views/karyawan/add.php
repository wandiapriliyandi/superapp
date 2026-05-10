<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">Tambah Pegawai Baru</h5>
                <p class="text-muted small">Silakan lengkapi data guru/karyawan di bawah ini.</p>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('kepegawaian/karyawan/save') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <div class="row g-4 bg-body-tertiary p-4 rounded-4 shadow-sm border mb-4">
                        <div class="col-md-12">
                            <h6 class="fw-bold text-primary mb-3">A. Informasi Personal</h6>
                        </div>
                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold small">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap" class="form-control border-0 shadow-sm" placeholder="Masukkan nama tanpa gelar..." required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold small">NIK (Nomor Induk Kependudukan)</label>
                                    <input type="text" name="nik" class="form-control border-0 shadow-sm" placeholder="16 digit NIK...">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <label class="form-label fw-semibold small d-block text-start">Foto Pegawai</label>
                            <div class="bg-white p-2 rounded shadow-sm border mb-2" style="height: 100px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-image text-muted fs-1"></i>
                            </div>
                            <input type="file" name="foto" class="form-control form-control-sm border-0 shadow-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select border-0 shadow-sm" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control border-0 shadow-sm" placeholder="Kota kelahiran...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control border-0 shadow-sm">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold small">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control border-0 shadow-sm" rows="3" placeholder="Alamat sesuai KTP..."></textarea>
                        </div>
                    </div>

                    <div class="row g-4 bg-body-tertiary p-4 rounded-4 shadow-sm border mb-4">
                        <div class="col-md-12">
                            <h6 class="fw-bold text-primary mb-3">B. Informasi Kepegawaian & Kontak</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">NIP (Jika ada)</label>
                            <input type="text" name="nip" class="form-control border-0 shadow-sm" placeholder="Nomor Induk Pegawai...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control border-0 shadow-sm" placeholder="Contoh: Guru Matematika, HRD, dll" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Status Pegawai</label>
                            <select name="status_pegawai" class="form-select border-0 shadow-sm" required>
                                <option value="Tetap">Tetap</option>
                                <option value="Kontrak">Kontrak</option>
                                <option value="Honorer">Honorer</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Pendidikan Terakhir</label>
                            <input type="text" name="pendidikan_terakhir" class="form-control border-0 shadow-sm" placeholder="Contoh: S1 Pendidikan Agama">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal Masuk Kerja</label>
                            <input type="date" name="tanggal_masuk" class="form-control border-0 shadow-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Nomor WhatsApp</label>
                            <input type="text" name="no_hp" class="form-control border-0 shadow-sm" placeholder="0812...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Email Aktif</label>
                            <input type="email" name="email" class="form-control border-0 shadow-sm" placeholder="nama@email.com">
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">💾 Simpan Data Pegawai</button>
                        <a href="<?= base_url('kepegawaian/karyawan') ?>" class="btn btn-light px-4 border py-2 fw-semibold">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
