<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .timeline { position: relative; padding: 20px 0; }
    .timeline::before {
        content: '';
        position: absolute;
        top: 0; bottom: 0;
        left: 40px;
        width: 2px;
        background: #e9ecef;
    }
    .timeline-item { position: relative; margin-bottom: 30px; padding-left: 80px; }
    .timeline-icon {
        position: absolute;
        left: 20px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        z-index: 1;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
</style>

<div class="row">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="bg-primary p-5 text-center text-white">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($p['nama_lengkap']) ?>&background=random&size=100" class="rounded-circle border border-4 border-white mb-3 shadow" alt="Avatar">
                <h5 class="fw-bold mb-0"><?= $p['nama_lengkap'] ?></h5>
                <p class="small opacity-75 mb-0"><?= $p['nomor_pendaftaran'] ?></p>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="small text-muted d-block">Jenis Kelamin</label>
                    <span class="fw-bold"><?= $p['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></span>
                </div>
                <div class="mb-3">
                    <label class="small text-muted d-block">Tempat, Tgl Lahir</label>
                    <span class="fw-bold"><?= $p['tempat_lahir'] ?>, <?= date('d/m/Y', strtotime($p['tanggal_lahir'])) ?></span>
                </div>
                <div class="mb-3">
                    <label class="small text-muted d-block">No. HP Orang Tua</label>
                    <span class="fw-bold text-primary"><?= $p['no_hp_ortu'] ?></span>
                </div>
                <div class="mb-0 p-3 bg-light rounded-3 border-start border-4 border-primary">
                    <label class="small text-muted d-block">Sponsor / Rekomendasi</label>
                    <span class="fw-bold"><?= $p['sponsor'] ?: '-' ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-4">Timeline Perjalanan Pendaftaran</h5>
            
            <div class="timeline">
                <!-- Tahap 1: Pendaftaran -->
                <div class="timeline-item">
                    <div class="timeline-icon bg-info">
                        <i class="bi bi-pencil-fill"></i>
                    </div>
                    <div class="timeline-content">
                        <h6 class="fw-bold mb-0">Pendaftaran Akun / Formulir</h6>
                        <p class="small text-muted mb-1"><?= date('d M Y, H:i', strtotime($p['created_at'])) ?></p>
                        <div class="alert alert-light border-0 py-2 small mb-0">
                            Calon santri mengisi data diri dan memilih tahun ajaran pendaftaran.
                        </div>
                    </div>
                </div>

                <!-- Tahap Baru: Pembayaran -->
                <div class="timeline-item">
                    <?php if(!empty($pembayaran)): ?>
                        <div class="timeline-icon bg-success">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="fw-bold mb-0">Pembayaran Pendaftaran</h6>
                            <?php foreach($pembayaran as $bayar): ?>
                                <p class="small text-muted mb-1"><?= date('d M Y', strtotime($bayar['tanggal_bayar'])) ?></p>
                                <div class="alert alert-success border-0 py-2 small mb-2 d-flex justify-content-between align-items-center">
                                    <span>Lunas: <strong>Rp <?= number_format($bayar['jumlah'], 0, ',', '.') ?></strong> (<?= $bayar['metode_bayar'] ?>)</span>
                                    <a href="<?= base_url('ppdb/pendaftar/cetak-struk/'.$bayar['id']) ?>" target="_blank" class="btn btn-sm btn-light py-0">Struk <i class="bi bi-printer"></i></a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="timeline-icon bg-secondary opacity-50">
                            <i class="bi bi-cash"></i>
                        </div>
                        <div class="timeline-content opacity-50">
                            <h6 class="fw-bold mb-0">Belum Ada Pembayaran</h6>
                            <p class="small text-muted mb-0">Pendaftar belum melakukan pelunasan biaya pendaftaran.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tahap 2: Tes Seleksi -->
                <div class="timeline-item">
                    <?php if(!empty($tes)): ?>
                        <div class="timeline-icon bg-primary">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="fw-bold mb-0">Tes Seleksi</h6>
                            <?php foreach($tes as $t): ?>
                                <p class="small text-muted mb-1"><?= date('d M Y', strtotime($t['tanggal'])) ?> (<?= $t['jam'] ?>)</p>
                                <div class="card border shadow-none p-3 mb-2">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="small fw-bold text-primary"><?= $t['nama_tes'] ?></div>
                                            <div class="small text-muted"><?= $t['tempat'] ?></div>
                                        </div>
                                        <div class="col-auto text-end">
                                            <div class="small text-muted">Hasil:</div>
                                            <span class="badge bg-<?= $p['status_seleksi'] == 'Tidak Lulus' ? 'danger' : 'success' ?>">
                                                <?= $p['status_seleksi'] == 'Pending' ? 'Belum Ada Hasil' : $p['status_seleksi'] ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="timeline-icon bg-secondary opacity-50">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                        <div class="timeline-content opacity-50">
                            <h6 class="fw-bold mb-0">Belum Ada Jadwal Tes</h6>
                            <p class="small text-muted mb-0">Calon santri menunggu plotting jadwal tes dari Admin.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tahap 3: Verifikasi Berkas -->
                <div class="timeline-item">
                    <?php if($berkas): ?>
                        <div class="timeline-icon bg-warning">
                            <i class="bi bi-files"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="fw-bold mb-0">Verifikasi Berkas Fisik</h6>
                            <p class="small text-muted mb-1"><?= date('d M Y, H:i', strtotime($berkas['updated_at'])) ?></p>
                            <div class="alert alert-warning border-0 py-2 small mb-0">
                                Berkas fisik telah dicek oleh petugas. Beberapa berkas telah divalidasi dan disimpan.
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="timeline-icon bg-secondary opacity-50">
                            <i class="bi bi-files"></i>
                        </div>
                        <div class="timeline-content opacity-50">
                            <h6 class="fw-bold mb-0">Belum Verifikasi Berkas</h6>
                            <p class="small text-muted">Tahap ini dilakukan setelah santri dinyatakan Lulus Tes Seleksi.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tahap 4: Menjadi Santri -->
                <div class="timeline-item">
                    <?php if($p['status_seleksi'] == 'Santri Terdaftar'): ?>
                        <div class="timeline-icon bg-success">
                            <i class="bi bi-award-fill"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="fw-bold text-success mb-0">Resmi Menjadi Santri</h6>
                            <p class="small text-muted mb-1">Finalisasi selesai!</p>
                            <div class="alert alert-success border-0 py-2 small mb-0">
                                <i class="bi bi-check-circle-fill me-1"></i> Data telah dipindahkan ke modul Akademik. Santri kini memiliki identitas resmi di Pesantren.
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="timeline-icon bg-secondary opacity-50">
                            <i class="bi bi-award"></i>
                        </div>
                        <div class="timeline-content opacity-50">
                            <h6 class="fw-bold mb-0">Finalisasi Pendaftaran</h6>
                            <p class="small text-muted mb-0">Menunggu seluruh tahap administrasi selesai.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                <a href="<?= base_url('ppdb/pendaftar') ?>" class="btn btn-light rounded-pill px-4 fw-bold">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                </a>
                <a href="<?= base_url('ppdb/berkas/detail/'.$p['id']) ?>" class="btn btn-primary rounded-pill px-4 fw-bold">
                    Cek Berkas Fisik <i class="bi bi-chevron-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
