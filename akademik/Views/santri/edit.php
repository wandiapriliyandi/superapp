<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4 text-center">
                <h4 class="fw-bold mb-0">✏️ Edit Biodata Santri</h4>
                <p class="text-muted">Perbarui data profil santri secara akurat.</p>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('akademik/santri/save') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $santri['id'] ?>">
                    
                    <div class="row g-4 bg-body-tertiary p-4 rounded-4 shadow-sm border mb-4">
                        <div class="col-md-9">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold small">Nama Lengkap Santri</label>
                                    <input type="text" name="nama_lengkap" class="form-control border-0 shadow-sm" value="<?= esc($santri['nama_lengkap']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">NISN</label>
                                    <input type="text" name="nisn" class="form-control border-0 shadow-sm" value="<?= esc($santri['nisn']) ?>" placeholder="10 digit...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">NIK</label>
                                    <input type="text" name="nik" class="form-control border-0 shadow-sm" value="<?= esc($santri['nik'] ?? '') ?>" placeholder="16 digit...">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <label class="form-label fw-bold small d-block text-start">Pas Foto (Opsional)</label>
                            <div class="bg-white p-2 rounded shadow-sm border mb-2 overflow-hidden" style="height: 120px; display: flex; align-items: center; justify-content: center;">
                                <?php if($santri['foto']): ?>
                                    <img src="<?= base_url('uploads/santri/'.$santri['foto']) ?>" class="w-100 h-100" style="object-fit: cover;">
                                <?php else: ?>
                                    <i class="bi bi-person-bounding-box text-muted fs-1"></i>
                                <?php endif; ?>
                            </div>
                            <input type="file" name="foto" class="form-control form-control-sm border-0 shadow-sm" title="Pilih foto baru untuk mengganti">
                            <div class="form-text" style="font-size: 10px;">Biarkan kosong jika tidak diganti</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select border-0 shadow-sm">
                                <option value="L" <?= $santri['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="P" <?= $santri['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control border-0 shadow-sm" value="<?= esc($santri['tempat_lahir']) ?>" placeholder="Kota/Kab...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control border-0 shadow-sm" value="<?= esc($santri['tanggal_lahir']) ?>">
                        </div>
                    </div>

                    <div class="row g-4 bg-body-tertiary p-4 rounded-4 shadow-sm border mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control border-0 shadow-sm" rows="3" placeholder="Jalan, RT/RW, Desa, Kec, Kab, Prov..."><?= esc($santri['alamat']) ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">No. HP / Kontak</label>
                            <input type="text" name="no_hp" class="form-control border-0 shadow-sm" value="<?= esc($santri['no_hp']) ?>" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Status Santri</label>
                            <select name="status_santri" class="form-select border-0 shadow-sm">
                                <option value="Aktif" <?= $santri['status_santri'] == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="Non-Aktif" <?= $santri['status_santri'] == 'Non-Aktif' ? 'selected' : '' ?>>Non-Aktif</option>
                                <option value="Pindah" <?= $santri['status_santri'] == 'Pindah' ? 'selected' : '' ?>>Pindah</option>
                                <option value="Lulus" <?= $santri['status_santri'] == 'Lulus' ? 'selected' : '' ?>>Lulus</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Tahun Ajaran Terdaftar</label>
                            <select name="id_tahun_ajaran" class="form-select border-0 shadow-sm">
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                <?php foreach($ta as $item): ?>
                                    <option value="<?= $item['id'] ?>" <?= $item['id'] == $santri['id_tahun_ajaran'] ? 'selected' : '' ?>>
                                        <?= $item['tahun_ajaran'] ?> (<?= $item['semester'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-2">
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">💾 Update Data Santri</button>
                        <a href="<?= base_url('akademik/santri') ?>" class="btn btn-light px-4 border py-2 fw-semibold">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
