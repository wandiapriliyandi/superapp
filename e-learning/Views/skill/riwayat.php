<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h3 class="fw-bold mb-1 text-dark"><i class="bi bi-clock-history text-warning me-2"></i>Papan Peringkat & Riwayat Skor Pelatihan</h3>
                <p class="text-muted small mb-0">Melacak perkembangan pencapaian nilai tes, kuis evaluasi per bab, serta skor TOEFL akhir seluruh santri.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('e-learning/skill/simulasi') ?>" class="btn btn-warning text-dark fw-bold px-4 rounded-pill shadow-sm">
                    <i class="bi bi-award-fill me-1"></i>Mulai Simulasi Baru
                </a>
                <a href="<?= base_url('e-learning/skill') ?>" class="btn btn-outline-secondary px-4 rounded-pill fw-medium">
                    Menu Utama
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-body">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-uppercase fs-7" style="letter-spacing: 0.5px;">
                <tr>
                    <th class="py-3 ps-4">No</th>
                    <th class="py-3">Nama Peserta / Santri</th>
                    <th class="py-3">Kelas</th>
                    <th class="py-3">Jenis Ujian / Tes</th>
                    <th class="py-3 text-center">Jawaban Benar</th>
                    <th class="py-3 text-center">Skor Pencapaian</th>
                    <th class="py-3 pe-4">Catatan Evaluasi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($riwayat)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada riwayat pengerjaan tes atau kuis.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach($riwayat as $r): ?>
                        <tr>
                            <td class="ps-4 fw-medium text-muted"><?= $no++ ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= esc($r['nama_santri']) ?></div>
                                <div class="fs-8 text-muted"><?= date('d M Y, H:i', strtotime($r['created_at'])) ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border px-2 py-1"><?= esc($r['kelas']) ?></span></td>
                            <td>
                                <span class="fw-semibold text-secondary"><?= esc($r['jenis_tes']) ?></span>
                            </td>
                            <td class="text-center fw-medium text-dark">
                                <?= esc($r['skor_benar']) ?> <span class="text-muted small">/ <?= esc($r['total_soal']) ?></span>
                            </td>
                            <td class="text-center">
                                <?php if(strpos($r['jenis_tes'], 'Simulasi') !== false): ?>
                                    <span class="badge bg-dark text-warning px-3 py-2 rounded-pill fw-bold fs-7" style="letter-spacing: 1px;">
                                        TOEFL: <?= esc($r['skor_akhir']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">
                                        <?= esc($r['skor_akhir']) ?>%
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-4 text-muted small" style="max-width: 250px;">
                                <?= esc($r['catatan']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .fs-7 { font-size: 0.85rem; }
    .fs-8 { font-size: 0.75rem; }
</style>
<?= $this->endSection() ?>
