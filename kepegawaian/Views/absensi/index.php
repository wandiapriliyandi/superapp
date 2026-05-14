<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
    <div class="card-body p-4">
        <form action="" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold text-muted small">Pilih Tanggal Presensi</label>
                <div class="input-group shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar3"></i></span>
                    <input type="date" name="tanggal" class="form-control border-start-0" value="<?= $tanggal_filter ?>" onchange="this.form.submit()">
                </div>
            </div>
            <div class="col-md-8 text-end">
                <h5 class="fw-bold mb-0 text-primary">Rekapitulasi Kehadiran Asatidz & Staff</h5>
                <p class="text-muted small mb-0">Tanggal: <?= date('d F Y', strtotime($tanggal_filter)) ?></p>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 20px;">
    <div class="card-body p-0">
        <form action="<?= base_url('kepegawaian/absensi/save') ?>" method="POST">
            <input type="hidden" name="tanggal" value="<?= $tanggal_filter ?>">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">NIK & Nama Lengkap</th>
                            <th class="text-center">Hadir</th>
                            <th class="text-center">Izin</th>
                            <th class="text-center">Sakit</th>
                            <th class="text-center">Alpha</th>
                            <th class="pe-4 text-center">Cuti</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Map existing attendance
                        $absensi_map = [];
                        foreach($absensi as $a) {
                            $absensi_map[$a['pegawai_id']] = $a['status'];
                        }
                        
                        foreach($pegawai as $p): 
                            $status_current = $absensi_map[$p['id']] ?? null;
                        ?>
                        <tr>
                            <td class="ps-4">
                                <input type="hidden" name="pegawai_id[]" value="<?= $p['id'] ?>">
                                <div class="fw-bold"><?= $p['nama_lengkap'] ?></div>
                                <div class="small text-muted"><?= $p['nik'] ?></div>
                            </td>
                            <td class="text-center">
                                <div class="form-check form-check-inline m-0">
                                    <input class="form-check-input" type="radio" name="status[<?= array_search($p, $pegawai) ?>]" value="Hadir" <?= ($status_current == 'Hadir' || $status_current == null) ? 'checked' : '' ?>>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-check form-check-inline m-0">
                                    <input class="form-check-input" type="radio" name="status[<?= array_search($p, $pegawai) ?>]" value="Izin" <?= ($status_current == 'Izin') ? 'checked' : '' ?>>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-check form-check-inline m-0">
                                    <input class="form-check-input" type="radio" name="status[<?= array_search($p, $pegawai) ?>]" value="Sakit" <?= ($status_current == 'Sakit') ? 'checked' : '' ?>>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="form-check form-check-inline m-0">
                                    <input class="form-check-input" type="radio" name="status[<?= array_search($p, $pegawai) ?>]" value="Alpha" <?= ($status_current == 'Alpha') ? 'checked' : '' ?>>
                                </div>
                            </td>
                            <td class="pe-4 text-center">
                                <div class="form-check form-check-inline m-0">
                                    <input class="form-check-input" type="radio" name="status[<?= array_search($p, $pegawai) ?>]" value="Cuti" <?= ($status_current == 'Cuti') ? 'checked' : '' ?>>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 p-4 text-end">
                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">
                    <i class="bi bi-cloud-check me-2"></i> Simpan Presensi Hari Ini
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
