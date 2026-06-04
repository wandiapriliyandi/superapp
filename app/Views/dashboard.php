<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row g-3">
    <div class="col-12 mb-2">
        <h3 class="fw-bold">SuperApp Pesantren Dashboard 👋</h3>
        <p class="text-muted">Pusat kendali manajemen pesantren terintegrasi.</p>
    </div>

    <!-- 0. Monitoring Eksekutif -->
    <?= dashboard_card('Monitoring', 'Laporan & Analisis Pimpinan', 'bi-speedometer2', 'bg-custom-indigo-dark', base_url('monitoring')) ?>

    <!-- 1. PPDB -->
    <?= dashboard_card('PPDB', 'Pendaftaran Santri Baru', 'bi-person-plus', 'bg-custom-blue', base_url('ppdb/dashboard')) ?>
 
    <!-- 2. Siakad -->
    <?= dashboard_card('SIAKAD', 'Data Santri & Akademik', 'bi-mortarboard', 'bg-custom-emerald', base_url('akademik/dashboard')) ?>
 
    <!-- 3. HR (Kepegawaian) -->
    <?= dashboard_card('Kepegawaian', 'Data Guru & Karyawan', 'bi-person-workspace', 'bg-custom-cyan', base_url('kepegawaian/dashboard')) ?>
 
    <!-- 4. Kedisiplinan -->
    <?= dashboard_card('Kedisiplinan', 'Poin Pelanggaran & Prestasi', 'bi-shield-check', 'bg-custom-rose', '#', 'Coming Soon') ?>
 
    <!-- 5. Perizinan -->
    <?= dashboard_card('Perizinan', 'Izin Pulang & Keluar', 'bi-card-checklist', 'bg-custom-orange', base_url('perijinan')) ?>
 
    <!-- 6. Keasramaan -->
    <?= dashboard_card('Keasramaan', 'Manajemen Kamar & Asrama', 'bi-houses', 'bg-custom-indigo', '#', 'Coming Soon') ?>
 
    <!-- 7. Kurikulum Spesialis -->
    <?= dashboard_card('Tahfidz/Diniyah', 'Progres Hafalan & Kitab', 'bi-book', 'bg-custom-teal', '#', 'Coming Soon') ?>
 
    <!-- 8. E-Learning -->
    <?= dashboard_card('E-Learning', 'Materi & Ujian Online', 'bi-laptop', 'bg-custom-violet', base_url('e-learning/dashboard')) ?>
 
    <!-- 9. SPP -->
    <?= dashboard_card('Manajemen SPP', 'Pembayaran & Tagihan Santri', 'bi-wallet2', 'bg-custom-amber', base_url('spp')) ?>
 
    <!-- 10. Keuangan -->
    <?= dashboard_card('Keuangan', 'Akuntansi, Jurnal & Buku Besar', 'bi-calculator', 'bg-custom-slate', base_url('keuangan')) ?>

    <!-- 11. OSIS -->
    <?= dashboard_card('OSIS', 'Kegiatan Santri/Siswa', 'bi-people', 'bg-custom-purple', '#', 'Coming Soon') ?>

    <!-- 12. Perpustakaan -->
    <?= dashboard_card('Perpustakaan', 'Katalog & Peminjaman Buku', 'bi-journal-bookmark', 'bg-custom-sky', base_url('perpustakaan')) ?>

    <!-- 13. Inventory -->
    <?= dashboard_card('Inventory', 'Sarana & Infrastruktur', 'bi-box-seam', 'bg-custom-gray', '#', 'Coming Soon') ?>

    <!-- 14. Poskestren -->
    <?= dashboard_card('Poskestren', 'Kesehatan & Rekam Medis', 'bi-heart-pulse', 'bg-custom-crimson', base_url('poskestren')) ?>

    <!-- 15. Portal Wali -->
    <?= dashboard_card('Portal Wali', 'Akses Orang Tua Santri', 'bi-phone-vibrate', 'bg-dark', '#', 'Coming Soon') ?>

    <!-- 16. Pengaturan -->
    <?= dashboard_card('Pengaturan', 'Profil Pesantren & Tema', 'bi-gear', 'bg-custom-charcoal text-white', base_url('setting')) ?>

    <!-- Additional: Activity Log -->
    <?= dashboard_card('Log Aktivitas', 'Riwayat Audit Sistem', 'bi-clock-history', 'bg-light text-dark border', base_url('activity')) ?>
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
    .bg-custom-blue { background-color: #3b82f6; }
    .bg-custom-emerald { background-color: #10b981; }
    .bg-custom-cyan { background-color: #06b6d4; }
    .bg-custom-rose { background-color: #f43f5e; }
    .bg-custom-orange { background-color: #f59e0b; }
    .bg-custom-indigo { background-color: #6366f1; }
    .bg-custom-teal { background-color: #14b8a6; }
    .bg-custom-violet { background-color: #8b5cf6; }
    .bg-custom-amber { background-color: #d97706; }
    .bg-custom-slate { background-color: #334155; }
    .bg-custom-purple { background-color: #a855f7; }
    .bg-custom-sky { background-color: #0ea5e9; }
    .bg-custom-gray { background-color: #64748b; }
    .bg-custom-crimson { background-color: #e11d48; }
    .bg-custom-indigo-dark { background-color: #312e81; }
    .bg-custom-charcoal { background-color: #374151; }

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
