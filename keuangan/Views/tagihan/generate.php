<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Generate Tagihan Massal</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info rounded-4 border-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Fitur ini akan membuat tagihan SPP untuk semua santri yang berstatus <strong>Aktif</strong>.
                </div>
                <form action="<?= base_url('keuangan/tagihan/process-generate') ?>" method="post">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-select rounded-3" required>
                                <?php
                                $months = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                                foreach ($months as $num => $name):
                                ?>
                                <option value="<?= $num ?>" <?= $num == date('n') ? 'selected' : '' ?>><?= $name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tahun</label>
                            <input type="number" name="tahun" class="form-control rounded-3" value="<?= date('Y') ?>" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Pilih Jenis Tarif</label>
                        <select name="tarif_id" class="form-select rounded-3" required>
                            <option value="">-- Pilih Tarif --</option>
                            <?php foreach ($tarif as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= $t['nama_tarif'] ?> (Rp <?= number_format($t['nominal'], 0, ',', '.') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('keuangan') ?>" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Generate Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
