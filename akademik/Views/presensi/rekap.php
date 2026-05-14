<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3">Filter Rekap Bulanan</h5>
        <form action="<?= base_url('akademik/presensi/rekap') ?>" method="get" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Pilih Kelas</label>
                <select name="kelas" class="form-select rounded-pill" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($kelas as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= $selected_kelas == $k['id'] ? 'selected' : '' ?>><?= $k['nama_kelas'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Bulan</label>
                <select name="bulan" class="form-select rounded-pill">
                    <?php 
                    $months = [
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', 
                        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', 
                        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                    ];
                    foreach ($months as $m => $name): ?>
                        <option value="<?= $m ?>" <?= $selected_bulan == $m ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Tahun</label>
                <select name="tahun" class="form-select rounded-pill">
                    <?php for($y = date('Y'); $y >= 2024; $y--): ?>
                        <option value="<?= $y ?>" <?= $selected_tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary rounded-pill w-100">Tampilkan Rekap</button>
            </div>
        </form>
    </div>
</div>

<?php if ($selected_kelas): ?>
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">Statistik Kehadiran: <?= $months[$selected_bulan] ?> <?= $selected_tahun ?></h6>
        <button onclick="window.print()" class="btn btn-light-primary rounded-pill px-4">
            <i class="bi bi-printer me-2"></i> Cetak Rekap
        </button>
    </div>
    <div class="card-body p-4 pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 rounded-start">Santri</th>
                        <th class="border-0 text-center text-success">Hadir</th>
                        <th class="border-0 text-center text-info">Izin</th>
                        <th class="border-0 text-center text-warning">Sakit</th>
                        <th class="border-0 text-center text-danger">Alpa</th>
                        <th class="border-0 text-center rounded-end">Total Pertemuan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($santri)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">Data tidak ditemukan</td></tr>
                    <?php else: ?>
                        <?php foreach ($santri as $s): 
                            $st = $rekap[$s['id']];
                            $total = array_sum($st);
                        ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><?= $s['nama_lengkap'] ?></div>
                                <small class="text-muted"><?= $s['nis'] ?></small>
                            </td>
                            <td class="text-center"><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3"><?= $st['Hadir'] ?></span></td>
                            <td class="text-center"><span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3"><?= $st['Izin'] ?></span></td>
                            <td class="text-center"><span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3"><?= $st['Sakit'] ?></span></td>
                            <td class="text-center"><span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3"><?= $st['Alpa'] ?></span></td>
                            <td class="text-center fw-bold"><?= $total ?></td>
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
