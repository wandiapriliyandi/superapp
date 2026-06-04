<?= $this->extend('layout/main') ?>
 
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="bi bi-shield-lock-fill me-2 text-primary"></i> Manajemen Hak Akses (Role & Permission)</h5>
                <a href="<?= base_url('setting/roles/tambah') ?>" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Hak Akses
                </a>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 8%;">No</th>
                                <th style="width: 25%;">Nama Hak Akses (Role)</th>
                                <th>Izin Akses Modul (Permissions)</th>
                                <th class="text-center" style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($roles)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada data hak akses.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($roles as $role): ?>
                                    <tr>
                                        <td class="fw-bold text-muted"><?= $no++ ?></td>
                                        <td class="fw-bold text-dark"><?= esc($role['nama_role']) ?></td>
                                        <td>
                                            <?php 
                                                $permissions = json_decode($role['permissions'], true) ?: [];
                                                if (in_array('*', $permissions)) {
                                                    echo '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill me-1 mb-1"><i class="bi bi-star-fill me-1"></i> AKSES SUPERADMIN (SEMUA)</span>';
                                                } else {
                                                    foreach ($permissions as $perm) {
                                                        echo '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1 rounded-pill me-1 mb-1">' . esc($perm) . '</span>';
                                                    }
                                                }
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('setting/roles/edit/' . $role['id']) ?>" class="btn btn-sm btn-outline-primary border-0 me-1" title="Edit">
                                                <i class="bi bi-pencil-square fs-5"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger border-0" 
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    data-href="<?= base_url('setting/roles/hapus/' . $role['id']) ?>" title="Hapus">
                                                <i class="bi bi-trash-fill fs-5"></i>
                                            </button>
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
</div>
<?= $this->endSection() ?>
