<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-primary bg-opacity-10 border-0 p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="<?= base_url('poskestren/kunjungan') ?>" class="btn btn-white shadow-sm rounded-circle me-3">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <h5 class="fw-bold mb-0 text-primary">Detail Rekam Medis</h5>
                    </div>
                    <span class="badge bg-primary rounded-pill px-3 py-2 small">ID #<?= $kunjungan['id'] ?></span>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row mb-5">
                    <div class="col-md-6 border-end">
                        <label class="text-muted small fw-bold mb-1">DATA SANTRI</label>
                        <h5 class="fw-bold mb-1"><?= $kunjungan['nama_santri'] ?></h5>
                        <p class="text-muted mb-0"><?= $kunjungan['nis'] ?> | Kelas <?= $kunjungan['nama_kelas'] ?></p>
                    </div>
                    <div class="col-md-6 ps-md-4">
                        <label class="text-muted small fw-bold mb-1">WAKTU KUNJUNGAN</label>
                        <h5 class="fw-bold mb-1"><?= date('d F Y', strtotime($kunjungan['tgl_kunjungan'])) ?></h5>
                        <p class="text-muted mb-0">Jam <?= date('H:i', strtotime($kunjungan['tgl_kunjungan'])) ?> WIB</p>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-muted small fw-bold mb-2">KELUHAN</label>
                    <div class="p-3 bg-light rounded-3">
                        <?= nl2br($kunjungan['keluhan']) ?>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold mb-2">DIAGNOSA</label>
                        <p class="fw-bold text-dark"><?= $kunjungan['diagnosa'] ?: 'Tidak ada diagnosa spesifik' ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold mb-2">STATUS AKHIR</label>
                        <div>
                            <?php 
                                $badge_class = 'bg-success';
                                if($kunjungan['status'] == 'Observasi') $badge_class = 'bg-warning';
                                if($kunjungan['status'] == 'Rujuk') $badge_class = 'bg-danger';
                            ?>
                            <span class="badge <?= $badge_class ?> rounded-pill px-3">
                                <?= $kunjungan['status'] ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-muted small fw-bold mb-2">TINDAKAN / CATATAN</label>
                    <p class="text-dark"><?= $kunjungan['tindakan'] ?: '-' ?></p>
                </div>

                <?php if (!empty($obat)): ?>
                <div class="mt-5">
                    <label class="text-muted small fw-bold mb-3 d-block"><i class="bi bi-capsule me-1"></i> OBAT YANG DIBERIKAN</label>
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3 py-2 small">Nama Obat</th>
                                    <th class="py-2 small">Jumlah</th>
                                    <th class="py-2 small">Dosis / Aturan Pakai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($obat as $o): ?>
                                <tr>
                                    <td class="ps-3 py-3 border-bottom">
                                        <div class="fw-bold"><?= $o['nama_obat'] ?></div>
                                    </td>
                                    <td class="py-3 border-bottom"><?= $o['jumlah'] ?> <?= $o['satuan'] ?></td>
                                    <td class="py-3 border-bottom">
                                        <span class="badge bg-light text-dark rounded-pill"><?= $o['dosis'] ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-light border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Dicatat oleh ID Petugas: <?= $kunjungan['petugas_id'] ?: '-' ?></span>
                    <button class="btn btn-primary rounded-pill btn-sm px-4" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i> Cetak Rekam Medis
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn, .card-header a, .card-footer button { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
        .card-header { background-color: #f8f9fa !important; border-bottom: 2px solid #0d6efd !important; }
        body { padding: 0; background: white; }
    }
</style>
<?= $this->endSection() ?>
