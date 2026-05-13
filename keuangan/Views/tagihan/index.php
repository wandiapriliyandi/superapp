<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Data Tagihan SPP</h5>
                <a href="<?= base_url('keuangan/tagihan/generate') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-magic me-1"></i>Generate Masal
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Santri</th>
                                <th>Periode</th>
                                <th>Jenis</th>
                                <th>Nominal</th>
                                <th>Terbayar</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $months = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                            foreach ($tagihan as $t): 
                                $badgeClass = $t['status'] == 'Lunas' ? 'bg-success' : ($t['status'] == 'Cicilan' ? 'bg-warning text-dark' : 'bg-danger');
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?= $t['nama_santri'] ?></div>
                                    <small class="text-muted">ID: <?= $t['santri_id'] ?></small>
                                </td>
                                <td><?= $months[$t['bulan']] ?> <?= $t['tahun'] ?></td>
                                <td><?= $t['nama_tarif'] ?></td>
                                <td>Rp <?= number_format($t['nominal_tagihan'], 0, ',', '.') ?></td>
                                <td class="text-success fw-bold">Rp <?= number_format($t['total_terbayar'], 0, ',', '.') ?></td>
                                <td><span class="badge rounded-pill <?= $badgeClass ?>"><?= $t['status'] ?></span></td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <a href="<?= base_url('keuangan/pembayaran/bayar/' . $t['id']) ?>" class="btn btn-sm btn-primary rounded-pill px-3 me-2">Bayar</a>
                                        <a href="<?= base_url('keuangan/tagihan/delete/' . $t['id']) ?>" class="btn btn-sm btn-outline-danger rounded-circle" onclick="return confirm('Hapus tagihan ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($tagihan)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada data tagihan.</td>
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
