<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-0">Tambah Departemen</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <form action="<?= base_url('kepegawaian/departemen/save') ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Departemen</label>
                        <input type="text" name="nama_departemen" class="form-control" placeholder="Contoh: Kesantrian, Akademik, dll" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Deskripsi singkat unit kerja"></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-pill py-2">Simpan Departemen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-0">Daftar Departemen / Unit Kerja</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Nama Departemen</th>
                                <th>Keterangan</th>
                                <th class="pe-4 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach($departemen as $d): ?>
                            <tr>
                                <td class="ps-4"><?= $no++ ?></td>
                                <td class="fw-bold text-primary"><?= $d['nama_departemen'] ?></td>
                                <td><?= $d['keterangan'] ?: '-' ?></td>
                                <td class="pe-4 text-end">
                                    <a href="javascript:void(0)" class="btn btn-sm btn-light rounded-circle text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-href="<?= base_url('kepegawaian/departemen/delete/' . $d['id']) ?>">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($departemen)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada data departemen.</td>
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
