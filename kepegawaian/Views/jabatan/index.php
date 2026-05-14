<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-0">Tambah Jabatan</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <form action="<?= base_url('kepegawaian/jabatan/save') ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Jabatan / Amanah</label>
                        <input type="text" name="nama_jabatan" class="form-control" placeholder="Contoh: Wali Kamar, Guru, dll" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Gaji Pokok</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="gaji_pokok" class="form-control" placeholder="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Tunj. Makan</label>
                            <input type="number" name="tunjangan_makan" class="form-control" placeholder="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Tunj. Transport</label>
                            <input type="number" name="tunjangan_transport" class="form-control" placeholder="0">
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-pill py-2">Simpan Jabatan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-0">Daftar Jabatan / Amanah</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Nama Jabatan</th>
                                <th>Gaji Pokok</th>
                                <th>Tunjangan</th>
                                <th class="pe-4 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($jabatan as $j): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?= $j['nama_jabatan'] ?></div>
                                </td>
                                <td>Rp <?= number_format($j['gaji_pokok'], 0, ',', '.') ?></td>
                                <td>
                                    <small class="text-muted d-block">Makan: Rp <?= number_format($j['tunjangan_makan'], 0, ',', '.') ?></small>
                                    <small class="text-muted d-block">Transport: Rp <?= number_format($j['tunjangan_transport'], 0, ',', '.') ?></small>
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="javascript:void(0)" class="btn btn-sm btn-light rounded-circle text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-href="<?= base_url('kepegawaian/jabatan/delete/' . $j['id']) ?>">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($jabatan)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada data jabatan.</td>
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
