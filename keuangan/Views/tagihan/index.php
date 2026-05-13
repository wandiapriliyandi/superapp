<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<form action="<?= base_url('keuangan/pembayaran/bayar-massal') ?>" method="POST" id="form-massal">
    <?= csrf_field() ?>
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Data Tagihan SPP</h5>
                    <div class="d-flex gap-2">
                        <button type="submit" id="btn-bayar-massal" class="btn btn-primary btn-sm rounded-pill px-3 d-none">
                            <i class="bi bi-wallet2 me-1"></i>Bayar Terpilih (<span id="count-checked">0</span>)
                        </button>
                        <a href="<?= base_url('keuangan/tagihan/generate') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                            <i class="bi bi-magic me-1"></i>Generate Masal
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4" width="40">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="check-all">
                                        </div>
                                    </th>
                                    <th>Santri</th>
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
                                        <?php if($t['status'] != 'Lunas'): ?>
                                        <div class="form-check">
                                            <input class="form-check-input check-item" type="checkbox" name="tagihan_ids[]" value="<?= $t['id'] ?>">
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= $t['nama_santri'] ?></div>
                                        <small class="text-muted">ID: <?= $t['santri_id'] ?></small>
                                    </td>
                                    <td><?= $t['bulan'] == 0 ? '<span class="badge bg-secondary">Tahunan</span>' : $months[$t['bulan']] ?> <?= $t['tahun'] ?></td>
                                    <td><?= $t['nama_tarif'] ?></td>
                                    <td>Rp <?= number_format($t['nominal_tagihan'], 0, ',', '.') ?></td>
                                    <td class="text-success fw-bold">Rp <?= number_format($t['total_terbayar'], 0, ',', '.') ?></td>
                                    <td><span class="badge rounded-pill <?= $badgeClass ?>"><?= $t['status'] ?></span></td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <a href="<?= base_url('keuangan/pembayaran/bayar/' . $t['id']) ?>" class="btn btn-sm btn-primary rounded-pill px-3 me-1">Bayar</a>
                                            <a href="<?= base_url('keuangan/tagihan/edit/' . $t['id']) ?>" class="btn btn-sm btn-outline-info rounded-circle me-1" title="Edit Nominal">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?= base_url('keuangan/tagihan/delete/' . $t['id']) ?>" class="btn btn-sm btn-outline-danger rounded-circle" onclick="return confirm('Hapus tagihan ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($tagihan)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Belum ada data tagihan.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        const checkAll = $('#check-all');
        const checkItems = $('.check-item');
        const btnBayar = $('#btn-bayar-massal');
        const countSpan = $('#count-checked');

        function updateButton() {
            const checkedCount = $('.check-item:checked').length;
            countSpan.text(checkedCount);
            if (checkedCount > 0) {
                btnBayar.removeClass('d-none');
            } else {
                btnBayar.addClass('d-none');
            }
        }

        checkAll.on('change', function() {
            checkItems.prop('checked', $(this).prop('checked'));
            updateButton();
        });

        checkItems.on('change', function() {
            updateButton();
            if ($('.check-item:checked').length === checkItems.length) {
                checkAll.prop('checked', true);
            } else {
                checkAll.prop('checked', false);
            }
        });
    });
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
