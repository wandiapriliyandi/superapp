<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-xl-10 mx-auto">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock-fill fs-4 text-primary"></i>
                <h5 class="fw-bold mb-0">
                    <?= isset($role) ? 'Edit Hak Akses' : 'Tambah Hak Akses Baru' ?>
                </h5>
            </div>
            <div class="card-body p-4">
                <?php $action = isset($role) ? 'setting/roles/update/' . $role['id'] : 'setting/roles/simpan'; ?>
                <form action="<?= base_url($action) ?>" method="POST">

                    <!-- Nama Role -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-primary">Nama Hak Akses (Role)</label>
                        <input type="text" name="nama_role" class="form-control form-control-lg rounded-3"
                               value="<?= old('nama_role', $role['nama_role'] ?? '') ?>"
                               placeholder="Contoh: Petugas SPP, Pimpinan, Wali Kelas..." required>
                    </div>

                    <!-- Daftar Izin -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-primary d-block mb-1">Konfigurasi Hak Akses Modul</label>
                        <p class="text-muted small mb-3">Aktifkan modul, lalu pilih sub-menu dan aksi yang diizinkan. Jika tidak ada sub-menu yang dipilih, user dapat mengakses semua sub-menu dalam modul tersebut.</p>

                        <?php
                        // ---- Data permissions saat ini ----
                        $currentPermissions = [];
                        if (isset($role) && !empty($role['permissions'])) {
                            $currentPermissions = json_decode($role['permissions'], true) ?: [];
                        }

                        // ---- Definisi aksi CRUD ----
                        $crudActions = [
                            'create' => ['label' => 'Tambah', 'icon' => 'bi-plus-circle-fill',  'color' => 'text-success'],
                            'update' => ['label' => 'Ubah',   'icon' => 'bi-pencil-fill',        'color' => 'text-primary'],
                            'delete' => ['label' => 'Hapus',  'icon' => 'bi-trash-fill',         'color' => 'text-danger'],
                            'export' => ['label' => 'Ekspor', 'icon' => 'bi-download',           'color' => 'text-secondary'],
                        ];

                        // ---- Definisi sub-menu per modul ----
                        $moduleSubMenus = [
                            'spp'          => ['tarif' => 'Tarif SPP', 'tagihan' => 'Tagihan', 'pembayaran' => 'Pembayaran', 'mapping' => 'Pemetaan Tarif'],
                            'akademik'     => ['santri' => 'Data Santri', 'tahun-ajaran' => 'Tahun Ajaran', 'kelas' => 'Kelas', 'mapel' => 'Mata Pelajaran', 'jadwal' => 'Jadwal', 'presensi' => 'Presensi', 'nilai' => 'Nilai'],
                            'kepegawaian'  => ['pegawai' => 'Data Pegawai', 'departemen' => 'Departemen', 'jabatan' => 'Jabatan', 'jadwal' => 'Jadwal Kerja', 'absensi' => 'Absensi', 'cuti' => 'Cuti', 'payroll' => 'Penggajian'],
                            'perpustakaan' => ['buku' => 'Koleksi Buku', 'anggota' => 'Anggota', 'peminjaman' => 'Peminjaman', 'pengaturan' => 'Pengaturan'],
                            'ppdb'         => ['pendaftar' => 'Data Pendaftar', 'berkas' => 'Berkas', 'jadwal' => 'Jadwal Tes', 'pengaturan' => 'Pengaturan'],
                            'perijinan'    => ['perijinan' => 'Data Perizinan', 'rekap' => 'Rekap Laporan', 'pengaturan' => 'Pengaturan'],
                            'poskestren'   => ['kunjungan' => 'Kunjungan / Rekam Medis', 'obat' => 'Data Obat', 'stok' => 'Stok Obat'],
                            'keuangan'     => ['akun' => 'COA / Akun', 'jurnal' => 'Jurnal Umum', 'buku-besar' => 'Buku Besar', 'laporan' => 'Laporan Keuangan'],
                            'alquran'      => ['tahsin' => 'Tahsin Qur\'an', 'tahfidz' => 'Tahfidz Qur\'an', 'laporan' => 'Rekap Laporan'],
                        ];
                        ?>

                        <div class="accordion" id="moduleAccordion">
                            <?php foreach ($modules as $moduleKey => $moduleLabel):
                                $mId       = 'mod_' . str_replace(['-', '.'], '_', $moduleKey);
                                $isChecked = in_array($moduleKey, $currentPermissions);
                                $subMenus  = $moduleSubMenus[$moduleKey] ?? [];
                                $isSpecial = in_array($moduleKey, ['*', 'setting', 'activity']);
                            ?>
                            <div class="accordion-item border rounded-3 mb-2 overflow-hidden module-item <?= $isChecked ? 'module-active' : '' ?>"
                                 id="item_<?= $mId ?>">

                                <!-- Header: Switch Modul -->
                                <div class="accordion-header d-flex align-items-center px-3 py-2 gap-3">
                                    <div class="form-check form-switch mb-0 flex-grow-1">
                                        <input class="form-check-input module-switch"
                                               type="checkbox"
                                               name="permissions[]"
                                               value="<?= $moduleKey ?>"
                                               id="<?= $mId ?>"
                                               <?= $isChecked ? 'checked' : '' ?>
                                               data-module="<?= $moduleKey ?>"
                                               data-target="<?= $mId ?>"
                                               onchange="toggleModule(this)">
                                        <label class="form-check-label fw-bold" for="<?= $mId ?>">
                                            <?= esc($moduleLabel) ?>
                                        </label>
                                    </div>
                                    <?php if (!$isSpecial && !empty($subMenus)): ?>
                                    <button type="button"
                                            class="btn btn-sm btn-link text-muted p-0 submenu-toggle <?= $isChecked ? '' : 'disabled' ?>"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapse_<?= $mId ?>"
                                            id="btn_<?= $mId ?>"
                                            title="Konfigurasi sub-menu">
                                        <i class="bi bi-sliders2"></i> Sub-Menu
                                    </button>
                                    <?php endif; ?>
                                </div>

                                <!-- Body: Sub-Menu + CRUD (collapsible) -->
                                <?php if (!$isSpecial && !empty($subMenus)): ?>
                                <div id="collapse_<?= $mId ?>" class="collapse <?= $isChecked ? 'show' : '' ?>">
                                    <div class="px-4 pb-3 pt-1 border-top bg-light-subtle">
                                        <div class="small text-muted mb-2 fw-semibold">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Pilih sub-menu yang boleh diakses. Jika tidak ada yang dipilih, semua sub-menu dapat diakses.
                                        </div>

                                        <?php foreach ($subMenus as $smKey => $smLabel):
                                            $smId      = $mId . '__' . str_replace('-', '_', $smKey);
                                            $smPermKey = $moduleKey . '.' . $smKey;
                                            $smChecked = in_array($smPermKey, $currentPermissions);
                                        ?>
                                        <div class="submenu-block rounded-3 border p-2 mb-2 bg-white <?= $smChecked ? 'submenu-active' : '' ?>"
                                             id="block_<?= $smId ?>">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <!-- Checkbox Sub-Menu -->
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input submenu-check"
                                                           type="checkbox"
                                                           name="permissions[]"
                                                           value="<?= $smPermKey ?>"
                                                           id="<?= $smId ?>"
                                                           <?= $smChecked ? 'checked' : '' ?>
                                                           <?= !$isChecked ? 'disabled' : '' ?>
                                                           data-block="<?= $smId ?>"
                                                           onchange="toggleSubMenu(this)">
                                                    <label class="form-check-label fw-semibold small" for="<?= $smId ?>">
                                                        <?= esc($smLabel) ?>
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- CRUD Actions (tampil bila sub-menu dicentang) -->
                                            <div class="crud-row d-flex flex-wrap gap-3 ps-4"
                                                 id="crud_<?= $smId ?>"
                                                 style="<?= $smChecked ? '' : 'display:none!important;' ?>">
                                                <?php foreach ($crudActions as $actionKey => $actionData):
                                                    $crudPermKey = $smPermKey . '_' . $actionKey;
                                                    $crudChecked = in_array($crudPermKey, $currentPermissions);
                                                    $crudId      = $smId . '_' . $actionKey;
                                                ?>
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           name="permissions[]"
                                                           value="<?= $crudPermKey ?>"
                                                           id="<?= $crudId ?>"
                                                           <?= $crudChecked ? 'checked' : '' ?>
                                                           <?= (!$isChecked || !$smChecked) ? 'disabled' : '' ?>>
                                                    <label class="form-check-label small <?= $actionData['color'] ?>" for="<?= $crudId ?>">
                                                        <i class="<?= $actionData['icon'] ?> me-1"></i><?= $actionData['label'] ?>
                                                    </label>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php elseif ($isSpecial): ?>
                                <div class="px-4 pb-2 pt-0">
                                    <small class="text-muted">
                                        <?php if ($moduleKey === '*'): ?>
                                            <i class="bi bi-star-fill text-warning me-1"></i>Mengizinkan akses penuh ke semua fitur dan modul.
                                        <?php else: ?>
                                            <i class="bi bi-lock-fill me-1"></i>Akses modul sistem — hanya untuk superadmin yang punya izin (<code>*</code>).
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <?php endif; ?>
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
.module-item {
    transition: all 0.2s ease;
    border-color: #dee2e6 !important;
}
.module-item.module-active {
    border-color: rgba(var(--bs-primary-rgb), 0.4) !important;
    background-color: rgba(var(--bs-primary-rgb), 0.02);
}
.module-item .accordion-header {
    background: transparent;
}
.submenu-block {
    transition: border-color 0.15s ease, background 0.15s ease;
    border-color: #dee2e6 !important;
}
.submenu-block.submenu-active {
    border-color: rgba(var(--bs-primary-rgb), 0.3) !important;
    background-color: rgba(var(--bs-primary-rgb), 0.02) !important;
}
.form-check-input { cursor: pointer; }
.submenu-toggle.disabled { pointer-events: none; opacity: 0.4; }
.crud-row .form-check-label { cursor: pointer; }
</style>

<script>
// Toggle modul utama ON/OFF
function toggleModule(switchEl) {
    const targetId   = switchEl.dataset.target;
    const moduleKey  = switchEl.dataset.module;
    const moduleItem = document.getElementById('item_' + targetId);
    const collapseEl = document.getElementById('collapse_' + targetId);
    const toggleBtn  = document.getElementById('btn_' + targetId);

    if (switchEl.checked) {
        moduleItem.classList.add('module-active');
        if (toggleBtn) toggleBtn.classList.remove('disabled');
        if (collapseEl) {
            // Tampilkan collapse
            new bootstrap.Collapse(collapseEl, {show: true});
            // Enable semua sub-menu checkbox
            collapseEl.querySelectorAll('.submenu-check').forEach(cb => cb.disabled = false);
            // Jangan auto-enable CRUD — biarkan sesuai status sub-menu
        }
    } else {
        moduleItem.classList.remove('module-active');
        if (toggleBtn) toggleBtn.classList.add('disabled');
        if (collapseEl) {
            new bootstrap.Collapse(collapseEl, {hide: true});
            // Disable & uncheck semua sub-menu dan CRUD di dalamnya
            collapseEl.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.disabled = true;
                cb.checked  = false;
            });
            // Sembunyikan semua crud-row
            collapseEl.querySelectorAll('.crud-row').forEach(el => el.style.setProperty('display', 'none', 'important'));
            collapseEl.querySelectorAll('.submenu-block').forEach(el => el.classList.remove('submenu-active'));
        }
    }
}

// Toggle sub-menu ON/OFF
function toggleSubMenu(checkEl) {
    const blockId  = checkEl.dataset.block;
    const block    = document.getElementById('block_' + blockId);
    const crudRow  = document.getElementById('crud_' + blockId);

    if (checkEl.checked) {
        block.classList.add('submenu-active');
        if (crudRow) {
            crudRow.style.removeProperty('display');
            crudRow.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.disabled = false);
        }
    } else {
        block.classList.remove('submenu-active');
        if (crudRow) {
            crudRow.style.setProperty('display', 'none', 'important');
            crudRow.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.disabled = true;
                cb.checked  = false;
            });
        }
    }
}
</script>
<?= $this->endSection() ?>
