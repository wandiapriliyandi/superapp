<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3">Pilih Sesi Pelajaran</h5>
        <form action="<?= base_url('akademik/presensi') ?>" method="get" class="row g-3">
            <div class="col-md-4">
                <select name="kelas" class="form-select rounded-pill" onchange="this.form.submit()">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($kelas as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= $selected_kelas == $k['id'] ? 'selected' : '' ?>><?= $k['nama_kelas'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-8 text-end">
                <a href="<?= base_url('akademik/presensi/rekap') ?>" class="btn btn-outline-primary rounded-pill px-4">
                    <i class="bi bi-file-earmark-spreadsheet me-2"></i> Rekap Bulanan
                </a>
            </div>
        </form>

    </div>
</div>

<?php if ($selected_kelas): ?>
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-4">Jadwal Hari Ini (<?= $hari_ini ?>)</h6>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 rounded-start">Waktu</th>
                        <th class="border-0">Mata Pelajaran</th>
                        <th class="border-0">Guru</th>
                        <th class="border-0 text-end rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($jadwal)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Tidak ada jadwal untuk kelas ini</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($jadwal as $j): ?>
                        <tr>
                            <td><?= date('H:i', strtotime($j['jam_mulai'])) ?> - <?= date('H:i', strtotime($j['jam_selesai'])) ?></td>
                            <td class="fw-bold"><?= $j['nama_mapel'] ?></td>
                            <td><?= $j['nama_guru'] ?></td>
                            <td class="text-end">
                                <a href="<?= base_url('akademik/presensi/input/' . $j['id']) ?>" class="btn btn-primary rounded-pill px-4">
                                    <i class="bi bi-pencil-square me-1"></i> Input Absensi
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>
