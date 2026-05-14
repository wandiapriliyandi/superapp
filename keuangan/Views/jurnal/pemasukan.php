<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-arrow-down me-2"></i>Catat Kas Masuk (Pemasukan)</h5>
                </div>
                <div class="card-body p-4">
                    <form action="<?= base_url('keuangan/jurnal/save_simple') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="type" value="pemasukan">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor Bukti</label>
                            <input type="text" class="form-control bg-light" value="<?= $nomor_jurnal ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Masuk ke Akun (Kas/Bank)</label>
                            <select name="akun_kas_id" class="form-select" required>
                                <option value="">-- Pilih Akun Kas --</option>
                                <?php foreach($akun_kas as $a): ?>
                                    <option value="<?= $a['id'] ?>"><?= $a['kode_akun'] ?> - <?= $a['nama_akun'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Diterima Dari / Kategori Pendapatan</label>
                            <select name="akun_lawan_id" class="form-select select2" required>
                                <option value="">-- Pilih Akun Sumber --</option>
                                <?php foreach($akun_lawan as $a): ?>
                                    <option value="<?= $a['id'] ?>"><?= $a['kode_akun'] ?> - <?= $a['nama_akun'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nominal (Rp)</label>
                            <input type="number" name="nominal" class="form-control form-control-lg fw-bold text-primary" placeholder="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Contoh: Penerimaan donasi, Penjualan aset, dll" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Referensi (Opsional)</label>
                            <input type="text" name="referensi" class="form-control" placeholder="No. Nota / Kwitansi Luar">
                        </div>

                        <hr class="my-4">

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                                <i class="fas fa-save me-2"></i>Simpan Pemasukan
                            </button>
                            <a href="<?= base_url('keuangan/jurnal') ?>" class="btn btn-link text-muted mt-2">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
