<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Data Tagihan SPP</h5>
                <div class="d-flex gap-2">
                    <button type="button" id="btn-submit-massal" class="btn btn-primary btn-sm rounded-pill px-3 d-none">
                        <i class="bi bi-wallet2 me-1"></i>Bayar Terpilih (<span id="count-checked">0</span>)
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-outline-success btn-sm rounded-pill px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-download me-1"></i>Eksport
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                            <li><a class="dropdown-item" href="<?= base_url('spp/tagihan/export/excel?' . http_build_query($filter)) ?>"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel (.xlsx)</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('spp/tagihan/export/word?' . http_build_query($filter)) ?>"><i class="bi bi-file-earmark-word me-2 text-primary"></i>Word (.doc)</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('spp/tagihan/export/pdf?' . http_build_query($filter)) ?>" target="_blank"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF / Cetak</a></li>
                        </ul>
                    </div>
                    <a href="<?= base_url('spp/tagihan/generate') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                        <i class="bi bi-magic me-1"></i>Generate Masal
                    </a>
                </div>
            </div>
            
            <!-- Form Filter (Mandiri, tidak nested) -->
            <div class="card-body bg-light border-bottom py-2 px-3 overflow-auto">
                <form action="<?= base_url('spp/tagihan') ?>" method="GET" class="d-flex flex-nowrap gap-2 align-items-center">
                    <div style="min-width: 250px;">
                        <select name="nisn" class="form-select form-select-sm rounded-pill">
                            <option value="">-- Semua Santri --</option>
                            <?php foreach($santri as $s): ?>
                                <option value="<?= $s['nisn'] ?>" <?= ($filter['nisn'] ?? '') == $s['nisn'] ? 'selected' : '' ?>><?= $s['nama_lengkap'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="min-width: 150px;">
                        <input type="text" name="q" class="form-control form-control-sm rounded-pill" placeholder="Cari Ket. (KJP)" value="<?= $filter['q'] ?>">
                    </div>
                    <div style="min-width: 130px;">
                        <select name="status" class="form-select form-select-sm rounded-pill">
                            <option value="">-- Status --</option>
                            <option value="Belum Lunas" <?= $filter['status'] == 'Belum Lunas' ? 'selected' : '' ?>>Belum Lunas</option>
                            <option value="Cicilan" <?= $filter['status'] == 'Cicilan' ? 'selected' : '' ?>>Cicilan</option>
                            <option value="Lunas" <?= $filter['status'] == 'Lunas' ? 'selected' : '' ?>>Lunas</option>
                        </select>
                    </div>
                    <div style="min-width: 100px;">
                        <select name="bulan" class="form-select form-select-sm rounded-pill">
                            <option value="">-- Bln --</option>
                            <?php for($i=1; $i<=12; $i++): ?>
                                <option value="<?= $i ?>" <?= $filter['bulan'] == $i ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="ms-auto d-flex gap-1">
                        <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3">Filter</button>
                        <a href="<?= base_url('spp/tagihan') ?>" class="btn btn-light btn-sm rounded-pill px-3 border text-dark">Reset</a>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                <form action="<?= base_url('spp/pembayaran/bayar-massal') ?>" method="POST" id="form-massal">
                    <?= csrf_field() ?>
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
                                    <th>Potongan</th>
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
                                        <small class="text-muted">NISN: <?= $t['nisn'] ?></small>
                                    </td>
                                    <td><?= $t['bulan'] == 0 ? '<span class="badge bg-secondary">Tahunan</span>' : $months[$t['bulan']] ?> <?= $t['tahun'] ?></td>
                                    <td><?= $t['nama_tarif'] ?></td>
                                    <td>Rp <?= number_format($t['nominal_tagihan'], 0, ',', '.') ?></td>
                                    <td>
                                        <?php if($t['diskon'] > 0): ?>
                                            <span class="text-danger fw-bold">- Rp <?= number_format($t['diskon'], 0, ',', '.') ?></span>
                                        <?php endif; ?>
                                        
                                        <?php if($t['keterangan_diskon']): ?>
                                            <?= $t['diskon'] > 0 ? '<br>' : '' ?>
                                            <span class="badge bg-info text-white small"><i class="bi bi-info-circle me-1"></i> <?= $t['keterangan_diskon'] ?></span>
                                        <?php elseif($t['diskon'] <= 0): ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-success fw-bold">Rp <?= number_format($t['total_terbayar'], 0, ',', '.') ?></td>
                                    <td><span class="badge rounded-pill <?= $badgeClass ?>"><?= $t['status'] ?></span></td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <a href="<?= base_url('spp/pembayaran/bayar/' . $t['id']) ?>" class="btn btn-sm btn-primary rounded-pill px-3 me-1">Bayar</a>
                                            <a href="<?= base_url('spp/tagihan/edit/' . $t['id']) ?>" class="btn btn-sm btn-outline-info rounded-circle me-1" title="Edit Nominal">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="<?= base_url('spp/tagihan/delete/' . $t['id']) ?>" class="btn btn-sm btn-outline-danger rounded-circle" onclick="return confirm('Hapus tagihan ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($tagihan)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">Belum ada data tagihan.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
            if ($('.check-item:checked').length === checkItems.length && checkItems.length > 0) {
                checkAll.prop('checked', true);
            } else {
                checkAll.prop('checked', false);
            }
        });

        $('#btn-submit-massal').on('click', function() {
            $('#form-massal').submit();
        });
    });
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
