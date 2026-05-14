<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h3 class="fw-bold">Laporan Laba Rugi 📈</h3>
            <p class="text-muted">Kinerja keuangan pesantren dalam periode <?= date('d M Y', strtotime($tgl_mulai)) ?> s/d <?= date('d M Y', strtotime($tgl_selesai)) ?>.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="" method="get" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Mulai Tanggal</label>
                    <input type="date" name="tgl_mulai" class="form-control" value="<?= $tgl_mulai ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Sampai Tanggal</label>
                    <input type="date" name="tgl_selesai" class="form-control" value="<?= $tgl_selesai ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100 py-2">Tampilkan Laporan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-5">
                        <h4 class="fw-bold mb-0">LAPORAN LABA RUGI</h4>
                        <p class="text-muted">Periode: <?= date('d/m/Y', strtotime($tgl_mulai)) ?> - <?= date('d/m/Y', strtotime($tgl_selesai)) ?></p>
                    </div>

                    <!-- Pendapatan -->
                    <h6 class="fw-bold text-success border-bottom pb-2 mb-3">PENDAPATAN</h6>
                    <table class="table table-borderless mb-4">
                        <tbody>
                            <tr><td>Pendapatan SPP</td><td class="text-end">0.00</td></tr>
                            <tr><td>Infaq & Sedekah</td><td class="text-end">0.00</td></tr>
                            <tr class="fw-bold border-top"><td>TOTAL PENDAPATAN</td><td class="text-end">0.00</td></tr>
                        </tbody>
                    </table>

                    <!-- Beban -->
                    <h6 class="fw-bold text-danger border-bottom pb-2 mb-3">BEBAN OPERASIONAL</h6>
                    <table class="table table-borderless mb-4">
                        <tbody>
                            <tr><td>Beban Gaji Karyawan</td><td class="text-end">(0.00)</td></tr>
                            <tr><td>Beban Listrik & Air</td><td class="text-end">(0.00)</td></tr>
                            <tr><td>Beban Konsumsi Santri</td><td class="text-end">(0.00)</td></tr>
                            <tr class="fw-bold border-top"><td>TOTAL BEBAN</td><td class="text-end">(0.00)</td></tr>
                        </tbody>
                    </table>

                    <div class="bg-light p-4 rounded-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">LABA / (RUGI) BERSIH</h5>
                        <h4 class="mb-0 fw-bold text-success">Rp 0.00</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
