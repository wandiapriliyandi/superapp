<?php
// Ambil setting dari database
$db = \Config\Database::connect();
$setting = $db->table('app_settings')->get()->getRowArray();

// Fungsi helper untuk mengubah Hex ke RGB
function hexToRgb($hex) {
    $hex = str_replace("#", "", $hex);
    if(strlen($hex) == 3) {
        $r = hexdec(substr($hex,0,1).substr($hex,0,1));
        $g = hexdec(substr($hex,1,1).substr($hex,1,1));
        $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    } else {
        $r = hexdec(substr($hex,0,2));
        $g = hexdec(substr($hex,2,2));
        $b = hexdec(substr($hex,4,2));
    }
    return "$r, $g, $b";
}
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="<?= ($setting['theme_mode'] === 'midnight' ? 'dark' : $setting['theme_mode']) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Beranda' ?> | <?= $setting['app_name'] ?></title>
    
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    
    <style>
        :root {
            --primary-color: <?= $setting['theme_primary'] ?>;
            --primary-rgb: <?= hexToRgb($setting['theme_primary']) ?>;
            --secondary-color: <?= $setting['theme_secondary'] ?>;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 85px;
            --border-radius-custom: 16px;
            --transition-speed: 0.3s;
        }

        /* Penyesuaian Warna Berdasarkan Tema */
        [data-bs-theme="dark"] {
            --sidebar-bg: #1a1c1e;
            --topbar-bg: rgba(26, 28, 30, 0.8);
            --body-bg: #111214;
            --card-bg: #1e2023;
            --bs-border-color: rgba(255, 255, 255, 0.1);
        }

        /* Khusus Tema Midnight (Biru Tua) */
        <?php if($setting['theme_mode'] === 'midnight'): ?>
        [data-bs-theme="dark"] {
            --sidebar-bg: #0f172a;
            --topbar-bg: rgba(15, 23, 42, 0.8);
            --body-bg: #020617;
            --card-bg: #1e293b;
            --primary-color: #38bdf8;
            --bs-border-color: rgba(56, 189, 248, 0.1);
        }
        <?php endif; ?>

        [data-bs-theme="light"] {
            --sidebar-bg: #ffffff;
            --topbar-bg: rgba(255, 255, 255, 0.8);
            --body-bg: #f8f9fa;
            --card-bg: #ffffff;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--body-bg);
            color: var(--bs-body-color);
            transition: background-color var(--transition-speed), color var(--transition-speed);
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0; top: 0;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--bs-border-color);
            z-index: 1000;
            transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
            padding: 1.5rem 0;
            overflow-y: auto;
        }

        .sidebar.toggled { width: var(--sidebar-collapsed-width); }

        .sidebar .nav-link {
            padding: 0.8rem 1.5rem;
            margin: 0.2rem 1rem;
            border-radius: 12px;
            color: var(--bs-secondary-color);
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .sidebar.toggled .nav-link { justify-content: center; padding: 0.8rem 0; }

        .sidebar .nav-link:hover {
            background-color: rgba(var(--primary-rgb), 0.1);
            color: var(--primary-color);
            transform: translateX(5px);
        }

        .sidebar.toggled .nav-link:hover { transform: scale(1.1); }

        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(var(--primary-rgb), 0.3);
        }

        .sidebar .nav-icon { font-size: 1.25rem; min-width: 35px; display: flex; justify-content: center; }

        .sidebar.toggled .nav-text, .sidebar.toggled .sidebar-heading { display: none; }

        .sidebar-heading {
            padding: 1rem 2.5rem 0.5rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--bs-secondary-color);
            opacity: 0.6;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
        }

        .main-content.toggled { margin-left: var(--sidebar-collapsed-width); }

        /* Topbar (Glassmorphism) */
        .topbar {
            height: 70px;
            background-color: var(--topbar-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--bs-border-color);
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* UI Elements */
        .card {
            border-radius: var(--border-radius-custom);
            border: 1px solid var(--bs-border-color);
            background-color: var(--card-bg);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            border-radius: 12px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
        }

        .form-control, .form-select {
            border-radius: 10px;
            background-color: var(--bs-body-bg);
            border: 1px solid var(--bs-border-color);
            padding: 0.7rem 1rem;
            color: var(--bs-body-color);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(var(--primary-rgb), 0.15);
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
        }.text-primary { color: var(--primary-color) !important; }
        
        /* Animasi */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .main-content .container-fluid { animation: fadeIn 0.4s ease-out; }

        /* Print Styling */
        @media print {
            .sidebar, .topbar, nav[aria-label="breadcrumb"], footer, .no-print, #sidebarToggle, .dropdown {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            .card {
                box-shadow: none !important;
                border: 1px solid #eee !important;
                break-inside: avoid;
            }
            body {
                background-color: white !important;
            }
        }
    </style>
</head>
<body>

    <?php if (!isset($hide_sidebar) || !$hide_sidebar): ?>
    <div class="sidebar" id="sidebar">
        <div class="px-4 mb-3 text-center border-bottom pb-3">
            <a href="<?= base_url() ?>" class="text-decoration-none">
                <?php 
                    if($setting && $setting['app_logo'] && file_exists(FCPATH . 'uploads/img/' . $setting['app_logo'])) {
                        $logo = base_url('uploads/img/' . $setting['app_logo']);
                        echo '<img src="'.$logo.'" class="img-fluid rounded mb-2 mt-3" style="max-height: 50px;" alt="Logo">';
                    }
                ?>
                <h5 class="fw-bold text-primary mb-0 mt-2"><?= $setting['app_name'] ?? 'SuperApp' ?></h5>
                <div class="small text-muted opacity-75" style="font-size: 10px;"><?= $setting['pesantren_name'] ?? 'Pesantren Digital' ?></div>
            </a>
        </div>
        
        <a class="nav-link <?= url_is('/') ? 'active' : '' ?>" href="<?= base_url() ?>">
            <span class="nav-icon">🏠</span> <span class="nav-text">Beranda Utama</span>
        </a>

        <!-- MENU PPDB -->
        <?php if (url_is('ppdb*')): ?>
            <div class="sidebar-heading text-primary fw-bold">MODUL PPDB</div>
            <a class="nav-link <?= url_is('ppdb/dashboard*') ? 'active' : '' ?>" href="<?= base_url('ppdb/dashboard') ?>">
                <span class="nav-icon">📊</span> <span class="nav-text">Statistik</span>
            </a>
            <a class="nav-link <?= url_is('ppdb/pendaftar/add*') ? 'active' : '' ?>" href="<?= base_url('ppdb/pendaftar/add') ?>">
                <span class="nav-icon">➕</span> <span class="nav-text">Tambah Pendaftar</span>
            </a>
            <a class="nav-link <?= url_is('ppdb/pendaftar') && !url_is('ppdb/pendaftar/add*') ? 'active' : '' ?>" href="<?= base_url('ppdb/pendaftar') ?>">
                <span class="nav-icon">📝</span> <span class="nav-text">Daftar Pendaftar</span>
            </a>
            <a class="nav-link <?= url_is('ppdb/jadwal*') ? 'active' : '' ?>" href="<?= base_url('ppdb/jadwal') ?>">
                <span class="nav-icon">📅</span> <span class="nav-text">Jadwal Tes</span>
            </a>
            <a class="nav-link <?= url_is('ppdb/berkas/verifikasi*') ? 'active' : '' ?>" href="<?= base_url('ppdb/berkas/verifikasi') ?>">
                <span class="nav-icon">✅</span> <span class="nav-text">Verifikasi Berkas</span>
            </a>
            <a class="nav-link <?= url_is('ppdb/berkas') && !url_is('ppdb/berkas/verifikasi*') ? 'active' : '' ?>" href="<?= base_url('ppdb/berkas') ?>">
                <span class="nav-icon">📋</span> <span class="nav-text">Syarat Berkas</span>
            </a>
            <a class="nav-link <?= url_is('ppdb/pengaturan*') ? 'active' : '' ?>" href="<?= base_url('ppdb/pengaturan') ?>">
                <span class="nav-icon">⚙️</span> <span class="nav-text">Gelombang</span>
            </a>
            <a class="nav-link <?= url_is('ppdb/workflow*') ? 'active' : '' ?>" href="<?= base_url('ppdb/workflow') ?>">
                <span class="nav-icon">📖</span> <span class="nav-text">Petunjuk PPDB</span>
            </a>

        <!-- MENU AKADEMIK -->
        <?php elseif (url_is('akademik*')): ?>
            <div class="sidebar-heading text-success fw-bold">MODUL AKADEMIK</div>
            <a class="nav-link <?= url_is('akademik/santri*') ? 'active' : '' ?>" href="<?= base_url('akademik/santri') ?>">
                <span class="nav-icon">🎓</span> <span class="nav-text">Data Santri</span>
            </a>
            <a class="nav-link <?= url_is('akademik/tahun-ajaran*') ? 'active' : '' ?>" href="<?= base_url('akademik/tahun-ajaran') ?>">
                <span class="nav-icon">📅</span> <span class="nav-text">Tahun Ajaran</span>
            </a>

        <!-- MENU KEUANGAN -->
        <?php elseif (url_is('keuangan*')): ?>
            <div class="sidebar-heading text-warning fw-bold">MANAJEMEN KEUANGAN</div>
            <a class="nav-link <?= url_is('keuangan') || url_is('keuangan/dashboard*') ? 'active' : '' ?>" href="<?= base_url('keuangan') ?>">
                <span class="nav-icon">📊</span> <span class="nav-text">Dashboard</span>
            </a>
            <a class="nav-link <?= url_is('keuangan/workflow*') ? 'active' : '' ?>" href="<?= base_url('keuangan/workflow') ?>">
                <span class="nav-icon">📖</span> <span class="nav-text">Petunjuk SPP</span>
            </a>
            <a class="nav-link <?= (url_is('keuangan/pembayaran/cari*') || url_is('keuangan/pembayaran/bayar*')) ? 'active' : '' ?>" href="<?= base_url('keuangan/pembayaran/cari') ?>">
                <span class="nav-icon">🏧</span> <span class="nav-text">Bayar SPP</span>
            </a>
            <a class="nav-link <?= url_is('keuangan/pembayaran/transaksi*') ? 'active' : '' ?>" href="<?= base_url('keuangan/pembayaran/transaksi') ?>">
                <span class="nav-icon">✂️</span> <span class="nav-text">Riwayat Transaksi</span>
            </a>
            <a class="nav-link <?= url_is('keuangan/tarif*') ? 'active' : '' ?>" href="<?= base_url('keuangan/tarif') ?>">
                <span class="nav-icon">🏷️</span> <span class="nav-text">Tarif SPP</span>
            </a>
            <a class="nav-link <?= url_is('keuangan/tagihan*') ? 'active' : '' ?>" href="<?= base_url('keuangan/tagihan') ?>">
                <span class="nav-icon">📄</span> <span class="nav-text">Data Tagihan</span>
            </a>
            <a class="nav-link <?= url_is('keuangan/mapping*') ? 'active' : '' ?>" href="<?= base_url('keuangan/mapping') ?>">
                <span class="nav-icon">🤝</span> <span class="nav-text">Pemetaan Tarif</span>
            </a>
            <a class="nav-link <?= url_is('keuangan/pembayaran*') && !url_is('keuangan/pembayaran/cari*') && !url_is('keuangan/pembayaran/bayar*') && !url_is('keuangan/pembayaran/transaksi*') ? 'active' : '' ?>" href="<?= base_url('keuangan/pembayaran') ?>">
                <span class="nav-icon">💸</span> <span class="nav-text">Riwayat Bayar</span>
            </a>

        <!-- MENU KEPEGAWAIAN -->
        <?php elseif (url_is('kepegawaian*')): ?>
            <div class="sidebar-heading text-info fw-bold">KEPEGAWAIAN</div>
            <a class="nav-link <?= url_is('kepegawaian/karyawan*') ? 'active' : '' ?>" href="<?= base_url('kepegawaian/karyawan') ?>">
                <span class="nav-icon">💎</span> <span class="nav-text">Data Pegawai</span>
            </a>

        <!-- MENU E-LEARNING -->
        <?php elseif (url_is('e-learning*')): ?>
            <div class="sidebar-heading fw-bold" style="color: #6610f2;">E-LEARNING</div>
            <a class="nav-link <?= url_is('e-learning/materi*') ? 'active' : '' ?>" href="<?= base_url('e-learning/materi') ?>">
                <span class="nav-icon">📚</span> <span class="nav-text">Materi Belajar</span>
            </a>
            <a class="nav-link <?= url_is('e-learning/ujian*') ? 'active' : '' ?>" href="<?= base_url('e-learning/ujian') ?>">
                <span class="nav-icon">📝</span> <span class="nav-text">Ujian Online</span>
            </a>
            <a class="nav-link <?= url_is('e-learning/skill*') ? 'active' : '' ?>" href="<?= base_url('e-learning/skill') ?>">
                <span class="nav-icon">🌟</span> <span class="nav-text">Skill & TOEFL</span>
            </a>

        <!-- MENU SYSTEM / DEFAULT -->
        <?php else: ?>
            <div class="sidebar-heading text-secondary fw-bold">SYSTEM ADMIN</div>
            <a class="nav-link <?= url_is('activity*') ? 'active' : '' ?>" href="<?= base_url('activity') ?>">
                <span class="nav-icon">📜</span> <span class="nav-text">Log Aktivitas</span>
            </a>
            <a class="nav-link <?= url_is('setting') && !url_is('setting/theme') ? 'active' : '' ?>" href="<?= base_url('setting') ?>">
                <span class="nav-icon">⚙️</span> <span class="nav-text">Profil Pesantren</span>
            </a>
            <a class="nav-link <?= url_is('setting/theme') ? 'active' : '' ?>" href="<?= base_url('setting/theme') ?>">
                <span class="nav-icon">🎨</span> <span class="nav-text">Tema Aplikasi</span>
            </a>
            <a class="nav-link <?= url_is('migrate*') ? 'active' : '' ?>" href="<?= base_url('migrate') ?>">
                <span class="nav-icon">⚡</span> <span class="nav-text">Migrasi & Database</span>
            </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="main-content" id="main-content" style="<?= (isset($hide_sidebar) && $hide_sidebar) ? 'margin-left: 0; width: 100%;' : '' ?>">
        <div class="topbar">
            <div class="d-flex align-items-center">
                <?php if (!isset($hide_sidebar) || !$hide_sidebar): ?>
                <button class="btn btn-link text-body me-3 p-0" id="sidebarToggle">
                    <i class="bi bi-list fs-3"></i>
                </button>
                <?php endif; ?>
                <h5 class="mb-0 fw-bold"><?= $title ?? 'Beranda' ?></h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <button class="btn btn-link text-body text-decoration-none dropdown-toggle p-0" type="button" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name=Admin&background=random" class="rounded-circle me-2" width="35" height="35">
                        <span class="fw-semibold">Administrator</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Profil</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('setting') ?>"><i class="bi bi-gear me-2"></i> Pengaturan</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i> Keluar</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="container-fluid p-4">
            <!-- Flash Message -->
            <?php if(session()->getFlashdata('success')): ?>
                <div class="alert alert-success border-0 shadow-sm mb-4"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger border-0 shadow-sm mb-4"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb bg-white p-3 rounded-4 shadow-sm">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none">Beranda</a></li>
                    <?= $this->renderSection('breadcrumb') ?>
                    <li class="breadcrumb-item active" aria-current="page"><?= $title ?? 'Dashboard' ?></li>
                </ol>
            </nav>

            <?= $this->renderSection('content') ?>

            <!-- Footer Aplikasi -->
            <footer class="mt-4 pb-3">
                <div class="border-top pt-3 text-center text-muted small">
                    <span class="me-2">&copy; <?= date('Y') ?> <strong><?= $setting['pesantren_name'] ?? 'Pesantren Modern' ?></strong>.</span>
                    <span class="mx-2 text-opacity-25">|</span>
                    <span class="mx-2">Developed by <span class="fw-bold text-dark">Wandi Apriliyandi</span></span>
                    <span class="mx-2 text-opacity-25">|</span>
                    <?php 
                        $aa = date('Y') - 2026 + 1; // Tahun ke-berapa sejak 2026
                        $bb = date('m');
                        $cccc = str_pad(date('z') + 1, 4, '0', STR_PAD_LEFT);
                        echo "<span class='ms-2'>Version $aa.$bb.$cccc</span>";
                    ?>
                </div>
            </footer>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Global -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-body text-center p-5">
                    <div class="text-danger mb-4" style="font-size: 5rem;">
                        <i class="bi bi-exclamation-octagon-fill"></i>
                    </div>
                    <h3 class="fw-bold mb-3">Yakin ingin menghapus?</h3>
                    <p class="text-muted mb-4">Data yang sudah dihapus tidak dapat dikembalikan lagi. Apakah Anda benar-benar yakin?</p>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-light px-4 py-2 fw-semibold" data-bs-dismiss="modal" style="border-radius: 12px;">Batal</button>
                        <a href="#" id="confirmDeleteBtn" class="btn btn-danger px-4 py-2 fw-semibold" style="border-radius: 12px;">Ya, Hapus Data</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Status (Lulus/Gagal) -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-body text-center p-5">
                    <div id="statusIcon" class="mb-4" style="font-size: 5rem;">
                        <i class="bi bi-question-circle-fill text-primary"></i>
                    </div>
                    <h3 class="fw-bold mb-3" id="statusTitle">Konfirmasi Aksi</h3>
                    <p class="text-muted mb-4" id="statusMessage">Apakah Anda yakin ingin melanjutkan aksi ini?</p>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-light px-4 py-2 fw-semibold" data-bs-dismiss="modal" style="border-radius: 12px;">Batal</button>
                        <a href="#" id="confirmStatusBtn" class="btn btn-primary px-4 py-2 fw-semibold" style="border-radius: 12px;">Ya, Lanjutkan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <?= $this->renderSection('scripts') ?>

    <script>
        // Modal Konfirmasi Status Dinamis
        const statusModal = document.getElementById('statusModal');
        if (statusModal) {
            statusModal.addEventListener('show.bs.modal', function (event) {
                const btn = event.relatedTarget;
                const url = btn.getAttribute('data-href');
                const title = btn.getAttribute('data-title');
                const msg = btn.getAttribute('data-message');
                const color = btn.getAttribute('data-color');
                const icon = btn.getAttribute('data-icon');

                document.getElementById('statusTitle').innerText = title;
                document.getElementById('statusMessage').innerText = msg;
                document.getElementById('confirmStatusBtn').setAttribute('href', url);
                document.getElementById('confirmStatusBtn').className = `btn btn-${color} px-4 py-2 fw-semibold`;
                document.getElementById('statusIcon').innerHTML = `<i class="bi ${icon} text-${color}"></i>`;
            });
        }

        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const sidebarToggle = document.getElementById('sidebarToggle');

        // Restore sidebar state
        if (localStorage.getItem('sidebar-toggled') === 'true') {
            sidebar.classList.add('toggled');
            mainContent.classList.add('toggled');
        }

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('toggled');
            mainContent.classList.toggle('toggled');
            localStorage.setItem('sidebar-toggled', sidebar.classList.contains('toggled'));
        });

        // Script untuk Modal Hapus Global
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                // Tombol yang memicu modal
                const button = event.relatedTarget;
                // Ambil URL hapus dari atribut data-href
                const href = button.getAttribute('data-href');
                // Update link di tombol konfirmasi modal
                const confirmBtn = document.getElementById('confirmDeleteBtn');
                confirmBtn.setAttribute('href', href);
            });
        }
    </script>
</body>
</html>
