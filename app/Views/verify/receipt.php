<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center pt-4">
    <div class="col-md-6 col-sm-12">
        <div class="card border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="card-header bg-success p-4 text-white text-center border-0">
                <div class="mb-2">
                    <i class="bi bi-check-circle-fill" style="font-size: 3rem;"></i>
                </div>
                <h4 class="fw-bold mb-0">KWITANSI VALID</h4>
                <p class="mb-0 opacity-75">Data Terverifikasi Sistem</p>
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <div class="row mb-2">
                        <div class="col-5 text-muted">No. Transaksi</div>
                        <div class="col-7 fw-bold text-end"><?= $no_trx ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Nama Santri</div>
                        <div class="col-7 fw-bold text-end"><?= $santri['nama_lengkap'] ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Tanggal Bayar</div>
                        <div class="col-7 fw-bold text-end"><?= date('d F Y H:i', strtotime($details[0]['created_at'])) ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted">Metode</div>
                        <div class="col-7 fw-bold text-end"><?= $details[0]['metode_pembayaran'] ?></div>
                    </div>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Item Pembayaran</th>
                                <th class="text-end">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $total = 0; foreach($details as $d): $total += $d['nominal_bayar']; ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?= $d['nama_tarif'] ?></div>
                                    <small class="text-muted">Bulan: <?= $d['bulan'] ?>/<?= $d['tahun'] ?></small>
                                </td>
                                <td class="text-end align-middle">Rp <?= number_format($d['nominal_bayar'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td>TOTAL PEMBAYARAN</td>
                                <td class="text-end text-success fs-5">Rp <?= number_format($total, 0, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="alert alert-light text-center border-0" style="background: #f8f9fa; border-radius: 12px;">
                    <small class="text-muted">Kwitansi ini dihasilkan secara otomatis oleh sistem keuangan pesantren dan merupakan bukti pembayaran yang sah.</small>
                </div>
            </div>
            <div class="card-footer bg-white p-4 border-0 text-center">
                <p class="mb-0 text-muted small">&copy; <?= date('Y') ?> Pesantren Digital SuperApp</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
