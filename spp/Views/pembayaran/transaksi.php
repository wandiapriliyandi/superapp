<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0 text-primary">
                <i class="bi bi-receipt-cutoff me-2"></i> Riwayat Transaksi (Kwitansi)
            </h5>
            <small class="text-muted">Daftar semua pembayaran yang dikelompokkan per kwitansi</small>
        </div>
        <div class="dropdown">
            <button class="btn btn-outline-success btn-sm rounded-pill px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-download me-1"></i>Eksport
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                <li><a class="dropdown-item" href="<?= base_url('spp/pembayaran/transaksi/export/excel?' . http_build_query($filter)) ?>"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel (.xlsx)</a></li>
                <li><a class="dropdown-item" href="<?= base_url('spp/pembayaran/transaksi/export/word?' . http_build_query($filter)) ?>"><i class="bi bi-file-earmark-word me-2 text-primary"></i>Word (.doc)</a></li>
                <li><a class="dropdown-item" href="<?= base_url('spp/pembayaran/transaksi/export/pdf?' . http_build_query($filter)) ?>" target="_blank"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF / Cetak</a></li>
            </ul>
        </div>
    </div>
    <div class="card-body bg-light border-bottom py-2 px-3">
        <form action="<?= base_url('spp/pembayaran/transaksi') ?>" method="GET" class="row row-cols-auto gx-2 gy-0 align-items-center">
            <div class="col-auto">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3">Dari</span>
                    <input type="date" name="tgl_mulai" class="form-control border-start-0" value="<?= $filter['tgl_mulai'] ?>">
                </div>
            </div>
            <div class="col-auto">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3">Sampai</span>
                    <input type="date" name="tgl_selesai" class="form-control border-start-0 rounded-end-pill pe-3" value="<?= $filter['tgl_selesai'] ?>">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3">Filter</button>
                <a href="<?= base_url('spp/pembayaran/transaksi') ?>" class="btn btn-light btn-sm rounded-pill px-3 border text-dark">Reset</a>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="table-transaksi">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Nama Santri</th>
                        <th class="text-end">Total Bayar</th>
                        <th class="text-center">Metode</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($list as $row): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <code class="fw-bold text-dark"><?= $row['nomor_transaksi'] ?></code>
                            </td>
                            <td>
                                <div class="small fw-bold"><?= date('d M Y', strtotime($row['created_at'])) ?></div>
                                <small class="text-muted"><?= date('H:i', strtotime($row['created_at'])) ?> WIB</small>
                            </td>
                            <td>
                                <div class="fw-bold small text-uppercase"><?= $row['nama_lengkap'] ?></div>
                                <small class="text-muted">NISN: <?= $row['nisn'] ?></small>
                            </td>
                            <td class="text-end fw-bold text-primary">
                                Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border rounded-pill px-3"><?= $row['metode_pembayaran'] ?></span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    <button class="btn btn-info btn-sm rounded-pill px-3 shadow-sm text-white btn-detail"
                                            data-no-trx="<?= $row['nomor_transaksi'] ?>"
                                            data-bs-toggle="modal" data-bs-target="#modalDetail">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </button>
                                    <a href="<?= base_url('spp/pembayaran/kwitansi/' . $row['nomor_transaksi']) ?>" 
                                       class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                                        <i class="bi bi-printer me-1"></i> Cetak
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<!-- Modal Detail Transaksi -->
<?= $this->section('modals') ?>
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-receipt-cutoff me-2"></i> Detail Transaksi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="detail-body">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3 text-muted">Memuat data...</p>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <a href="#" id="btn-cetak-kwitansi" class="btn btn-primary rounded-pill px-4" target="_blank">
                    <i class="bi bi-printer me-1"></i> Cetak Kwitansi
                </a>
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    const months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const baseDetailUrl = '<?= base_url('spp/pembayaran/detail/') ?>';
    const baseKwitansiUrl = '<?= base_url('spp/pembayaran/kwitansi/') ?>';

    $(document).ready(function() {
        $('#table-transaksi').DataTable({
            "order": [[2, "desc"]],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    });

    // Handle Detail Modal — pakai event delegation pada document agar kompatibel dengan DataTable
    $(document).on('click', '.btn-detail', function(e) {
        e.preventDefault();
        const noTrx = $(this).attr('data-no-trx');
        const detailUrl = baseDetailUrl + noTrx;

        $('#btn-cetak-kwitansi').attr('href', baseKwitansiUrl + noTrx);
        $('#detail-body').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 text-muted">Memuat data...</p></div>');
        $('#modalDetail').modal('show');

        $.ajax({
            url: detailUrl,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (!res.success || !res.data || !res.data.length) {
                    var msg = res.message ? '<code class="d-block mt-2 text-danger small">' + res.message + '</code>' : '';
                    $('#detail-body').html('<div class="text-center py-4 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Data tidak ditemukan.' + msg + '</div>');
                    return;
                }
                const d = res.data;
                let total = 0;
                let rows = '';
                d.forEach(function(item, i) {
                    total += parseFloat(item.nominal_bayar);
                    rows += '<tr>' +
                        '<td>' + (i+1) + '</td>' +
                        '<td><b>' + item.nama_tarif + '</b><br><small class="text-muted">' + (months[item.bulan] || 'Tahunan') + ' ' + item.tahun + '</small></td>' +
                        '<td class="text-end fw-bold">Rp ' + parseInt(item.nominal_bayar).toLocaleString('id-ID') + '</td>' +
                        '</tr>';
                });

                $('#detail-body').html(
                    '<div class="d-flex justify-content-between align-items-start mb-4">' +
                        '<div>' +
                            '<h5 class="fw-bold mb-1">' + d[0].nama_lengkap + '</h5>' +
                            '<small class="text-muted">NISN: ' + (d[0].nisn || '-') + '</small>' +
                        '</div>' +
                        '<div class="text-end">' +
                            '<span class="badge bg-primary rounded-pill px-3">' + noTrx + '</span><br>' +
                            '<small class="text-muted">' + d[0].tanggal_bayar + '</small><br>' +
                            '<span class="badge bg-light text-dark border mt-1">' + d[0].metode_pembayaran + '</span>' +
                        '</div>' +
                    '</div>' +
                    '<div class="table-responsive">' +
                        '<table class="table table-bordered table-sm align-middle">' +
                            '<thead class="table-light"><tr><th>#</th><th>Item Tagihan</th><th class="text-end">Jumlah</th></tr></thead>' +
                            '<tbody>' + rows + '</tbody>' +
                            '<tfoot><tr class="table-primary fw-bold">' +
                                '<td colspan="2" class="text-end">TOTAL BAYAR</td>' +
                                '<td class="text-end">Rp ' + total.toLocaleString('id-ID') + '</td>' +
                            '</tr></tfoot>' +
                        '</table>' +
                    '</div>'
                );
            },
            error: function(xhr, status, error) {
                $('#detail-body').html(
                    '<div class="alert alert-danger">' +
                        '<b>Gagal memuat data.</b><br>' +
                        '<small>HTTP ' + xhr.status + ' — ' + error + '</small><br>' +
                        '<small class="text-muted">URL: ' + detailUrl + '</small>' +
                    '</div>'
                );
            }
        });
    });
</script>
<?= $this->endSection() ?>

