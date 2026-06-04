<?= $this->extend('layout/main') ?>
 
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="bi bi-people-fill me-2 text-primary"></i> Manajemen Pengguna</h5>
                <a href="<?= base_url('setting/users/tambah') ?>" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Pengguna
                </a>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 8%;">No</th>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Hak Akses (Role)</th>
                                <th>Terdaftar Pada</th>
                                <th class="text-center" style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data pengguna.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($users as $user): ?>
                                    <tr>
                                        <td class="fw-bold text-muted"><?= $no++ ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['nama_lengkap']) ?>&background=random" class="rounded-circle me-3" width="36" height="36">
                                                <div class="fw-bold text-dark"><?= esc($user['nama_lengkap']) ?></div>
                                            </div>
                                        </td>
                                        <td><code class="text-secondary small fw-semibold"><?= esc($user['username']) ?></code></td>
                                        <td>
                                            <?php if ($user['nama_role'] === 'superadmin'): ?>
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill">
                                                    <i class="bi bi-shield-fill-check me-1"></i> Superadmin
                                                </span>
                                            <?php elseif ($user['nama_role']): ?>
                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill">
                                                    <?= esc($user['nama_role']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1 rounded-pill">
                                                    Tanpa Hak Akses
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small text-muted"><?= date('d M Y, H:i', strtotime($user['created_at'])) ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('setting/users/edit/' . $user['id']) ?>" class="btn btn-sm btn-outline-primary border-0 me-1" title="Edit">
                                                <i class="bi bi-pencil-square fs-5"></i>
                                            </a>
                                            <?php if ($user['id'] != session()->get('user_id')): ?>
                                                <button type="button" class="btn btn-sm btn-outline-danger border-0" 
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        data-href="<?= base_url('setting/users/hapus/' . $user['id']) ?>" title="Hapus">
                                                    <i class="bi bi-trash-fill fs-5"></i>
                                                </button>
                                            <?php endif; ?>
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
