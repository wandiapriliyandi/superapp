<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Sertifikat Hasil Simulasi -->
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-5 bg-body position-relative">
            <!-- Pita Dekorasi -->
            <div class="bg-dark text-warning py-3 px-4 text-center fw-bold letter-spacing">
                ★ PREDIKSI RESMI HASIL SIMULASI TOEFL PBT/ITP ★
            </div>

            <div class="card-body p-4 p-md-5 text-center position-relative z-1">
                <div class="mb-3 text-warning" style="font-size: 4rem;">
                    <i class="bi bi-award-fill"></i>
                </div>

                <span class="text-uppercase small text-muted fw-bold d-block mb-1">DIBERIKAN KEPADA</span>
                <h2 class="fw-bold text-dark mb-1 display-6"><?= esc($nama) ?></h2>
                <p class="text-muted small mb-4">Tingkat / Kelas: <strong class="text-dark"><?= esc($kelas) ?></strong></p>

                <!-- Skor Pusat -->
                <div class="my-4 py-4 px-5 bg-light rounded-4 border max-w-md mx-auto shadow-sm">
                    <span class="small text-muted d-block mb-1">PREDIKSI SKOR TOEFL KONVERSI</span>
                    <div class="display-1 fw-bold text-dark mb-0" style="letter-spacing: -2px;"><?= $skor_toefl ?></div>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1 rounded-pill small mt-2">Rentang Standar: 310 - 677</span>
                </div>

                <!-- Perincian Benar / Salah -->
                <div class="row g-2 justify-content-center max-w-md mx-auto mb-4 small text-muted">
                    <div class="col-6 border-end">
                        <span>Total Jawaban Benar: <strong class="text-success"><?= $benar ?></strong></span>
                    </div>
                    <div class="col-6">
                        <span>Total Butir Soal: <strong><?= $total ?></strong></span>
                    </div>
                </div>

                <!-- Kesimpulan Kualifikasi -->
                <div class="p-4 bg-warning bg-opacity-10 rounded-4 border border-warning border-opacity-25 text-start mb-4">
                    <h6 class="fw-bold text-dark mb-1"><i class="bi bi-bookmark-star-fill text-warning me-2"></i>Kualifikasi & Rekomendasi</h6>
                    <p class="small text-dark mb-0" style="line-height: 1.6;">
                        <?= esc($catatan) ?>
                    </p>
                </div>

                <div class="text-muted small border-top pt-3 mt-4">
                    Sertifikat simulasi internal diterbitkan secara otomatis oleh modul pengembangan E-Learning SuperApp Pesantren pada tanggal <?= date('d M Y') ?>.
                </div>
            </div>
            
            <!-- Watermark -->
            <div class="position-absolute start-50 top-50 translate-middle opacity-5 pointer-events-none" style="font-size: 25rem; color: #adb5bd;">
                <i class="bi bi-shield-check"></i>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="text-center d-flex justify-content-center gap-3 flex-wrap">
            <a href="<?= base_url('e-learning/skill/simulasi') ?>" class="btn btn-outline-dark px-4 py-3 rounded-pill fw-bold">
                <i class="bi bi-arrow-repeat me-2"></i>Ulangi Simulasi Ujian
            </a>
            <a href="<?= base_url('e-learning/skill/riwayat') ?>" class="btn btn-warning text-dark px-4 py-3 rounded-pill fw-bold shadow-sm">
                <i class="bi bi-clock-history me-2"></i>Lihat Papan Peringkat / Riwayat
            </a>
            <a href="<?= base_url('e-learning/skill') ?>" class="btn btn-primary px-4 py-3 rounded-pill fw-bold shadow-sm">
                Beranda Skill Mandiri
            </a>
        </div>
    </div>
</div>

<style>
    .letter-spacing { letter-spacing: 2px; font-size: 0.9rem; }
</style>
<?= $this->endSection() ?>
