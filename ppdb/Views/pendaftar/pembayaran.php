<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">Form Pembayaran</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('ppdb/pendaftar/save-pembayaran') ?>" method="POST">
                    <input type="hidden" name="id_pendaftar" value="<?= $p['id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nomor Kwitansi</label>
                        <div class="alert alert-light border-0 py-2 small mb-0 text-muted italic">
                            <i class="bi bi-info-circle me-1"></i> Akan dibuat otomatis saat disimpan
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Pendaftar</label>
                        <input type="text" class="form-control bg-light border-0" value="<?= $p['nama_lengkap'] ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Jumlah Bayar (Rp)</label>
                        <input type="number" name="jumlah" class="form-control border-0 shadow-sm" placeholder="Contoh: 250000" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Tanggal Bayar</label>
                            <input type="date" name="tanggal_bayar" class="form-control border-0 shadow-sm" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Metode</label>
                            <select name="metode_bayar" class="form-select border-0 shadow-sm">
                                <option value="Cash">Cash</option>
                                <option value="Transfer">Transfer</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Keterangan</label>
                        <textarea name="keterangan" class="form-control border-0 shadow-sm" rows="2" placeholder="Catatan tambahan..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow">
                        <i class="bi bi-cash-coin me-1"></i> Simpan Pembayaran
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">Riwayat Pembayaran</h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Kwitansi</th>
                                <th>Tanggal</th>
                                <th>Jumlah</th>
                                <th class="text-center">Struk</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($riwayat as $r): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?= $r['nomor_kwitansi'] ?></div>
                                    <div class="small text-muted"><?= $r['metode_bayar'] ?></div>
                                </td>
                                <td><?= date('d/m/Y', strtotime($r['tanggal_bayar'])) ?></td>
                                <td class="fw-bold text-success">Rp <?= number_format($r['jumlah'], 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <a href="<?= base_url('ppdb/pendaftar/cetak-struk/'.$r['id']) ?>" target="_blank" class="btn btn-light btn-sm rounded-circle">
                                        <i class="bi bi-printer text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($riwayat)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted small">Belum ada transaksi pembayaran.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <a href="<?= base_url('ppdb/pendaftar') ?>" class="btn btn-link text-decoration-none text-muted small">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pendaftar
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
