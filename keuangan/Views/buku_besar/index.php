<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h3 class="fw-bold">Buku Besar (General Ledger) 📊</h3>
            <p class="text-muted">Mutasi transaksi untuk setiap akun dalam periode tertentu.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="<?= base_url('keuangan/buku-besar') ?>" method="get" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Pilih Akun</label>
                    <select name="akun_id" class="form-select select2" required>
                        <option value="">-- Pilih Akun --</option>
                        <?php foreach ($akun as $a): ?>
                            <option value="<?= $a['id'] ?>" <?= $selected_akun == $a['id'] ? 'selected' : '' ?>>
                                <?= $a['kode_akun'] ?> - <?= $a['nama_akun'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Mulai Tanggal</label>
                    <input type="date" name="tgl_mulai" class="form-control" value="<?= $tgl_mulai ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Sampai Tanggal</label>
                    <input type="date" name="tgl_selesai" class="form-control" value="<?= $tgl_selesai ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($selected_akun): ?>
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Nomor Jurnal</th>
                                <th>Keterangan</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end">Kredit</th>
                                <th class="text-end">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3" class="fw-bold">SALDO AWAL</td>
                                <td class="text-end">-</td>
                                <td class="text-end">-</td>
                                <td class="text-end fw-bold"><?= number_format($saldo_awal, 2) ?></td>
                            </tr>
                            <?php 
                            $current_saldo = $saldo_awal;
                            foreach ($transaksi as $t): 
                                $current_saldo += $t['debit'] - $t['kredit'];
                            ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($t['tanggal'])) ?></td>
                                    <td><span class="badge bg-light text-primary"><?= $t['nomor_jurnal'] ?></span></td>
                                    <td>
                                        <?= $t['ket_jurnal'] ?>
                                        <?php if ($t['keterangan_item']): ?>
                                            <br><small class="text-muted"><?= $t['keterangan_item'] ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end text-success"><?= $t['debit'] > 0 ? number_format($t['debit'], 2) : '-' ?></td>
                                    <td class="text-end text-danger"><?= $t['kredit'] > 0 ? number_format($t['kredit'], 2) : '-' ?></td>
                                    <td class="text-end"><?= number_format($current_saldo, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-light fw-bold">
                            <tr>
                                <td colspan="5" class="text-end">SALDO AKHIR</td>
                                <td class="text-end text-primary"><?= number_format($current_saldo, 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-search fa-4x text-light mb-3"></i>
            <h5 class="text-muted">Pilih akun dan periode untuk melihat detail buku besar.</h5>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
