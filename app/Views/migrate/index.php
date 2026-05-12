<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <!-- Header Page dengan Desain Premium -->
    <div class="col-12">
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
            <div class="card-body p-5 position-relative bg-gradient" style="background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.1) 0%, rgba(var(--primary-rgb), 0.02) 100%);">
                <div class="position-absolute top-0 end-0 p-4 opacity-10 d-none d-md-block" style="font-size: 8rem; line-height: 1; transform: rotate(-15deg); pointer-events: none;">
                    <i class="bi bi-database-gear"></i>
                </div>
                <div class="max-w-75 position-relative z-1">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold mb-3 border border-primary border-opacity-25">
                        <i class="bi bi-shield-fill-exclamation me-1"></i> Alat Administrator
                    </span>
                    <h2 class="fw-bold mb-2">Migrasi Paksa & Kelola Skema Database</h2>
                    <p class="text-muted mb-0" style="font-size: 1.1rem;">
                        Panel khusus untuk mengeksekusi, menyegarkan, atau menyetel ulang skema migrasi database secara langsung dari antarmuka web. Sangat berguna untuk lingkungan hosting tanpa akses terminal (SSH/CLI).
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pemberitahuan Hasil Migrasi Otomatis Saat Halaman Dibuka -->
    <?php if (!empty($auto_migrate_status)): ?>
    <div class="col-12">
        <div class="alert <?= $auto_migrate_status === 'success' ? 'alert-success bg-success bg-opacity-10 border-success' : 'alert-danger bg-danger bg-opacity-10 border-danger' ?> border border-opacity-25 p-4 shadow-sm d-flex align-items-center" style="border-radius: 16px;">
            <div class="fs-1 me-4 <?= $auto_migrate_status === 'success' ? 'text-success' : 'text-danger' ?>">
                <i class="bi <?= $auto_migrate_status === 'success' ? 'bi-check2-circle' : 'bi-exclamation-triangle-fill' ?>"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-1 <?= $auto_migrate_status === 'success' ? 'text-success' : 'text-danger' ?>">
                    <?= $auto_migrate_status === 'success' ? 'Eksekusi Migrasi Otomatis Berhasil!' : 'Eksekusi Migrasi Otomatis Mendapati Informasi' ?>
                </h5>
                <p class="mb-0 text-body-secondary">
                    <?= esc($auto_migrate_message) ?>
                </p>
                <hr class="opacity-10 my-2">
                <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Sistem dirancang untuk otomatis memverifikasi dan menjalankan skema terbaru saat halaman ini diakses untuk pertama kalinya.</small>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Status Koneksi & Informasi Singkat -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4 flex-grow-1 border-bottom pb-3">
                    <i class="bi bi-hdd-network-fill text-primary me-2"></i> Status Database
                </h5>

                <div class="d-flex align-items-center mb-4 p-3 rounded-4 bg-body-tertiary border">
                    <div class="fs-1 me-3 <?= $db_connected ? 'text-success' : 'text-danger' ?>">
                        <i class="bi <?= $db_connected ? 'bi-check-circle-fill' : 'bi-x-circle-fill' ?>"></i>
                    </div>
                    <div>
                        <div class="small text-muted fw-bold text-uppercase">Koneksi Database</div>
                        <div class="fs-5 fw-bold <?= $db_connected ? 'text-success' : 'text-danger' ?>">
                            <?= $db_connected ? 'Terhubung' : 'Gagal Terhubung' ?>
                        </div>
                    </div>
                </div>

                <?php if (!$db_connected && !empty($db_error)): ?>
                    <div class="alert alert-danger small border-0 rounded-3 mb-4">
                        <strong>Pesan Error:</strong><br><?= esc($db_error) ?>
                    </div>
                <?php endif; ?>

                <div class="d-flex align-items-center mb-4 p-3 rounded-4 bg-body-tertiary border">
                    <div class="fs-1 me-3 <?= $has_migrations_table ? 'text-primary' : 'text-warning' ?>">
                        <i class="bi <?= $has_migrations_table ? 'bi-table' : 'bi-exclamation-triangle-fill' ?>"></i>
                    </div>
                    <div>
                        <div class="small text-muted fw-bold text-uppercase">Tabel Riwayat Migrasi</div>
                        <div class="fs-5 fw-bold <?= $has_migrations_table ? 'text-body' : 'text-warning' ?>">
                            <?= $has_migrations_table ? 'Tersedia' : 'Belum Dibuat' ?>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-primary bg-opacity-10 rounded-4 border border-primary border-opacity-25 text-primary small">
                    <i class="bi bi-info-circle-fill me-1"></i> <strong>Catatan:</strong> Jika tabel migrasi belum dibuat, menjalankan <strong>Migrasi Terbaru</strong> akan secara otomatis membuat tabel <code>migrations</code> dan mengeksekusi semua skema.
                </div>
            </div>
        </div>
    </div>

    <!-- Opsi Aksi Migrasi Paksa -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4 border-bottom pb-3">
                    <i class="bi bi-lightning-charge-fill text-warning me-2"></i> Pilihan Tindakan Migrasi
                </h5>

                <div class="row g-3">
                    <!-- Action 1: Latest -->
                    <div class="col-md-6">
                        <div class="card h-100 border-primary border-opacity-25 bg-primary bg-opacity-10 hover-elevate transition-all" style="border-radius: 16px;">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="fw-bold text-primary mb-0"><i class="bi bi-play-fill fs-5 me-1"></i> Jalankan Migrasi Terbaru</h6>
                                        <span class="badge bg-primary">Aman</span>
                                    </div>
                                    <p class="small text-muted mb-4">
                                        Mengeksekusi semua file skema migrasi baru yang belum pernah dijalankan sebelumnya. Data lama tidak akan terhapus.
                                    </p>
                                </div>
                                <button type="button" class="btn btn-primary w-100 fw-bold shadow-sm" style="border-radius: 12px;"
                                        data-bs-toggle="modal" data-bs-target="#statusModal"
                                        data-href="<?= base_url('migrate/latest') ?>"
                                        data-title="Jalankan Migrasi Terbaru"
                                        data-message="Apakah Anda yakin ingin mengeksekusi semua skema migrasi terbaru ke dalam database?"
                                        data-color="primary"
                                        data-icon="bi-play-circle-fill">
                                    <i class="bi bi-caret-right-fill me-1"></i> Eksekusi (Latest)
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Action 2: Rollback -->
                    <div class="col-md-6">
                        <div class="card h-100 border border-opacity-25 bg-body-tertiary hover-elevate transition-all" style="border-radius: 16px;">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="fw-bold mb-0"><i class="bi bi-arrow-counterclockwise fs-5 me-1"></i> Batalkan Terakhir (Rollback)</h6>
                                        <span class="badge bg-secondary">Regresi</span>
                                    </div>
                                    <p class="small text-muted mb-4">
                                        Membatalkan (rollback) satu batch eksekusi migrasi terakhir. Tabel atau kolom dari batch terakhir akan dihapus.
                                    </p>
                                </div>
                                <button type="button" class="btn btn-outline-secondary w-100 fw-bold" style="border-radius: 12px;"
                                        data-bs-toggle="modal" data-bs-target="#statusModal"
                                        data-href="<?= base_url('migrate/rollback') ?>"
                                        data-title="Rollback Migrasi Terakhir"
                                        data-message="Perhatian: Melakukan rollback akan membatalkan batch migrasi terakhir dan dapat menghapus tabel/kolom terkait. Yakin melanjutkan?"
                                        data-color="warning"
                                        data-icon="bi-arrow-counterclockwise">
                                    <i class="bi bi-skip-backward-fill me-1"></i> Rollback 1 Batch
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Action 3: Force Reset Table -->
                    <div class="col-md-6">
                        <div class="card h-100 border-warning border-opacity-25 bg-warning bg-opacity-10 hover-elevate transition-all" style="border-radius: 16px;">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="fw-bold text-warning-emphasis mb-0"><i class="bi bi-trash3-fill fs-5 me-1"></i> Hapus Riwayat Migrasi</h6>
                                        <span class="badge bg-warning text-dark">Penyinkron</span>
                                    </div>
                                    <p class="small text-muted mb-4">
                                        Menghapus tabel riwayat <code>migrations</code> tanpa menyentuh tabel data. Gunakan jika status migrasi macet/korup di hosting.
                                    </p>
                                </div>
                                <button type="button" class="btn btn-warning w-100 fw-bold text-dark shadow-sm" style="border-radius: 12px;"
                                        data-bs-toggle="modal" data-bs-target="#statusModal"
                                        data-href="<?= base_url('migrate/force-reset') ?>"
                                        data-title="Hapus Paksa Tabel Riwayat Migrasi"
                                        data-message="Tindakan ini akan me-drop tabel 'migrations' untuk menyetel ulang riwayat pelacakan skema tanpa menghapus isi data tabel utama. Yakin?"
                                        data-color="warning"
                                        data-icon="bi-exclamation-triangle-fill">
                                    <i class="bi bi-eraser-fill me-1"></i> Hapus Tabel Riwayat
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Action 4: Refresh All -->
                    <div class="col-md-6">
                        <div class="card h-100 border-danger border-opacity-25 bg-danger bg-opacity-10 hover-elevate transition-all" style="border-radius: 16px;">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="fw-bold text-danger mb-0"><i class="bi bi-arrow-repeat fs-5 me-1"></i> Segarkan Ulang (Refresh)</h6>
                                        <span class="badge bg-danger">Kritis</span>
                                    </div>
                                    <p class="small text-muted mb-4">
                                        Melakukan regresi ke titik nol (menghapus seluruh tabel skema) lalu menjalankan ulang migrasi dari awal. <strong>Semua data akan hilang!</strong>
                                    </p>
                                </div>
                                <button type="button" class="btn btn-danger w-100 fw-bold shadow-sm" style="border-radius: 12px;"
                                        data-bs-toggle="modal" data-bs-target="#statusModal"
                                        data-href="<?= base_url('migrate/refresh') ?>"
                                        data-title="Segarkan Ulang Seluruh Database"
                                        data-message="PERINGATAN KRITIS: Seluruh tabel yang dibuat melalui migrasi akan di-drop dan dibuat ulang dari awal. Seluruh data akan terhapus permanen! Apakah Anda sangat yakin?"
                                        data-color="danger"
                                        data-icon="bi-exclamation-octagon-fill">
                                    <i class="bi bi-bootstrap-reboot me-1"></i> Refresh Total (Reset)
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Action 5: Git Pull -->
                    <div class="col-12 pt-2">
                        <div class="card border-info border-opacity-25 bg-info bg-opacity-10 hover-elevate transition-all" style="border-radius: 16px;">
                            <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                <div class="max-w-75">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h6 class="fw-bold text-info-emphasis mb-0"><i class="bi bi-cloud-arrow-down-fill fs-5 me-1"></i> Tarik Pembaruan Repositori (Git Pull)</h6>
                                        <span class="badge bg-info text-dark">Sinkronisasi</span>
                                    </div>
                                    <p class="small text-muted mb-0">
                                        Mengambil file skema migrasi atau kode aplikasi terbaru dari server repositori Git (remote <code>origin main</code>) untuk memastikan file migrasi baru terunduh.
                                    </p>
                                </div>
                                <button type="button" class="btn btn-info fw-bold text-dark px-4 py-2 shadow-sm text-nowrap" style="border-radius: 12px;"
                                        data-bs-toggle="modal" data-bs-target="#statusModal"
                                        data-href="<?= base_url('migrate/pull-git') ?>"
                                        data-title="Tarik Pembaruan Git (Pull)"
                                        data-message="Sistem akan menjalankan perintah 'git pull origin main' di latar belakang untuk mengunduh kode/skema terbaru dari GitHub. Lanjutkan?"
                                        data-color="info"
                                        data-icon="bi-cloud-arrow-down-fill">
                                    <i class="bi bi-git me-1"></i> Eksekusi Git Pull
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Daftar File Skema Migrasi (AJAX Powered) -->
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom pb-3 mb-4 gap-3">
                    <div>
                        <h5 class="fw-bold mb-1 d-flex align-items-center">
                            <i class="bi bi-file-earmark-code-fill text-primary me-2"></i> Daftar File Skema Migrasi
                            <span class="badge bg-primary bg-opacity-10 text-primary ms-2 small" style="font-size: 0.7rem;">Live AJAX</span>
                        </h5>
                        <p class="small text-muted mb-0">File-file skema PHP yang ditemukan di dalam direktori <code>app/Database/Migrations/</code> dimuat secara asinkron</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" id="refreshAjaxBtn" class="btn btn-sm btn-outline-primary fw-bold rounded-pill px-3 shadow-sm d-flex align-items-center">
                            <i class="bi bi-arrow-clockwise me-1" id="refreshSpinnerIcon"></i> Muat Ulang Data
                        </button>
                        <span id="totalFilesBadge" class="badge bg-body-tertiary text-body border px-3 py-2 rounded-pill fw-semibold">
                            Total: memuat...
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 rounded-start-3" style="width: 5%;">No</th>
                                <th class="border-0" style="width: 45%;">Nama File Migrasi</th>
                                <th class="border-0" style="width: 15%;">Status</th>
                                <th class="border-0" style="width: 15%;">Batch</th>
                                <th class="border-0 rounded-end-3" style="width: 20%;">Waktu Dieksekusi</th>
                            </tr>
                        </thead>
                        <tbody id="migrationTableBody">
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="spinner-border text-primary mb-2" role="status">
                                        <span class="visually-hidden">Memuat...</span>
                                    </div>
                                    <div class="text-muted small fw-semibold">Mengambil data skema migrasi secara real-time via AJAX...</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tampilan Riwayat Mentah Database -->
    <?php if ($has_migrations_table && !empty($history)): ?>
    <div class="col-12">
        <div class="accordion shadow-sm" id="accordionHistory" style="border-radius: 20px; overflow: hidden;">
            <div class="accordion-item border-0">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold bg-body-tertiary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHistory">
                        <i class="bi bi-clock-history text-secondary me-2"></i> Data Riwayat Tabel Migrations di Database
                    </button>
                </h2>
                <div id="collapseHistory" class="accordion-collapse collapse" data-bs-parent="#accordionHistory">
                    <div class="accordion-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered m-0 small font-monospace">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Version</th>
                                        <th>Class</th>
                                        <th>Group</th>
                                        <th>Namespace</th>
                                        <th>Time</th>
                                        <th>Batch</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($history as $h): ?>
                                        <tr>
                                            <td><?= esc($h['id'] ?? '-') ?></td>
                                            <td class="text-primary"><?= esc($h['version'] ?? '-') ?></td>
                                            <td><?= esc($h['class'] ?? '-') ?></td>
                                            <td><?= esc($h['group'] ?? '-') ?></td>
                                            <td><?= esc($h['namespace'] ?? '-') ?></td>
                                            <td><?= isset($h['time']) ? date('Y-m-d H:i:s', $h['time']) : '-' ?></td>
                                            <td class="text-center fw-bold"><?= esc($h['batch'] ?? '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Fungsi memuat tabel migrasi menggunakan AJAX yang kebal terhadap injeksi naskah hosting
    function loadMigrationTable() {
        const spinnerIcon = $('#refreshSpinnerIcon');
        spinnerIcon.addClass('spinner-border spinner-border-sm').removeClass('bi-arrow-clockwise');
        
        // Bentuk URL secara dinamis dari alamat browser aktif agar terhindar dari kendala base_url localhost di server hosting live
        let currentUrl = window.location.href.split('#')[0].split('?')[0].replace(/\/$/, '');
        const targetAjaxUrl = currentUrl + '/table-data';

        // Tulis ulang atribut data-href pada seluruh tombol aksi agar selalu menggunakan host/path yang sesuai di server live
        $('.btn[data-href]').each(function() {
            let oldHref = $(this).attr('data-href');
            let routePart = oldHref.substring(oldHref.lastIndexOf('/') + 1);
            $(this).attr('data-href', currentUrl + '/' + routePart);
        });

        $.ajax({
            url: targetAjaxUrl,
            type: 'GET',
            dataType: 'text', // Menggunakan text untuk mencegah error parse otomatis jika server menyisipkan kode HTML/JS tambahan
            success: function(rawText) {
                try {
                    // Ekstrak blok JSON murni dari respon (mencari tanda kurung kurawal pertama dan terakhir)
                    const startIdx = rawText.indexOf('{');
                    const endIdx = rawText.lastIndexOf('}');
                    
                    if (startIdx !== -1 && endIdx !== -1) {
                        const pureJsonStr = rawText.substring(startIdx, endIdx + 1);
                        const response = JSON.parse(pureJsonStr);
                        
                        if (response.status === 'success') {
                            $('#totalFilesBadge').html(`Total: ${response.total_files} File Skema`);
                            const tbody = $('#migrationTableBody');
                            tbody.empty();
                            
                            if (response.migration_files.length === 0) {
                                tbody.append(`
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2 text-opacity-50"></i>
                                            Tidak ada file migrasi ditemukan di direktori.
                                        </td>
                                    </tr>
                                `);
                            } else {
                                let no = 1;
                                response.migration_files.forEach(function(item) {
                                    let statusBadge = item.is_executed ? 
                                        `<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">
                                            <i class="bi bi-check-circle-fill me-1"></i> Selesai
                                        </span>` : 
                                        `<span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-25 px-2 py-1 rounded-pill">
                                            <i class="bi bi-clock-fill me-1"></i> Belum Dieksekusi
                                        </span>`;
                                        
                                    let rowHtml = `
                                        <tr style="animation: fadeIn 0.4s ease-out;">
                                            <td class="fw-bold text-muted">${no++}</td>
                                            <td>
                                                <div class="fw-bold text-primary font-monospace small">
                                                    <i class="bi bi-filetype-php me-1 text-secondary"></i>${item.file}
                                                </div>
                                                <div class="text-muted small" style="font-size: 0.75rem;">Class/Versi: ${item.name}</div>
                                            </td>
                                            <td>${statusBadge}</td>
                                            <td>
                                                <span class="badge bg-body-secondary text-body fw-bold">
                                                    ${item.batch}
                                                </span>
                                            </td>
                                            <td class="text-muted small">${item.executed_time}</td>
                                        </tr>
                                    `;
                                    tbody.append(rowHtml);
                                });
                            }
                        }
                    } else {
                        throw new Error("Pola JSON tidak ditemukan dalam respon teks server.");
                    }
                } catch (err) {
                    console.error("Gagal mengurai JSON AJAX:", err, rawText);
                    $('#migrationTableBody').html(`
                        <tr>
                            <td colspan="5" class="text-center py-4 text-danger">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Gagal memproses data JSON dari server. (Respon terhalang oleh naskah hosting/keamanan).
                            </td>
                        </tr>
                    `);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                $('#migrationTableBody').html(`
                    <tr>
                        <td colspan="5" class="text-center py-4 text-danger">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Koneksi ke server untuk mengambil data riwayat terputus. Silakan coba muat ulang.
                        </td>
                    </tr>
                `);
            },
            complete: function() {
                setTimeout(() => {
                    spinnerIcon.removeClass('spinner-border spinner-border-sm').addClass('bi-arrow-clockwise');
                }, 300);
            }
        });
    }

    // Eksekusi otomatis saat halaman selesai dimuat
    loadMigrationTable();

    // Event tombol muat ulang data manual
    $('#refreshAjaxBtn').on('click', function() {
        loadMigrationTable();
    });
});
</script>
<?= $this->endSection() ?>
