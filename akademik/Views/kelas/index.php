<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Daftar Kelas</h5>
        <a href="<?= base_url('akademik/kelas/create') ?>" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg me-2"></i> Tambah Kelas
        </a>
    </div>
    <div class="card-body p-4 pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 rounded-start">Tingkat</th>
                        <th class="border-0">Nama Kelas</th>
                        <th class="border-0">Wali Kelas</th>
                        <th class="border-0 text-end rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kelas)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">Belum ada data kelas</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($kelas as $k): ?>
                        <tr>
                            <td><span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill"><?= $k['tingkat'] ?></span></td>
                            <td class="fw-bold text-dark"><?= $k['nama_kelas'] ?></td>
                            <td><?= $k['nama_wali'] ?? '<span class="text-muted italic">Belum ditentukan</span>' ?></td>
                            <td class="text-end">
                                <a href="<?= base_url('akademik/kelas/edit/' . $k['id']) ?>" class="btn btn-sm btn-light-primary rounded-pill px-3">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="<?= base_url('akademik/kelas/delete/' . $k['id']) ?>" class="btn btn-sm btn-light-danger rounded-pill px-3" onclick="return confirm('Hapus kelas ini?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
