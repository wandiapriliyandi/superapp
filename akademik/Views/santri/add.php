<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4 text-center">
                <h4 class="fw-bold mb-0">📝 Input Biodata Santri Baru</h4>
                <p class="text-muted">Pastikan data yang diinput sesuai dengan dokumen resmi.</p>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('akademik/santri/save') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <div class="row g-4 bg-body-tertiary p-4 rounded-4 shadow-sm border mb-4">
                        <div class="col-md-9">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold small">Nama Lengkap Santri</label>
                                    <input type="text" name="nama_lengkap" class="form-control border-0 shadow-sm" placeholder="Nama sesuai ijazah..." required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">NISN</label>
                                    <input type="text" name="nisn" class="form-control border-0 shadow-sm" placeholder="10 digit...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">NIK</label>
                                    <input type="text" name="nik" class="form-control border-0 shadow-sm" placeholder="16 digit...">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <label class="form-label fw-bold small d-block text-start">Pas Foto</label>
                            <div class="bg-white p-2 rounded shadow-sm border mb-2" style="height: 120px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-person-bounding-box text-muted fs-1"></i>
                            </div>
                            <input type="file" name="foto" class="form-control form-control-sm border-0 shadow-sm">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select border-0 shadow-sm">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control border-0 shadow-sm" placeholder="Kota/Kab...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control border-0 shadow-sm">
                        </div>
                    </div>

                    <div class="row g-4 bg-body-tertiary p-4 rounded-4 shadow-sm border mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control border-0 shadow-sm" rows="3" placeholder="Jalan, RT/RW, Desa, Kec, Kab, Prov..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">No. HP / WhatsApp (Wali)</label>
                            <input type="text" name="no_hp" class="form-control border-0 shadow-sm" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Status Santri</label>
                            <select name="status_santri" class="form-select border-0 shadow-sm">
                                <option value="Aktif">Aktif</option>
                                <option value="Non-Aktif">Non-Aktif</option>
                                <option value="Pindah">Pindah</option>
                                <option value="Lulus">Lulus</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Tahun Ajaran Masuk / Terdaftar</label>
                            <select name="id_tahun_ajaran" class="form-select border-0 shadow-sm">
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                <?php foreach($ta as $item): ?>
                                    <option value="<?= $item['id'] ?>" <?= $item['status'] == 'Aktif' ? 'selected' : '' ?>>
                                        <?= $item['tahun_ajaran'] ?> (<?= $item['semester'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-2">
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">💾 Simpan Data Santri</button>
                        <a href="<?= base_url('akademik/santri') ?>" class="btn btn-light px-4 border py-2 fw-semibold">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
