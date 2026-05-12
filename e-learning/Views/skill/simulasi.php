<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <!-- Header Banner -->
            <div class="card-header bg-warning bg-gradient p-4 p-md-5 border-0 text-dark">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <a href="<?= base_url('e-learning/skill') ?>" class="btn btn-sm btn-dark text-warning rounded-circle">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <span class="badge bg-dark text-warning px-3 py-1 rounded-pill small fw-bold">EVALUASI KESELURUHAN (FINAL TEST)</span>
                </div>
                <h2 class="fw-bold mb-1">Simulasi Ujian Akhir Terpadu TOEFL PBT</h2>
                <p class="text-dark bg-opacity-75 small mb-0 max-w-lg">
                    Paket ujian komprehensif berdurasi mandiri mencakup 15 butir pertanyaan acak terpadu dari seksi Listening, Structure, dan Reading. Jawablah seluruh butir soal untuk mendapatkan estimasi skor TOEFL akhir Anda.
                </p>
            </div>

            <form action="<?= base_url('e-learning/skill/submit-simulasi') ?>" method="post" class="card-body p-4 p-md-5 bg-body">
                <!-- Formulir Data Peserta -->
                <div class="p-4 bg-light rounded-4 border mb-5">
                    <div class="row g-3">
                        <div class="col-12 mb-1">
                            <span class="fw-bold text-dark small text-uppercase"><i class="bi bi-person-badge-fill text-warning me-2"></i>Data Identitas Peserta Ujian</span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Nama Lengkap Santri</label>
                            <input type="text" name="nama_santri" class="form-control form-control-lg" placeholder="Masukkan nama lengkap..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Sasaran Tingkatan</label>
                            <select name="kelas" class="form-select form-select-lg" required>
                                <option value="">-- Pilih Tingkatan --</option>
                                <option value="Kelas 10">Kelas 10</option>
                                <option value="Kelas 11">Kelas 11</option>
                                <option value="Kelas 12">Kelas 12</option>
                                <option value="Guru / Pengurus">Guru / Pengurus</option>
                                <option value="Alumni / Umum">Alumni / Umum</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Bagian Pertanyaan Terpadu -->
                <?php 
                $kategoriSaatIni = '';
                foreach($soal as $idx => $s): 
                    // Tampilkan pemisah jika berganti kategori/seksi
                    if($s['kategori_bab'] !== $kategoriSaatIni):
                        $kategoriSaatIni = $s['kategori_bab'];
                        $badgeColor = 'primary';
                        if (strpos($kategoriSaatIni, 'Structure') !== false) $badgeColor = 'success';
                        if (strpos($kategoriSaatIni, 'Reading') !== false) $badgeColor = 'indigo';
                ?>
                    <div class="mt-5 mb-4 pt-3 border-top">
                        <span class="badge bg-<?= $badgeColor ?> px-3 py-2 rounded-pill small fw-bold text-uppercase mb-2">
                            SEKSI: <?= esc($kategoriSaatIni) ?>
                        </span>
                    </div>
                <?php endif; ?>

                    <!-- Butir Pertanyaan -->
                    <div class="mb-4 p-4 bg-body-tertiary rounded-4 border shadow-sm">
                        <div class="d-flex gap-3 mb-3">
                            <div class="bg-dark text-warning rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                <?= $s['nomor_simulasi'] ?>
                            </div>
                            <div class="fw-medium text-dark pt-1" style="font-size: 1.05rem;">
                                <?= $s['pertanyaan'] ?>
                            </div>
                        </div>

                        <div class="ps-md-5 pt-2">
                            <div class="row g-3">
                                <?php foreach($s['pilihan'] as $optKey => $optText): ?>
                                    <div class="col-12">
                                        <label class="btn btn-outline-secondary w-100 text-start p-3 rounded-3 d-flex align-items-center gap-3 sim-option">
                                            <input type="radio" name="jawaban[<?= $s['nomor_simulasi'] ?>]" value="<?= $optKey ?>" class="form-check-input mt-0 flex-shrink-0" required>
                                            <span class="fw-bold text-dark flex-shrink-0"><?= $optKey ?>.</span>
                                            <span class="text-body"><?= $optText ?></span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="p-4 bg-warning bg-opacity-10 rounded-4 border border-warning border-opacity-25 mt-5 mb-4 text-center">
                    <p class="small text-dark mb-0 fw-medium">
                        <i class="bi bi-shield-lock-fill text-warning me-2"></i>Pastikan seluruh butir dari nomor 1 hingga 15 telah terisi sebelum menekan tombol penyelesaian di bawah. Sistem akan memproses perkiraan rentang skor akhir secara instan.
                    </p>
                </div>

                <div class="d-flex justify-content-end gap-3 pt-3">
                    <a href="<?= base_url('e-learning/skill') ?>" class="btn btn-light px-4 py-3 fw-semibold rounded-pill">Batalkan Simulasi</a>
                    <button type="submit" class="btn btn-dark text-warning px-5 py-3 fw-bold rounded-pill shadow-sm">
                        <i class="bi bi-award-fill me-2"></i>Kumpulkan & Lihat Prediksi Skor TOEFL
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .sim-option {
        border-color: rgba(0,0,0,0.1);
        transition: all 0.2s ease;
    }
    .sim-option:hover {
        background-color: rgba(0,0,0,0.02);
        border-color: rgba(0,0,0,0.2);
    }
    .form-check-input:checked ~ span {
        font-weight: bold;
    }
    .text-indigo { color: #6610f2; }
    .bg-indigo { background-color: #6610f2; color: #fff; }
</style>
<?= $this->endSection() ?>
