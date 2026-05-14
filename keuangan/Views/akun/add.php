<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h3 class="fw-bold">Tambah Akun Baru 🆕</h3>
            <p class="text-muted">Gunakan pengkodean yang standar agar laporan keuangan mudah dianalisis.</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="<?= base_url('keuangan/akun/save') ?>" method="post">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kode Akun</label>
                            <input type="text" name="kode_akun" class="form-control" placeholder="Contoh: 1-101" required>
                            <small class="text-muted">Gunakan format angka dan strip (misal: 1-101 untuk Kas).</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Akun</label>
                            <input type="text" name="nama_akun" class="form-control" placeholder="Contoh: Kas Utama Pesantren" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="Aset">Aset</option>
                                <option value="Kewajiban">Kewajiban</option>
                                <option value="Ekuitas">Ekuitas</option>
                                <option value="Pendapatan">Pendapatan</option>
                                <option value="Beban">Beban</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Saldo Normal</label>
                            <select name="saldo_normal" class="form-select" required>
                                <option value="Debit">Debit (Default untuk Aset & Beban)</option>
                                <option value="Kredit">Kredit (Default untuk Kewajiban, Ekuitas & Pendapatan)</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Parent Akun (Opsional)</label>
                            <select name="parent_id" class="form-select">
                                <option value="">-- Tanpa Parent --</option>
                                <?php foreach ($parent_akun as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= $p['kode_akun'] ?> - <?= $p['nama_akun'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('keuangan/akun') ?>" class="btn btn-light px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-4">Simpan Akun</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
