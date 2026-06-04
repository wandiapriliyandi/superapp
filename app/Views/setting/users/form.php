<?= $this->extend('layout/main') ?>
 
<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-person-fill-gear me-2 text-primary"></i> 
                    <?= isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna Baru' ?>
                </h5>
            </div>
            <div class="card-body p-4">
                <?php $action = isset($user) ? 'setting/users/update/' . $user['id'] : 'setting/users/simpan'; ?>
                <form action="<?= base_url($action) ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-primary">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="<?= old('nama_lengkap', $user['nama_lengkap'] ?? '') ?>" placeholder="Masukkan nama lengkap..." required>
                    </div>
 
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-primary">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= old('username', $user['username'] ?? '') ?>" placeholder="Masukkan username..." required>
                    </div>
 
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-primary">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="<?= isset($user) ? 'Kosongkan jika tidak ingin diubah' : 'Masukkan password...' ?>" <?= isset($user) ? '' : 'required' ?>>
                        <?php if (isset($user)): ?>
                            <div class="form-text small text-muted">Isi kolom password hanya jika ingin mengganti password lama.</div>
                        <?php endif; ?>
                    </div>
 
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-primary">Hak Akses (Role)</label>
                        <select name="role_id" class="form-select">
                            <option value="">-- Pilih Hak Akses --</option>
                            <?php foreach ($roles as $role): ?>
                                <?php $selected = (isset($user) && $user['role_id'] == $role['id']) ? 'selected' : ''; ?>
                                <option value="<?= $role['id'] ?>" <?= $selected ?>><?= esc($role['nama_role']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
 
                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="<?= base_url('setting/users') ?>" class="btn btn-light rounded-pill px-4 fw-bold">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow fw-bold">
                            <i class="bi bi-save me-1"></i> Simpan Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
