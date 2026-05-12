<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="p-5 bg-primary bg-gradient text-white rounded-5 shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-4 position-relative overflow-hidden mb-3">
            <div class="position-relative z-1">
                <span class="badge bg-white text-primary px-3 py-1 rounded-pill fw-bold small mb-3">PROGRAM PELATIHAN MANDIRI</span>
                <h2 class="fw-bold mb-2 display-6">Pengembangan Skill & TOEFL Preparation</h2>
                <p class="text-white-50 mb-0 max-w-lg fs-6">
                    Program pelatihan intensif di luar kurikulum formal untuk melatih kecakapan akademis global santri menuju standar beasiswa dan universitas internasional.
                </p>
            </div>
            <div class="d-flex gap-3 position-relative z-1">
                <a href="<?= base_url('e-learning/skill/simulasi') ?>" class="btn btn-light text-primary px-4 py-3 fw-bold rounded-pill shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-award-fill fs-5 text-warning"></i>Simulasi Ujian TOEFL
                </a>
                <a href="<?= base_url('e-learning/skill/riwayat') ?>" class="btn btn-outline-light px-4 py-3 fw-bold rounded-pill d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history"></i>Riwayat Skor
                </a>
            </div>
            <!-- Dekorasi Latar -->
            <div class="position-absolute end-0 bottom-0 opacity-10 translate-middle-y me-n5" style="font-size: 15rem;">
                <i class="bi bi-globe-americas"></i>
            </div>
        </div>

        <!-- Navigasi Cepat Submodul E-Learning -->
        <div class="d-flex gap-2 pb-2 border-bottom overflow-auto">
            <a href="<?= base_url('e-learning/materi') ?>" class="btn btn-sm btn-outline-primary rounded-pill fw-medium px-3">
                <i class="bi bi-book me-1"></i>Materi Belajar
            </a>
            <a href="<?= base_url('e-learning/ujian') ?>" class="btn btn-sm btn-outline-secondary rounded-pill fw-medium px-3">
                <i class="bi bi-pencil-square me-1"></i>Ujian Online
            </a>
            <a href="<?= base_url('e-learning/skill') ?>" class="btn btn-sm text-dark border-warning rounded-pill fw-bold px-3 bg-warning bg-opacity-25" style="background-color: #ffc107;">
                <i class="bi bi-award-fill text-dark me-1"></i>Pelatihan Skill & TOEFL
            </a>
        </div>
    </div>
</div>

<?php foreach ($materiPerKategori as $kategori => $daftarMateri): ?>
    <div class="row g-4 mb-5">
        <div class="col-12">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-journal-bookmark-fill text-primary fs-4"></i>
                <h4 class="fw-bold mb-0"><?= esc($kategori) ?></h4>
            </div>
            <p class="text-muted small">Kuasai intisari materi <?= strtolower(esc($kategori)) ?> secara mendalam sebelum mengambil ujian evaluasi.</p>
        </div>

        <?php foreach ($daftarMateri as $m): ?>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden d-flex flex-column hover-card">
                    <div class="p-4 bg-<?= $m['color'] ?> bg-opacity-10 text-<?= $m['color'] ?> d-flex justify-content-between align-items-center">
                        <div class="bg-<?= $m['color'] ?> text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 45px; height: 45px;">
                            <i class="bi <?= esc($m['icon']) ?> fs-5"></i>
                        </div>
                        <span class="badge bg-<?= $m['color'] ?> rounded-pill text-uppercase" style="font-size: 0.7rem;">Modul</span>
                    </div>
                    <div class="card-body p-4 flex-grow-1">
                        <h5 class="fw-bold text-dark mb-2"><?= esc($m['judul']) ?></h5>
                        <p class="text-muted small mb-4">
                            <?= esc($m['ringkasan']) ?>
                        </p>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 pt-0 d-flex gap-2">
                        <a href="<?= base_url('e-learning/skill/materi/' . $m['slug_bab']) ?>" class="btn btn-<?= $m['color'] ?> w-50 py-2 fw-semibold rounded-pill small text-white">
                            <i class="bi bi-book me-1"></i>Materi
                        </a>
                        <a href="<?= base_url('e-learning/skill/kuis/' . $m['slug_bab']) ?>" class="btn btn-outline-<?= $m['color'] ?> w-50 py-2 fw-semibold rounded-pill small">
                            <i class="bi bi-patch-question me-1"></i>Kuis Bab
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<!-- Banner Ujian Akhir Terpadu -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-body-tertiary">
    <div class="row g-0 align-items-center">
        <div class="col-md-8 p-4 p-md-5">
            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-1 rounded-pill fw-bold small mb-2">EVALUASI PUNCAK</span>
            <h3 class="fw-bold text-dark mb-2">Simulasi Akhir Terpadu TOEFL PBT</h3>
            <p class="text-muted small mb-4">
                Uji kapabilitas komprehensif Anda melalui pengerjaan paket lengkap 15 butir soal simulasi terpadu (Listening, Structure, dan Reading) dengan sistem konversi skor akhir nyata standar ITP/PBT (Rentang Skor: 310 - 677).
            </p>
            <a href="<?= base_url('e-learning/skill/simulasi') ?>" class="btn btn-warning text-dark px-5 py-3 fw-bold rounded-pill shadow-sm">
                <i class="bi bi-rocket-takeoff-fill me-2"></i>Mulai Ujian Simulasi Sekarang
            </a>
        </div>
        <div class="col-md-4 text-center p-4 bg-warning bg-opacity-10 h-100 d-flex align-items-center justify-content-center">
            <div class="text-warning" style="font-size: 8rem;">
                <i class="bi bi-trophy-fill"></i>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
    }
</style>
<?= $this->endSection() ?>
