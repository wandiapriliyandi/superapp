<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1"><?= $jadwal['nama_mapel'] ?></h5>
                <p class="text-muted mb-0"><?= $jadwal['nama_kelas'] ?> | <?= $jadwal['hari'] ?>, <?= date('d M Y') ?></p>
            </div>
            <div class="text-end">
                <span class="badge bg-primary px-3 py-2 rounded-pill"><?= date('H:i', strtotime($jadwal['jam_mulai'])) ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])) ?></span>
            </div>
        </div>
    </div>
</div>

<form action="<?= base_url('akademik/presensi/store') ?>" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="id_jadwal" value="<?= $jadwal['id'] ?>">
    <input type="hidden" name="tanggal" value="<?= $tanggal ?>">

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 rounded-start">Santri</th>
                            <th class="border-0 text-center" style="width: 150px;">Hadir</th>
                            <th class="border-0 text-center" style="width: 150px;">Izin</th>
                            <th class="border-0 text-center" style="width: 150px;">Sakit</th>
                            <th class="border-0 text-center" style="width: 150px;">Alpa</th>
                            <th class="border-0 rounded-end">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($santri as $s): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><?= $s['nama_lengkap'] ?></div>
                                <small class="text-muted"><?= $s['nis'] ?></small>
                            </td>
                            <td class="text-center">
                                <input class="form-check-input" type="radio" name="presensi[<?= $s['id'] ?>]" value="Hadir" checked>
                            </td>
                            <td class="text-center">
                                <input class="form-check-input" type="radio" name="presensi[<?= $s['id'] ?>]" value="Izin">
                            </td>
                            <td class="text-center">
                                <input class="form-check-input" type="radio" name="presensi[<?= $s['id'] ?>]" value="Sakit">
                            </td>
                            <td class="text-center">
                                <input class="form-check-input" type="radio" name="presensi[<?= $s['id'] ?>]" value="Alpa">
                            </td>
                            <td>
                                <input type="text" name="catatan[<?= $s['id'] ?>]" class="form-control form-control-sm rounded-pill" placeholder="Opsional">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-5 py-2">Simpan Presensi</button>
                <a href="<?= base_url('akademik/presensi') ?>" class="btn btn-light rounded-pill px-4 py-2">Batal</a>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>
