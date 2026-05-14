<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Mata Pelajaran</h5>
        <a href="<?= base_url('akademik/mapel/create') ?>" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg me-2"></i> Tambah Mapel
        </a>
    </div>
    <div class="card-body p-4 pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 rounded-start">Kode</th>
                        <th class="border-0">Nama Mata Pelajaran</th>
                        <th class="border-0">Kelompok</th>
                        <th class="border-0 text-end rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($mapel)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">Belum ada data mata pelajaran</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($mapel as $m): ?>
                        <tr>
                            <td class="text-muted"><?= $m['kode_mapel'] ?></td>
                            <td class="fw-bold text-dark"><?= $m['nama_mapel'] ?></td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2 
                                    <?= $m['kelompok'] == 'Pesantren' ? 'bg-success bg-opacity-10 text-success' : ($m['kelompok'] == 'Nasional' ? 'bg-primary bg-opacity-10 text-primary' : 'bg-info bg-opacity-10 text-info') ?>">
                                    <?= $m['kelompok'] ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= base_url('akademik/mapel/edit/' . $m['id']) ?>" class="btn btn-sm btn-light-primary rounded-pill px-3">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="<?= base_url('akademik/mapel/delete/' . $m['id']) ?>" class="btn btn-sm btn-light-danger rounded-pill px-3" onclick="return confirm('Hapus mata pelajaran ini?')">
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
