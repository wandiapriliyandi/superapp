<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <!-- Bagian Atas / Header Kartu -->
            <div class="card-header bg-light p-4 border-bottom-0 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <a href="<?= base_url('e-learning/materi') ?>" class="btn btn-sm btn-outline-secondary rounded-circle">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill fw-bold small me-2">
                            <?= esc($materi['mata_pelajaran']) ?>
                        </span>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1 rounded-pill small">
                            <?= esc($materi['kelas']) ?>
                        </span>
                    </div>
                </div>
                <div class="text-muted small">
                    Diunggah: <?= date('d M Y, H:i', strtotime($materi['created_at'])) ?>
                </div>
            </div>

            <!-- Isi Utama Materi -->
            <div class="card-body p-4 p-md-5">
                <h2 class="fw-bold text-dark mb-3"><?= esc($materi['judul']) ?></h2>
                
                <div class="d-flex align-items-center gap-2 mb-4 pb-3 border-bottom text-muted">
                    <i class="bi bi-person-badge fs-5 text-primary"></i>
                    <span class="fw-semibold text-dark">Pengampu:</span>
                    <span><?= esc($materi['guru_pengampu'] ?: 'Tidak disebutkan') ?></span>
                </div>

                <!-- Bagian Video Pembelajaran jika ada -->
                <?php if(!empty($materi['link_video'])): ?>
                    <div class="mb-5 bg-dark rounded-4 overflow-hidden shadow-sm p-1">
                        <?php 
                        // Sederhana merubah watch?v= menjadi embed/ jika link youtube
                        $embedUrl = $materi['link_video'];
                        if (strpos($embedUrl, 'youtube.com/watch?v=') !== false) {
                            $embedUrl = str_replace('youtube.com/watch?v=', 'youtube.com/embed/', $embedUrl);
                        }
                        ?>
                        <div class="ratio ratio-16x9 rounded-3 overflow-hidden">
                            <iframe src="<?= esc($embedUrl) ?>" title="Video Pembelajaran" allowfullscreen border="0"></iframe>
                        </div>
                        <div class="p-2 text-center bg-dark text-white-50 small">
                            <a href="<?= esc($materi['link_video']) ?>" target="_blank" class="text-white-50 text-decoration-none">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Buka langsung di YouTube
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Deskripsi Lengkap -->
                <h5 class="fw-bold mb-3 text-secondary"><i class="bi bi-card-text me-2"></i>Rangkuman & Deskripsi Materi</h5>
                <div class="p-4 bg-light rounded-4 mb-4 text-break" style="line-height: 1.8;">
                    <?= nl2br(esc($materi['deskripsi'])) ?>
                </div>

                <!-- Unduhan Dokumen Terlampir -->
                <?php if(!empty($materi['file_materi'])): ?>
                    <div class="border rounded-4 p-4 d-flex justify-content-between align-items-center bg-body-tertiary">
                        <div class="d-flex align-items-center gap-3 overflow-hidden">
                            <div class="bg-success bg-opacity-10 text-success p-3 rounded-3 fs-3">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="overflow-hidden">
                                <h6 class="fw-bold mb-1 text-truncate">Lampiran Dokumen Tersedia</h6>
                                <p class="text-muted small mb-0 text-truncate"><?= esc($materi['file_materi']) ?></p>
                            </div>
                        </div>
                        <a href="<?= base_url('uploads/materi/' . esc($materi['file_materi'])) ?>" class="btn btn-success px-4 py-2 fw-semibold rounded-pill flex-shrink-0" target="_blank">
                            <i class="bi bi-cloud-arrow-down-fill me-2"></i>Unduh Dokumen
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tombol Aksi Bawah -->
            <div class="card-footer bg-light p-4 border-top-0 d-flex justify-content-between align-items-center">
                <a href="<?= base_url('e-learning/materi') ?>" class="btn btn-outline-secondary px-4 rounded-pill fw-medium">Kembali ke Daftar</a>
                <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-medium" data-bs-toggle="modal" data-bs-target="#deleteModal" data-href="<?= base_url('e-learning/materi/delete/' . $materi['id']) ?>">
                    <i class="bi bi-trash3 me-2"></i>Hapus Materi Ini
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
