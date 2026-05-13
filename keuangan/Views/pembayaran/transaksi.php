<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 border-0">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-receipt-cutoff me-2"></i> Riwayat Transaksi (Kwitansi)
                </h5>
                <small class="text-muted">Daftar semua pembayaran yang dikelompokkan per kwitansi</small>
            </div>
        </div>
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
                    <?php if (empty($list)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Belum ada transaksi pembayaran.</td>
                        </tr>
                    <?php else: ?>
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
                                    <a href="<?= base_url('keuangan/pembayaran/kwitansi/' . $row['nomor_transaksi']) ?>" 
                                       class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                                        <i class="bi bi-printer me-1"></i> Cetak Kwitansi
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#table-transaksi').DataTable({
            "order": [[2, "desc"]],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    });
</script>
<?= $this->endSection() ?>
