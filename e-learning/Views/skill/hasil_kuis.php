<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-center p-4 p-md-5 bg-body">
            <!-- Ikon Hasil -->
            <div class="mb-4">
                <?php if($skor_akhir >= 80): ?>
                    <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle" style="width: 100px; height: 100px; font-size: 3.5rem;">
                        <i class="bi bi-patch-check-fill"></i>
                    </div>
                <?php elseif($skor_akhir >= 60): ?>
                    <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning rounded-circle" style="width: 100px; height: 100px; font-size: 3.5rem;">
                        <i class="bi bi-emoji-smile-fill"></i>
                    </div>
                <?php else: ?>
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle" style="width: 100px; height: 100px; font-size: 3.5rem;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Identitas & Skor -->
            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1 rounded-pill small fw-bold mb-2">HASIL EVALUASI KUIS</span>
            <h3 class="fw-bold text-dark mb-1"><?= esc($judul_tes) ?></h3>
            <p class="text-muted small mb-4">Atas Nama: <strong class="text-dark"><?= esc($nama) ?></strong> (<?= esc($kelas) ?>)</p>

            <div class="p-4 bg-light rounded-4 mb-4 max-w-md mx-auto border">
                <div class="text-muted small mb-1">Skor Pencapaian Anda</div>
                <div class="display-3 fw-bold text-dark mb-2"><?= round($skor_akhir) ?> <span class="fs-5 text-muted fw-normal">/ 100</span></div>
                <div class="d-flex justify-content-center gap-3 small border-top pt-2 mt-2 text-muted">
                    <span>Jawaban Benar: <strong class="text-success"><?= $benar ?></strong></span>
                    <span>Total Soal: <strong><?= $total ?></strong></span>
                </div>
            </div>

            <!-- Catatan Evaluasi -->
            <div class="alert alert-info small mb-5 text-start">
                <strong>Catatan Pengampu:</strong> <?= esc($catatan) ?>
            </div>

            <!-- Tombol Navigasi -->
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="<?= base_url('e-learning/skill/kuis/' . $bab_id) ?>" class="btn btn-outline-primary px-4 py-2 rounded-pill fw-medium">
                    <i class="bi bi-arrow-clockwise me-1"></i>Ulangi Kuis
                </a>
                <a href="<?= base_url('e-learning/skill/materi/' . $bab_id) ?>" class="btn btn-outline-secondary px-4 py-2 rounded-pill fw-medium">
                    <i class="bi bi-book me-1"></i>Baca Ulang Materi
                </a>
                <a href="<?= base_url('e-learning/skill') ?>" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm">
                    Kembali ke Beranda Pelatihan
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
