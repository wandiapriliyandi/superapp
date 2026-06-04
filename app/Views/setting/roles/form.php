<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-shield-lock-fill me-2 text-primary"></i> 
                    <?= isset($role) ? 'Edit Hak Akses' : 'Tambah Hak Akses Baru' ?>
                </h5>
            </div>
            <div class="card-body p-4">
                <?php $action = isset($role) ? 'setting/roles/update/' . $role['id'] : 'setting/roles/simpan'; ?>
                <form action="<?= base_url($action) ?>" method="POST">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-primary">Nama Hak Akses (Role)</label>
                        <input type="text" name="nama_role" class="form-control form-control-lg rounded-3" value="<?= old('nama_role', $role['nama_role'] ?? '') ?>" placeholder="Contoh: Petugas SPP, Pimpinan, dll..." required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-primary d-block mb-3">Daftar Izin Akses Modul</label>
                        
                        <div class="row g-3">
                            <?php 
                            $currentPermissions = [];
                            if (isset($role) && !empty($role['permissions'])) {
                                $currentPermissions = json_decode($role['permissions'], true) ?: [];
                            }
                            ?>
                            
                            <?php foreach ($modules as $key => $label): ?>
                                <div class="col-md-6">
                                    <div class="card border border-light-subtle rounded-3 p-3 h-100 hover-shadow transition">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= $key ?>" id="module_<?= $key ?>" <?= in_array($key, $currentPermissions) ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-semibold text-dark cursor-pointer" for="module_<?= $key ?>">
                                                <?= esc($label) ?>
                                            </label>
                                            <div class="small text-muted mt-1">Mengizinkan pengguna mengakses rute/menu <?= strtolower($key == '*' ? 'semua fitur' : $key) ?>.</div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <a href="<?= base_url('setting/roles') ?>" class="btn btn-light rounded-pill px-4 fw-bold">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow fw-bold">
                            <i class="bi bi-save me-1"></i> Simpan Hak Akses
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    border-color: var(--bs-primary-border-subtle) !important;
}
.transition {
    transition: all 0.25s ease-in-out;
}
.cursor-pointer {
    cursor: pointer;
}
</style>
<?= $this->endSection() ?>
