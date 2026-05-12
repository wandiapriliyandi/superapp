<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row g-3">
    <div class="col-12 mb-2">
        <h3 class="fw-bold">SuperApp Pesantren Dashboard 👋</h3>
        <p class="text-muted">Pusat kendali manajemen pesantren terintegrasi.</p>
    </div>

    <!-- 1. PPDB -->
    <?= dashboard_card('PPDB', 'Pendaftaran Santri Baru', 'bi-person-plus', 'bg-primary', base_url('ppdb/pendaftar')) ?>

    <!-- 2. Siakad -->
    <?= dashboard_card('SIAKAD', 'Data Santri & Akademik', 'bi-mortarboard', 'bg-success', base_url('akademik/santri')) ?>

    <!-- 3. HR (Kepegawaian) -->
    <?= dashboard_card('Kepegawaian', 'Data Guru & Karyawan', 'bi-person-workspace', 'bg-info', base_url('kepegawaian/karyawan')) ?>

    <!-- 4. Kedisiplinan -->
    <?= dashboard_card('Kedisiplinan', 'Poin Pelanggaran & Prestasi', 'bi-shield-check', 'bg-danger', '#', 'Coming Soon') ?>

    <!-- 5. Perizinan -->
    <?= dashboard_card('Perizinan', 'Izin Pulang & Keluar', 'bi-card-checklist', 'bg-warning text-dark', '#', 'Coming Soon') ?>

    <!-- 6. Keasramaan -->
    <?= dashboard_card('Keasramaan', 'Manajemen Kamar & Asrama', 'bi-houses', 'bg-secondary', '#', 'Coming Soon') ?>

    <!-- 7. Kurikulum Spesialis -->
    <?= dashboard_card('Tahfidz/Diniyah', 'Progres Hafalan & Kitab', 'bi-book', 'bg-teal', '#', 'Coming Soon') ?>

    <!-- 8. E-Learning -->
    <?= dashboard_card('E-Learning', 'Materi & Ujian Online', 'bi-laptop', 'bg-indigo', base_url('e-learning/materi')) ?>

    <!-- 9. Keuangan -->
    <?= dashboard_card('Keuangan', 'SPP & Tagihan Santri', 'bi-wallet2', 'bg-success bg-gradient', '#', 'Coming Soon') ?>

    <!-- 9. OSIS -->
    <?= dashboard_card('OSIS', 'Kegiatan Santri/Siswa', 'bi-people', 'bg-primary bg-gradient', '#', 'Coming Soon') ?>

    <!-- 10. Perpustakaan -->
    <?= dashboard_card('Perpustakaan', 'Katalog & Peminjaman Buku', 'bi-journal-bookmark', 'bg-info bg-gradient', '#', 'Coming Soon') ?>

    <!-- 11. Inventory -->
    <?= dashboard_card('Inventory', 'Sarana & Infrastruktur', 'bi-box-seam', 'bg-secondary bg-gradient', '#', 'Coming Soon') ?>

    <!-- 12. Poskestren -->
    <?= dashboard_card('Poskestren', 'Kesehatan & Rekam Medis', 'bi-heart-pulse', 'bg-danger bg-gradient', '#', 'Coming Soon') ?>

    <!-- 13. Portal Wali -->
    <?= dashboard_card('Portal Wali', 'Akses Orang Tua Santri', 'bi-phone-vibrate', 'bg-dark', '#', 'Coming Soon') ?>

    <!-- Additional: Activity Log -->
    <?= dashboard_card('Log Aktivitas', 'Riwayat Audit Sistem', 'bi-clock-history', 'bg-light text-dark border', base_url('activity-log')) ?>
</div>

<?php
// Helper lokal untuk merender kartu dashboard agar rapi
function dashboard_card($title, $desc, $icon, $bg, $link, $status = '') {
    $opacity = $status ? 'opacity-50' : '';
    $badge = $status ? "<span class='badge bg-white text-dark position-absolute top-0 end-0 m-3 small'>$status</span>" : "";
    return "
    <div class='col-md-4 col-lg-3'>
        <a href='$link' class='text-decoration-none $opacity'>
            <div class='card border-0 shadow-sm h-100 rounded-4 overflow-hidden dashboard-card $bg text-white position-relative'>
                $badge
                <div class='card-body p-3'>
                    <div class='bg-white bg-opacity-25 rounded-3 d-inline-flex align-items-center justify-content-center mb-3' style='width: 45px; height: 45px;'>
                        <i class='bi $icon fs-4'></i>
                    </div>
                    <h6 class='fw-bold mb-1'>$title</h6>
                    <p style='font-size: 0.75rem;' class='mb-0 opacity-75'>$desc</p>
                </div>
            </div>
        </a>
    </div>";
}
?>

<style>
    .bg-teal { background-color: #20c997; }
    .bg-indigo { background-color: #6610f2; }
    .dashboard-card {
        transition: all 0.3s cubic-bezier(.25,.8,.25,1);
        min-height: 140px;
    }
    .dashboard-card:hover:not(.opacity-50) {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
        filter: brightness(1.1);
    }
    .opacity-50 {
        cursor: not-allowed;
    }
</style>

<style>
    .dashboard-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
    }
    .dashboard-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
    }
</style>
<?= $this->endSection() ?>
