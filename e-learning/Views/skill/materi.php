<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <!-- Header -->
            <div class="card-header p-4 border-0 text-white d-flex align-items-center gap-3" style="background-color: var(--bs-<?= $materi['color'] ?>);">
                <a href="<?= base_url('e-learning/skill') ?>" class="btn btn-sm btn-light rounded-circle" style="color: var(--bs-<?= $materi['color'] ?>);">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 py-1 small mb-1">MATERI PELATIHAN MANDIRI</span>
                    <h4 class="fw-bold mb-0"><?= esc($materi['judul']) ?></h4>
                </div>
            </div>

            <!-- Konten Materi Lengkap -->
            <div class="card-body p-4 p-md-5 bg-body">
                <div class="mb-4 pb-3 border-bottom text-muted small">
                    <i class="bi bi-info-circle-fill me-2 text-<?= $materi['color'] ?>"></i>
                    <strong>Ringkasan Target:</strong> <?= esc($materi['ringkasan']) ?>
                </div>

                <!-- Teks Inti Silabus Lengkap -->
                <div class="materi-content" style="line-height: 1.8; font-size: 1.05rem;">
                    <?= $materi['materi_lengkap'] ?>
                </div>

                <!-- Contoh Latihan & Simulasi Interaktif di Bawah Materi -->
                <div class="mt-5 p-4 bg-light rounded-4 border">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-lightbulb-fill text-warning me-2"></i>Siap Menguji Pemahaman Anda?
                    </h5>
                    <p class="text-muted small mb-4">
                        Setelah mempelajari rangkuman kaidah dan strategi di atas secara saksama, ujilah penguasaan materi Anda melalui kuis interaktif bab ini.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="<?= base_url('e-learning/skill/kuis/' . $bab_id) ?>" class="btn btn-<?= $materi['color'] ?> px-4 py-2 fw-bold rounded-pill text-white shadow-sm">
                            <i class="bi bi-play-circle me-2"></i>Mulai Kuis Bab Ini
                        </a>
                        <a href="<?= base_url('e-learning/skill') ?>" class="btn btn-outline-secondary px-4 py-2 fw-medium rounded-pill">
                            Kembali ke Menu Utama
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .materi-content ul, .materi-content ol {
        margin-bottom: 1.5rem;
        padding-left: 1.5rem;
    }
    .materi-content li {
        margin-bottom: 0.5rem;
    }
</style>
<?= $this->endSection() ?>
