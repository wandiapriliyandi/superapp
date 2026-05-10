<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">Tambah Syarat Berkas</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('ppdb/berkas/syarat-save') ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Dokumen Fisik</label>
                        <input type="text" name="nama_berkas" class="form-control border-0 shadow-sm bg-body-tertiary" placeholder="Contoh: Fotokopi KK" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_wajib" value="1" checked>
                            <label class="form-check-item fw-bold small">Wajib Dikumpulkan?</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">➕ Tambah Syarat</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Daftar Dokumen Yang Diatur</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Dokumen</th>
                                <th>Sifat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($syarat as $s): ?>
                            <tr>
                                <td class="fw-bold"><?= $s['nama_berkas'] ?></td>
                                <td>
                                    <?= $s['is_wajib'] ? '<span class="badge bg-danger">Wajib</span>' : '<span class="badge bg-secondary">Opsional</span>' ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('ppdb/berkas/syarat-delete/'.$s['id']) ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Hapus syarat ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
