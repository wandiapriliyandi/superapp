<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <!-- Header Kartu -->
            <div class="card-header text-white p-4 border-0 d-flex justify-content-between align-items-center" style="background-color: #6610f2;">
                <div class="d-flex align-items-center gap-3">
                    <a href="<?= base_url('e-learning/ujian') ?>" class="btn btn-sm btn-light rounded-circle" style="color: #6610f2;">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill fw-bold small me-2">
                            <?= esc($ujian['mata_pelajaran']) ?>
                        </span>
                        <span class="badge bg-dark bg-opacity-25 text-white px-3 py-1 rounded-pill small">
                            <?= esc($ujian['kelas']) ?>
                        </span>
                    </div>
                </div>
                <div>
                    <?php if($ujian['status'] === 'Aktif'): ?>
                        <span class="badge bg-success px-3 py-2 rounded-pill">Status: AKTIF</span>
                    <?php elseif($ujian['status'] === 'Draf'): ?>
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Status: DRAF</span>
                    <?php else: ?>
                        <span class="badge bg-secondary px-3 py-2 rounded-pill">Status: SELESAI</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Rincian Parameter Ujian -->
            <div class="card-body p-4 p-md-5">
                <h2 class="fw-bold text-dark mb-4"><?= esc($ujian['judul']) ?></h2>
                
                <div class="row g-4 mb-5 p-4 bg-light rounded-4">
                    <div class="col-md-4 border-end">
                        <div class="text-muted small mb-1"><i class="bi bi-stopwatch text-danger me-2"></i>Durasi Waktu</div>
                        <h4 class="fw-bold text-dark mb-0"><?= esc($ujian['durasi_menit']) ?> <span class="fs-6 fw-normal text-muted">Menit</span></h4>
                    </div>
                    <div class="col-md-4 border-end">
                        <div class="text-muted small mb-1"><i class="bi bi-calendar-check text-success me-2"></i>Akses Dibuka</div>
                        <div class="fw-semibold text-dark small">
                            <?= date('d M Y', strtotime($ujian['tgl_mulai'])) ?><br>
                            <span class="text-muted"><?= date('H:i', strtotime($ujian['tgl_mulai'])) ?> WIB</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small mb-1"><i class="bi bi-calendar-x text-danger me-2"></i>Batas Ditutup</div>
                        <div class="fw-semibold text-dark small">
                            <?= date('d M Y', strtotime($ujian['tgl_selesai'])) ?><br>
                            <span class="text-muted"><?= date('H:i', strtotime($ujian['tgl_selesai'])) ?> WIB</span>
                        </div>
                    </div>
                </div>

                <!-- Petunjuk / Deskripsi -->
                <h5 class="fw-bold mb-3 text-secondary"><i class="bi bi-info-square me-2"></i>Petunjuk Pengerjaan Soal</h5>
                <div class="p-4 border rounded-4 mb-5 text-break bg-body-tertiary" style="line-height: 1.8;">
                    <?= nl2br(esc($ujian['deskripsi'])) ?>
                </div>

                <!-- Area Pengerjaan / Status Ujian Simulasi -->
                <div class="text-center p-5 border rounded-4 bg-light">
                    <div class="mb-3">
                        <i class="bi bi-laptop text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="fw-bold">Simulasi Modul Pengerjaan Santri</h5>
                    <p class="text-muted small max-w-md mx-auto mb-4">
                        Di antarmuka santri, tombol mulai ujian akan otomatis aktif pada rentang waktu yang telah dijadwalkan dan menghitung mundur durasi pengerjaan.
                    </p>
                    <button type="button" class="btn btn-lg text-white px-5 rounded-pill shadow-sm" style="background-color: #6610f2;" onclick="alert('Ini adalah tampilan rincian di dashboard admin. Fitur pengerjaan soal diakses melalui portal aplikasi santri.')">
                        <i class="bi bi-play-circle me-2"></i>Simulasikan Pengerjaan Soal
                    </button>
                </div>
            </div>

            <!-- Tombol Aksi Bawah -->
            <div class="card-footer bg-light p-4 border-top-0 d-flex justify-content-between align-items-center">
                <a href="<?= base_url('e-learning/ujian') ?>" class="btn btn-outline-secondary px-4 rounded-pill fw-medium">Kembali ke Daftar</a>
                <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-medium" data-bs-toggle="modal" data-bs-target="#deleteModal" data-href="<?= base_url('e-learning/ujian/delete/' . $ujian['id']) ?>">
                    <i class="bi bi-trash3 me-2"></i>Batalkan & Hapus Ujian
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
