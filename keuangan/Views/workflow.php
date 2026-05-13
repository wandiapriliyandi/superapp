<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center pb-5">
    <div class="col-md-11">
        <!-- Header Section -->
        <div class="text-center mb-5">
            <h2 class="fw-bold text-dark">Workflow Manajemen Keuangan</h2>
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
            <!-- Phase 1: Setup -->
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-lg" style="border-radius: 24px;">
                    <div class="card-header bg-primary text-white p-4 border-0" style="border-radius: 24px 24px 0 0;">
                        <div class="d-flex align-items-center">
                            <div class="bg-white bg-opacity-25 rounded-3 p-2 me-3">
                                <i class="bi bi-gear-wide-connected fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">Fase 1: Konfigurasi</h5>
                                <small class="opacity-75">Pengaturan Dasar Master Data</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary"><i class="bi bi-1-circle-fill me-2"></i>Atur Tarif SPP</h6>
                            <p class="small text-muted mb-0">Tentukan kategori tarif (Misal: SPP Bulanan, Uang Makan) dan nominalnya di menu <strong>Tarif SPP</strong>.</p>
                        </div>
                        <hr class="opacity-10">
                        <div>
                            <h6 class="fw-bold text-primary"><i class="bi bi-2-circle-fill me-2"></i>Pemetaan Santri</h6>
                            <p class="small text-muted mb-0">Hubungkan santri dengan tarif yang sesuai melalui menu <strong>Pemetaan Tarif</strong>. Di sini Anda menentukan siapa membayar berapa.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Phase 2: Billing & Payment -->
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-lg" style="border-radius: 24px;">
                    <div class="card-header bg-success text-white p-4 border-0" style="border-radius: 24px 24px 0 0;">
                        <div class="d-flex align-items-center">
                            <div class="bg-white bg-opacity-25 rounded-3 p-2 me-3">
                                <i class="bi bi-cash-stack fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">Fase 2: Operasional</h5>
                                <small class="opacity-75">Siklus Bulanan & Transaksi</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <h6 class="fw-bold text-success"><i class="bi bi-3-circle-fill me-2"></i>Generate Tagihan</h6>
                            <p class="small text-muted mb-0">Setiap awal bulan, lakukan <strong>Generate Tagihan</strong> agar tagihan muncul di data santri berdasarkan pemetaan yang sudah dibuat.</p>
                        </div>
                        <hr class="opacity-10">
                        <div>
                            <h6 class="fw-bold text-success"><i class="bi bi-4-circle-fill me-2"></i>Proses Bayar SPP</h6>
                            <p class="small text-muted mb-0">Admin menerima uang melalui menu <strong>Bayar SPP</strong>, memilih bulan, memasukkan nominal, dan mencetak kwitansi transaksi.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Phase 3: Monitoring -->
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-lg" style="border-radius: 24px;">
                    <div class="card-header bg-info text-white p-4 border-0" style="border-radius: 24px 24px 0 0;">
                        <div class="d-flex align-items-center">
                            <div class="bg-white bg-opacity-25 rounded-3 p-2 me-3">
                                <i class="bi bi-graph-up-arrow fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">Fase 3: Monitoring</h5>
                                <small class="opacity-75">Pelaporan & Riwayat</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <h6 class="fw-bold text-info"><i class="bi bi-5-circle-fill me-2"></i>Riwayat Transaksi</h6>
                            <p class="small text-muted mb-0">Pantau semua uang masuk secara real-time di menu <strong>Riwayat Transaksi</strong> atau <strong>Riwayat Bayar</strong>.</p>
                        </div>
                        <hr class="opacity-10">
                        <div>
                            <h6 class="fw-bold text-info"><i class="bi bi-pie-chart-fill me-2"></i>Dashboard Laporan</h6>
                            <p class="small text-muted mb-0">Analisis total tunggakan vs total terbayar pada <strong>Dashboard Keuangan</strong> untuk bahan evaluasi pimpinan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Section -->
        <div class="mt-5 text-center">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                <h5 class="fw-bold mb-3">Butuh bantuan lebih lanjut?</h5>
                <div class="d-flex justify-content-center gap-3">
                    <a href="<?= base_url('keuangan') ?>" class="btn btn-primary px-4 py-2 shadow-sm" style="border-radius: 12px;">
                        <i class="bi bi-house-door me-2"></i> Ke Dashboard
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 12px;">
                        <i class="bi bi-printer me-2"></i> Cetak Panduan
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
