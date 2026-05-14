<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center pb-5">
    <div class="col-md-11">
        <!-- Header Section -->
        <div class="text-center mb-5">
            <h2 class="fw-bold text-dark">Workflow Manajemen SPP</h2>
            <p class="text-muted">Panduan lengkap alur kerja administrator dari setup hingga pelaporan</p>
        </div>

        <!-- Horizontal Process Map (Visual Only) -->
        <div class="card border-0 shadow-sm mb-5" style="border-radius: 20px; background: rgba(255,255,255,0.5); backdrop-filter: blur(10px);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center position-relative px-md-5">
                    <div class="text-center position-relative" style="z-index: 2; width: 120px;">
                        <div class="bg-primary text-white rounded-circle shadow-sm mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">1</div>
                        <span class="small fw-bold text-primary">Setup</span>
                    </div>
                    <div class="position-absolute border-top border-2 border-primary border-dashed opacity-25" style="top: 22px; left: 10%; right: 10%; z-index: 1;"></div>
                    <div class="text-center position-relative" style="z-index: 2; width: 120px;">
                        <div class="bg-primary text-white rounded-circle shadow-sm mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">2</div>
                        <span class="small fw-bold text-primary">Mapping</span>
                    </div>
                    <div class="text-center position-relative" style="z-index: 2; width: 120px;">
                        <div class="bg-primary text-white rounded-circle shadow-sm mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">3</div>
                        <span class="small fw-bold text-primary">Billing</span>
                    </div>
                    <div class="text-center position-relative" style="z-index: 2; width: 120px;">
                        <div class="bg-success text-white rounded-circle shadow-sm mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">4</div>
                        <span class="small fw-bold text-success">Payment</span>
                    </div>
                    <div class="text-center position-relative" style="z-index: 2; width: 120px;">
                        <div class="bg-info text-white rounded-circle shadow-sm mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">5</div>
                        <span class="small fw-bold text-info">Report</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Phase 1: Setup & Scholarship -->
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-lg" style="border-radius: 24px;">
                    <div class="card-header bg-primary text-white p-4 border-0" style="border-radius: 24px 24px 0 0;">
                        <div class="d-flex align-items-center">
                            <div class="bg-white bg-opacity-25 rounded-3 p-2 me-3">
                                <i class="bi bi-gear-wide-connected fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">Fase 1: Konfigurasi</h5>
                                <small class="opacity-75">Master Data & Beasiswa</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary"><i class="bi bi-1-circle-fill me-2"></i>Atur Tarif SPP</h6>
                            <p class="small text-muted mb-0">Tentukan kategori tarif (Misal: SPP, Uang Makan) di menu <strong>Tarif SPP</strong>.</p>
                        </div>
                        <hr class="opacity-10">
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary"><i class="bi bi-2-circle-fill me-2"></i>Pemetaan & KJP</h6>
                            <p class="small text-muted mb-0">Hubungkan santri dengan tarif. Untuk penerima <strong>KJP/Beasiswa</strong>, isi kolom 'Potongan' dan 'Keterangan' (misal: "Covered KJP") agar tagihan otomatis terpotong setiap bulan.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Phase 2: Billing & Mass Payment -->
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-lg" style="border-radius: 24px;">
                    <div class="card-header bg-success text-white p-4 border-0" style="border-radius: 24px 24px 0 0;">
                        <div class="d-flex align-items-center">
                            <div class="bg-white bg-opacity-25 rounded-3 p-2 me-3">
                                <i class="bi bi-cash-stack fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">Fase 2: Operasional</h5>
                                <small class="opacity-75">Tagihan & Pelunasan Kolektif</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <h6 class="fw-bold text-success"><i class="bi bi-3-circle-fill me-2"></i>Generate Tagihan</h6>
                            <p class="small text-muted mb-0">Lakukan <strong>Generate Tagihan</strong> setiap bulan. Tagihan akan membawa otomatis data diskon/beasiswa dari hasil pemetaan di Fase 1.</p>
                        </div>
                        <hr class="opacity-10">
                        <div>
                            <h6 class="fw-bold text-success"><i class="bi bi-4-circle-fill me-2"></i>Pelunasan KJP (Massal)</h6>
                            <p class="small text-muted mb-0">Cari tagihan dengan filter "KJP", centang semua, lalu gunakan tombol <strong>Bayar Terpilih</strong> untuk melunasi secara kolektif saat dana beasiswa cair.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Phase 3: Accounting & Reporting -->
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-lg" style="border-radius: 24px;">
                    <div class="card-header bg-dark text-white p-4 border-0" style="border-radius: 24px 24px 0 0;">
                        <div class="d-flex align-items-center">
                            <div class="bg-white bg-opacity-25 rounded-3 p-2 me-3">
                                <i class="bi bi-journal-check fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">Fase 3: Akuntansi</h5>
                                <small class="opacity-75">Jurnal & Eksport Laporan</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark"><i class="bi bi-5-circle-fill me-2"></i>Otomatisasi Jurnal</h6>
                            <p class="small text-muted mb-0">Setiap transaksi pembayaran akan otomatis tercatat ke <strong>Buku Besar (Jurnal Umum)</strong>. Kas bertambah dan Pendapatan tercatat tanpa input ulang.</p>
                        </div>
                        <hr class="opacity-10">
                        <div>
                            <h6 class="fw-bold text-dark"><i class="bi bi-6-circle-fill me-2"></i>Eksport Laporan</h6>
                            <p class="small text-muted mb-0">Gunakan tombol <strong>Eksport</strong> untuk mengunduh laporan dalam format <strong>Excel, Word, atau PDF</strong>. Data akan disaring sesuai filter yang Anda pasang.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Tutorial Section -->
        <div class="card border-0 shadow-sm mt-5 overflow-hidden" style="border-radius: 24px;">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-md-4 bg-light p-4 border-end">
                        <h5 class="fw-bold"><i class="bi bi-question-diamond me-2 text-primary"></i>Tanya Jawab (FAQ)</h5>
                        <div class="mt-4">
                            <div class="mb-3">
                                <h6 class="fw-bold small mb-1">Bagaimana cara mencatat beasiswa?</h6>
                                <p class="small text-muted">Buka 'Pemetaan Tarif', cari santri, klik 'Atur Tarif'. Masukkan nominal potongan dan keterangan 'KJP'.</p>
                            </div>
                            <div class="mb-3">
                                <h6 class="fw-bold small mb-1">Kenapa tagihan berstatus 'Belum Lunas' padahal dicover KJP?</h6>
                                <p class="small text-muted">Status tetap 'Belum Lunas' sebagai piutang sekolah. Tagihan akan lunas setelah dana KJP cair dan diproses via 'Bayar Terpilih'.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 p-4">
                        <h5 class="fw-bold mb-4"><i class="bi bi-info-circle me-2 text-success"></i>Tips Penggunaan Profesional</h5>
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item border-0 ps-0"><i class="bi bi-check2-all me-2 text-success"></i>Gunakan filter <strong>Keyword KJP</strong> di Data Tagihan untuk rekapitulasi cepat.</li>
                            <li class="list-group-item border-0 ps-0"><i class="bi bi-check2-all me-2 text-success"></i>Ekspor data ke <strong>Excel</strong> jika ingin melakukan perhitungan statistik mandiri.</li>
                            <li class="list-group-item border-0 ps-0"><i class="bi bi-check2-all me-2 text-success"></i>Selalu periksa <strong>Buku Besar</strong> di modul Keuangan untuk memastikan saldo kas sesuai fisik.</li>
                            <li class="list-group-item border-0 ps-0"><i class="bi bi-check2-all me-2 text-success"></i>Gunakan metode pembayaran <strong>'Beasiswa/KJP'</strong> agar terpisah dari transaksi tunai wali santri.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Section -->
        <div class="mt-5 text-center">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                <h5 class="fw-bold mb-3">Butuh bantuan lebih lanjut?</h5>
                <div class="d-flex justify-content-center gap-3">
                    <a href="<?= base_url('spp') ?>" class="btn btn-primary px-4 py-2 shadow-sm" style="border-radius: 12px;">
                        <i class="bi bi-house-door me-2"></i> Ke Dashboard
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 12px;">
                        <i class="bi bi-printer me-2"></i> Cetak Panduan Ini
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-dashed { border-style: dashed !important; }
    .card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important; }
</style>
<?= $this->endSection() ?>
