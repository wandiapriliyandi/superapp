<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-1">Manajemen Payroll Asatidz & Staff</h5>
                        <p class="text-muted small mb-0">Kelola penggajian bulanan secara terpusat</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <form action="<?= base_url('kepegawaian/payroll/generate') ?>" method="POST" class="d-inline">
                            <input type="hidden" name="bulan" value="<?= $bulan_filter ?>">
                            <input type="hidden" name="tahun" value="<?= $tahun_filter ?>">
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="bi bi-gear-fill me-2"></i> Generate Payroll
                            </button>
                        </form>
                    </div>
                </div>
                <hr class="my-4">
                <form action="" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Pilih Bulan</label>
                        <select name="bulan" class="form-select" onchange="this.form.submit()">
                            <?php 
                            $bulan_nama = [
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                            ];
                            foreach($bulan_nama as $k => $v): ?>
                            <option value="<?= $k ?>" <?= ($bulan_filter == $k) ? 'selected' : '' ?>><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Pilih Tahun</label>
                        <select name="tahun" class="form-select" onchange="this.form.submit()">
                            <?php for($i = date('Y'); $i >= 2024; $i--): ?>
                            <option value="<?= $i ?>" <?= ($tahun_filter == $i) ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">SDM</th>
                                <th>Jabatan</th>
                                <th class="text-end">Gaji Pokok</th>
                                <th class="text-end">Tunjangan</th>
                                <th class="text-end text-primary">Gaji Bersih</th>
                                <th>Status</th>
                                <th class="pe-4 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($payroll as $p): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?= $p['nama_lengkap'] ?></div>
                                    <div class="small text-muted"><?= $p['nik'] ?></div>
                                </td>
                                <td><?= $p['nama_jabatan'] ?></td>
                                <td class="text-end">Rp <?= number_format($p['gaji_pokok'], 0, ',', '.') ?></td>
                                <td class="text-end">Rp <?= number_format($p['total_tunjangan'], 0, ',', '.') ?></td>
                                <td class="text-end fw-bold text-primary">Rp <?= number_format($p['gaji_bersih'], 0, ',', '.') ?></td>
                                <td>
                                    <?php if($p['status_bayar'] == 'Sudah Dibayar'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Paid</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Unpaid</span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="<?= base_url('kepegawaian/payroll/slip/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="bi bi-printer me-1"></i> Slip
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($payroll)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <p class="text-muted mb-0">Data payroll belum digenerate untuk periode ini.</p>
                                </td>
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
