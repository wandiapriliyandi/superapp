<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-10 mx-auto">
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
                        <input type="text" name="nama_role" class="form-control form-control-lg rounded-3"
                               value="<?= old('nama_role', $role['nama_role'] ?? '') ?>"
                               placeholder="Contoh: Petugas SPP, Pimpinan, dll..." required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-primary d-block mb-3">
                            Daftar Izin Akses Modul
                            <span class="text-muted fw-normal ms-2 fs-7">— centang modul untuk mengaktifkan, lalu pilih aksi yang diizinkan</span>
                        </label>

                        <div class="row g-3">
                            <?php
                            $currentPermissions = [];
                            if (isset($role) && !empty($role['permissions'])) {
                                $currentPermissions = json_decode($role['permissions'], true) ?: [];
                            }

                            // Modul yang bisa dikonfigurasi aksi CRUD-nya
                            $crudModules = [
                                'perijinan', 'poskestren', 'akademik', 'spp',
                                'keuangan', 'kepegawaian', 'perpustakaan', 'ppdb', 'e-learning', 'monitoring'
                            ];

                            // Label aksi CRUD
                            $crudActions = [
                                'create' => ['label' => 'Tambah', 'icon' => 'bi-plus-circle'],
                                'update' => ['label' => 'Ubah',   'icon' => 'bi-pencil'],
                                'delete' => ['label' => 'Hapus',  'icon' => 'bi-trash'],
                                'export' => ['label' => 'Ekspor', 'icon' => 'bi-download'],
                            ];
                            ?>

                            <?php foreach ($modules as $key => $label): ?>
                                <?php
                                $isChecked   = in_array($key, $currentPermissions);
                                $isCrudAble  = in_array($key, $crudModules);
                                $isSpecial   = in_array($key, ['*', 'setting', 'activity']);
                                $uniqueId    = 'module_' . str_replace('-', '_', $key);
                                ?>
                                <div class="col-md-6">
                                    <div class="module-card card border border-light-subtle rounded-3 p-3 h-100 <?= $isChecked ? 'module-active' : '' ?>"
                                         id="card_<?= $uniqueId ?>">

                                        <!-- Switch Modul Utama -->
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input module-switch"
                                                   type="checkbox"
                                                   name="permissions[]"
                                                   value="<?= $key ?>"
                                                   id="<?= $uniqueId ?>"
                                                   <?= $isChecked ? 'checked' : '' ?>
                                                   data-module="<?= $key ?>"
                                                   onchange="toggleModuleActions(this)">
                                            <label class="form-check-label fw-semibold text-dark" for="<?= $uniqueId ?>">
                                                <?= esc($label) ?>
                                            </label>
                                            <div class="small text-muted mt-1">
                                                <?php if ($key === '*'): ?>
                                                    Mengizinkan akses penuh ke semua fitur sistem.
                                                <?php elseif (in_array($key, ['setting', 'activity'])): ?>
                                                    Akses modul sistem — hanya untuk superadmin.
                                                <?php else: ?>
                                                    Akses tampil/baca data modul <strong><?= strtolower($key) ?></strong>.
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Sub-checkbox CRUD (hanya untuk modul reguler) -->
                                        <?php if ($isCrudAble && !$isSpecial): ?>
                                        <div class="crud-actions mt-3 pt-3 border-top" id="actions_<?= $uniqueId ?>"
                                             style="<?= $isChecked ? '' : 'display:none;' ?>">
                                            <div class="small text-muted mb-2 fw-semibold">
                                                <i class="bi bi-sliders me-1"></i>Izin Aksi:
                                            </div>
                                            <div class="d-flex flex-wrap gap-2">
                                                <?php foreach ($crudActions as $action => $actionData): ?>
                                                    <?php
                                                    $actionPermKey  = $key . '_' . $action;
                                                    $actionChecked  = in_array($actionPermKey, $currentPermissions);
                                                    $actionUniqueId = $uniqueId . '_' . $action;
                                                    ?>
                                                    <div class="form-check crud-check">
                                                        <input class="form-check-input"
                                                               type="checkbox"
                                                               name="permissions[]"
                                                               value="<?= $actionPermKey ?>"
                                                               id="<?= $actionUniqueId ?>"
                                                               <?= $actionChecked ? 'checked' : '' ?>
                                                               <?= !$isChecked ? 'disabled' : '' ?>>
                                                        <label class="form-check-label small" for="<?= $actionUniqueId ?>">
                                                            <i class="<?= $actionData['icon'] ?> me-1"></i><?= $actionData['label'] ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
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
.module-card {
    transition: all 0.25s ease-in-out;
}
.module-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.07);
    border-color: var(--bs-primary-border-subtle) !important;
}
.module-card.module-active {
    border-color: rgba(var(--bs-primary-rgb), 0.35) !important;
    background-color: rgba(var(--bs-primary-rgb), 0.03);
}
.crud-check .form-check-label {
    cursor: pointer;
    color: #555;
}
.crud-check input:checked + .form-check-label {
    color: var(--bs-primary);
    font-weight: 600;
}
.fs-7 { font-size: 0.78rem; }
</style>

<script>
function toggleModuleActions(switchEl) {
    const moduleKey  = switchEl.dataset.module;
    const uniqueId   = 'module_' + moduleKey.replace(/-/g, '_');
    const card       = document.getElementById('card_' + uniqueId);
    const actionsDiv = document.getElementById('actions_' + uniqueId);

    if (switchEl.checked) {
        card.classList.add('module-active');
        if (actionsDiv) {
            actionsDiv.style.display = '';
            actionsDiv.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.disabled = false);
        }
    } else {
        card.classList.remove('module-active');
        if (actionsDiv) {
            actionsDiv.style.display = 'none';
            actionsDiv.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.disabled = true;
                cb.checked  = false; // auto-uncheck aksi CRUD jika modul dinonaktifkan
            });
        }
    }
}
</script>
<?= $this->endSection() ?>
