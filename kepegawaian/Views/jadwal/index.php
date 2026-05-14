<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-0">Tambah Jadwal Baru</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <form action="<?= base_url('kepegawaian/jadwal/save') ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih Asatidz / Staff</label>
                        <select name="pegawai_id" class="form-select" required>
                            <option value="">-- Pilih SDM --</option>
                            <?php foreach($pegawai as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= $p['nama_lengkap'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Hari</label>
                        <select name="hari" class="form-select" required>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                            <option value="Minggu">Minggu</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Kegiatan / Shift</label>
                        <input type="text" name="kegiatan" class="form-control" placeholder="Contoh: Mengajar Kitab Kuning" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Musholla, Kelas A">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-pill py-2">Simpan Jadwal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-0">Agenda & Jadwal Mingguan</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Hari</th>
                                <th>Waktu</th>
                                <th>SDM</th>
                                <th>Kegiatan</th>
                                <th class="pe-4 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($jadwal as $j): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill"><?= $j['hari'] ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold"><?= date('H:i', strtotime($j['jam_mulai'])) ?> - <?= date('H:i', strtotime($j['jam_selesai'])) ?></div>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?= $j['nama_lengkap'] ?></div>
                                    <div class="small text-muted"><?= $j['nik'] ?></div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= $j['kegiatan'] ?></div>
                                    <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i><?= $j['lokasi'] ?: '-' ?></div>
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="javascript:void(0)" class="btn btn-sm btn-light rounded-circle text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-href="<?= base_url('kepegawaian/jadwal/delete/' . $j['id']) ?>">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($jadwal)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Belum ada agenda penjadwalan.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
