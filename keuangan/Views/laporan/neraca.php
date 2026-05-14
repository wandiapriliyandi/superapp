<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold">Laporan Neraca ⚖️</h3>
            <p class="text-muted">Posisi keuangan pesantren pada tanggal tertentu.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <form action="" method="get" class="d-inline-flex gap-2">
                <input type="date" name="tanggal" class="form-control" value="<?= $tanggal ?>">
                <button type="submit" class="btn btn-primary px-4">Tampilkan</button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-primary text-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">AKTIVA (Aset)</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <tbody>
                            <tr class="table-light"><td colspan="2" class="fw-bold">Aset Lancar</td></tr>
                            <tr><td class="ps-4">Kas Utama</td><td class="text-end">0.00</td></tr>
                            <tr><td class="ps-4">Bank BSI</td><td class="text-end">0.00</td></tr>
                            <tr class="table-light"><td colspan="2" class="fw-bold">Aset Tetap</td></tr>
                            <tr><td class="ps-4">Bangunan Pesantren</td><td class="text-end">0.00</td></tr>
                        </tbody>
                        <tfoot class="bg-light fw-bold border-top">
                            <tr><td class="ps-4">TOTAL AKTIVA</td><td class="text-end">0.00</td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-dark text-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">PASIVA (Kewajiban & Ekuitas)</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <tbody>
                            <tr class="table-light"><td colspan="2" class="fw-bold">Kewajiban</td></tr>
                            <tr><td class="ps-4">Utang Operasional</td><td class="text-end">0.00</td></tr>
                            <tr class="table-light"><td colspan="2" class="fw-bold">Ekuitas</td></tr>
                            <tr><td class="ps-4">Modal Yayasan</td><td class="text-end">0.00</td></tr>
                            <tr><td class="ps-4">Laba/Rugi Tahun Berjalan</td><td class="text-end text-success">0.00</td></tr>
                        </tbody>
                        <tfoot class="bg-light fw-bold border-top">
                            <tr><td class="ps-4">TOTAL PASIVA</td><td class="text-end">0.00</td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="alert alert-info border-0 rounded-4">
                <i class="fas fa-info-circle me-2"></i>
                Laporan Neraca ini akan otomatis terisi seiring dengan penginputan jurnal transaksi.
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
