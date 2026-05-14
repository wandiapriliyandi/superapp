<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-5">
        <div class="row align-items-center mb-5">
            <div class="col">
                <h4 class="fw-bold mb-1">RAPOR HASIL BELAJAR</h4>
                <p class="text-muted mb-0">Tahun Ajaran <?= $tahun_ajaran['tahun_ajaran'] ?> - <?= $tahun_ajaran['semester'] ?></p>
            </div>
            <div class="col-auto text-end">
                <button onclick="window.print()" class="btn btn-light rounded-pill px-4 me-2">
                    <i class="bi bi-printer me-2"></i> Cetak
                </button>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr><th width="150">Nama Santri</th><td>: <?= $santri['nama_lengkap'] ?></td></tr>
                    <tr><th>NIS</th><td>: <?= $santri['nis'] ?></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr><th width="150">Kelas</th><td>: (Ditentukan dari Jadwal)</td></tr>
                    <tr><th>Status</th><td>: <span class="badge bg-success bg-opacity-10 text-success rounded-pill"><?= $santri['status_santri'] ?></span></td></tr>
                </table>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="bg-light text-center">
                    <tr>
                        <th rowspan="2" class="align-middle">No</th>
                        <th rowspan="2" class="align-middle">Mata Pelajaran</th>
                        <th colspan="3">Nilai Pengetahuan</th>
                        <th rowspan="2" class="align-middle">Predikat</th>
                        <th rowspan="2" class="align-middle">Keterangan</th>
                    </tr>
                    <tr>
                        <th>Tugas</th>
                        <th>UTS</th>
                        <th>UAS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($nilai)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data nilai</td></tr>
                    <?php else: ?>
                        <?php $n=1; foreach ($nilai as $val): ?>
                        <tr>
                            <td class="text-center"><?= $n++ ?></td>
                            <td class="fw-bold"><?= $val['nama_mapel'] ?></td>
                            <td class="text-center"><?= $val['nilai_tugas'] ?></td>
                            <td class="text-center"><?= $val['nilai_uts'] ?></td>
                            <td class="text-center"><?= $val['nilai_uas'] ?></td>
                            <td class="text-center"><span class="fw-bold"><?= $val['predikat'] ?></span></td>
                            <td class="small"><?= $val['keterangan'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="row mt-5 text-center">
            <div class="col-md-4">
                <p class="mb-5">Orang Tua/Wali</p>
                <br><br>
                <p class="fw-bold text-decoration-underline">(..........................)</p>
            </div>
            <div class="col-md-4">
            </div>
            <div class="col-md-4">
                <p class="mb-0">Ditetapkan di: Bekasi</p>
                <p class="mb-5">Tanggal: <?= date('d M Y') ?></p>
                <br><br>
                <p class="fw-bold text-decoration-underline">Wali Kelas</p>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        .card, .card * { visibility: visible; }
        .card { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none!important; border: 0!important; }
        .btn, .sidebar, .header { display: none!important; }
    }
</style>
<?= $this->endSection() ?>
