<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-primary py-3 px-4 text-white border-0">
                <h5 class="fw-bold mb-0"><i class="bi bi-wallet2 me-2"></i>Konfirmasi Pembayaran Sekaligus</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('spp/pembayaran/save-massal') ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>Santri</th>
                                    <th>Keterangan Tagihan</th>
                                    <th class="text-end">Sisa Tagihan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total = 0;
                                $months = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                                foreach($tagihan as $t): 
                                    $sisa = $t['nominal_tagihan'] - $t['total_terbayar'];
                                    $total += $sisa;
                                ?>
                                <tr>
                                    <td>
                                        <input type="hidden" name="tagihan_ids[]" value="<?= $t['id'] ?>">
                                        <div class="fw-bold"><?= $t['nama_santri'] ?></div>
                                    </td>
                                    <td><?= $t['nama_tarif'] ?> - <?= $months[$t['bulan']] ?> <?= $t['tahun'] ?></td>
                                    <td class="text-end fw-bold text-primary">Rp <?= number_format($sisa, 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="bg-light fw-bold">
                                <tr>
                                    <td colspan="2" class="text-end py-3">TOTAL YANG HARUS DIBAYAR:</td>
                                    <td class="text-end text-success fs-5 py-3">Rp <?= number_format($total, 0, ',', '.') ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row g-4 bg-light p-4 rounded-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Metode Pembayaran</label>
                            <select name="metode_pembayaran" class="form-select border-0 shadow-sm" required>
                                <option value="Tunai">Tunai / Cash</option>
                                <option value="Transfer">Transfer Bank</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Keterangan Tambahan</label>
                            <input type="text" name="keterangan" class="form-control border-0 shadow-sm" placeholder="Contoh: Titipan wali santri...">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('spp/tagihan') ?>" class="btn btn-light px-4 py-2 fw-semibold">Batal</a>
                        <button type="submit" class="btn btn-success px-5 py-2 fw-bold shadow-sm rounded-pill" onclick="return confirm('Proses pembayaran lunas untuk semua tagihan terpilih?')">
                            <i class="bi bi-check-circle me-1"></i>Proses Pembayaran Lunas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
