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
                    <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 py-1 small mb-1">EVALUASI KUIS BAB</span>
                    <h4 class="fw-bold mb-0"><?= esc($materi['judul']) ?></h4>
                </div>
            </div>

            <form action="<?= base_url('e-learning/skill/submit-kuis') ?>" method="post" class="card-body p-4 p-md-5 bg-body">
                <input type="hidden" name="bab_id" value="<?= esc($bab_id) ?>">
                
                <!-- Data Diri Pengambil Kuis -->
                <div class="row g-3 mb-5 p-4 bg-light rounded-4 border">
                    <div class="col-12 mb-1">
                        <span class="fw-bold text-dark small text-uppercase">Informasi Santri / Peserta Tes</span>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                        <input type="text" name="nama_santri" class="form-control" placeholder="Tuliskan nama Anda..." required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Kelas / Tingkat</label>
                        <select name="kelas" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            <option value="Kelas 10">Kelas 10</option>
                            <option value="Kelas 11">Kelas 11</option>
                            <option value="Kelas 12">Kelas 12</option>
                            <option value="Alumni / Umum">Alumni / Umum</option>
                        </select>
                    </div>
                </div>

                <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom">
                    <i class="bi bi-question-circle-fill text-<?= $materi['color'] ?> me-2"></i>Jawablah Pertanyaan Berikut:
                </h5>

                <!-- Daftar Soal Kuis -->
                <?php foreach($materi['kuis'] as $idx => $soal): ?>
                    <div class="mb-5 p-4 bg-body-tertiary rounded-4 border">
                        <div class="d-flex gap-3 mb-3">
                            <div class="bg-<?= $materi['color'] ?> text-white rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                <?= $idx + 1 ?>
                            </div>
                            <div class="fw-medium text-dark pt-1" style="font-size: 1.05rem;">
                                <?= $soal['pertanyaan'] ?>
                            </div>
                        </div>

                        <div class="ps-md-5 pt-2">
                            <div class="row g-3">
                                <?php foreach($soal['pilihan'] as $optKey => $optText): ?>
                                    <div class="col-12">
                                        <label class="btn btn-outline-secondary w-100 text-start p-3 rounded-3 d-flex align-items-center gap-3 option-label">
                                            <input type="radio" name="jawaban[<?= $soal['id'] ?>]" value="<?= $optKey ?>" class="form-check-input mt-0 flex-shrink-0" required>
                                            <span class="fw-bold text-dark flex-shrink-0"><?= $optKey ?>.</span>
                                            <span class="text-body"><?= $optText ?></span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="d-flex justify-content-end gap-3 pt-4 border-top">
                    <a href="<?= base_url('e-learning/skill') ?>" class="btn btn-light px-4 py-2 fw-semibold rounded-pill">Batal</a>
                    <button type="submit" class="btn btn-<?= $materi['color'] ?> text-white px-5 py-2 fw-bold rounded-pill shadow-sm">
                        <i class="bi bi-check2-all me-2"></i>Kumpulkan & Periksa Jawaban
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .option-label {
        border-color: rgba(0,0,0,0.1);
        transition: all 0.2s ease;
    }
    .option-label:hover {
        background-color: rgba(0,0,0,0.02);
        border-color: rgba(0,0,0,0.2);
    }
    .form-check-input:checked ~ span {
        font-weight: bold;
    }
</style>
<?= $this->endSection() ?>
