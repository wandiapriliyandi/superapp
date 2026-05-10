<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0">Checklist Berkas Fisik Pendaftar</h5>
        <p class="text-muted small">Kelola kelengkapan dokumen pendaftar di bawah ini.</p>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID / Nama Pendaftar</th>
                        <th>Status Seleksi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($pendaftar as $p): ?>
                    <tr>
                        <td>
                            <div class="fw-bold"><?= $p['nama_lengkap'] ?></div>
                            <div class="small text-muted">ID: <?= $p['nomor_pendaftaran'] ?></div>
                        </td>
                        <td>
                            <span class="badge bg-<?= $p['status_seleksi'] == 'Lulus' ? 'success' : ($p['status_seleksi'] == 'Tidak Lulus' ? 'danger' : 'info') ?>">
                                <?= $p['status_seleksi'] ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="<?= base_url('ppdb/berkas/detail/'.$p['id']) ?>" class="btn btn-primary btn-sm px-3 rounded-pill shadow-sm">
                                <i class="bi bi-check2-square me-1"></i> Cek Berkas
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
