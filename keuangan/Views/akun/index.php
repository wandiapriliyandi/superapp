<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold">Bagan Akun (COA) 📑</h3>
            <p class="text-muted">Daftar semua akun yang digunakan untuk pencatatan transaksi.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="<?= base_url('keuangan/akun/add') ?>" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-2"></i>Tambah Akun
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th width="150">Kode Akun</th>
                            <th>Nama Akun</th>
                            <th>Kategori</th>
                            <th>Saldo Normal</th>
                            <th>Status</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($akun)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">Belum ada akun. Klik Tambah Akun untuk memulai.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($akun as $row): ?>
                                <tr>
                                    <td class="fw-bold"><?= $row['kode_akun'] ?></td>
                                    <td>
                                        <?php if ($row['parent_id']): ?>
                                            <span class="ms-4 text-muted">↳</span>
                                        <?php endif; ?>
                                        <?= $row['nama_akun'] ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-opacity-10 text-dark" style="background-color: var(--bs-info-bg-subtle)">
                                            <?= $row['kategori'] ?>
                                        </span>
                                    </td>
                                    <td><?= $row['saldo_normal'] ?></td>
                                    <td>
                                        <?php if ($row['is_aktif']): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Non-aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('keuangan/akun/edit/' . $row['id']) ?>" class="btn btn-sm btn-light rounded-circle text-primary mx-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('keuangan/akun/delete/' . $row['id']) ?>" class="btn btn-sm btn-light rounded-circle text-danger mx-1" onclick="return confirm('Hapus akun ini?')" title="Hapus">
                                            <i class="fas fa-trash"></i>
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
</div>
<?= $this->endSection() ?>
