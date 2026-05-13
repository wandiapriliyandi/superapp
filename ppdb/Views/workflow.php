<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center pb-5">
    <div class="col-md-11">
        <!-- Header Section -->
        <div class="text-center mb-5">
            <h2 class="fw-bold text-dark">Workflow Penerimaan Peserta Didik Baru (PPDB)</h2>
            <p class="text-muted">Panduan alur kerja admin dari persiapan hingga pendaftaran ulang santri baru</p>
        </div>

        <!-- Process Map -->
        <div class="card border-0 shadow-sm mb-5" style="border-radius: 20px; background: rgba(255,255,255,0.5); backdrop-filter: blur(10px);">
            <div class="card-body p-4">
                <div class="row text-center g-0 position-relative">
                    <div class="col">
                        <div class="bg-primary text-white rounded-pill shadow-sm mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">1</div>
                        <span class="x-small fw-bold">Persiapan</span>
                    </div>
                    <div class="col">
                        <div class="bg-primary text-white rounded-pill shadow-sm mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">2</div>
                        <span class="x-small fw-bold">Pendaftaran</span>
                    </div>
                    <div class="col">
                        <div class="bg-primary text-white rounded-pill shadow-sm mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">3</div>
                        <span class="x-small fw-bold">Seleksi</span>
                    </div>
                    <div class="col">
                        <div class="bg-success text-white rounded-pill shadow-sm mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">4</div>
                        <span class="x-small fw-bold">Kelulusan</span>
                    </div>
                    <div class="col">
                        <div class="bg-success text-white rounded-pill shadow-sm mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">5</div>
                        <span class="x-small fw-bold">Final</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Phase 1: Setup -->
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-lg" style="border-radius: 24px;">
                    <div class="card-header bg-primary text-white p-4 border-0" style="border-radius: 24px 24px 0 0;">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-calendar-check me-2"></i>Fase 1: Persiapan</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary">1. Atur Gelombang</h6>
                            <p class="small text-muted mb-0">Buka menu <strong>Gelombang</strong> untuk menentukan periode pendaftaran dan kuota santri.</p>
                        </div>
                        <hr class="opacity-10">
                        <div>
                            <h6 class="fw-bold text-primary">2. Syarat Berkas</h6>
                            <p class="small text-muted mb-0">Tentukan daftar dokumen yang harus dibawa pendaftar di menu <strong>Syarat Berkas</strong>.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Phase 2: Data & Jadwal Tes -->
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-lg" style="border-radius: 24px;">
                    <div class="card-header bg-info text-white p-4 border-0" style="border-radius: 24px 24px 0 0;">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-clipboard-data me-2"></i>Fase 2: Pendaftaran & Tes</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <h6 class="fw-bold text-info">3. Input Pendaftar</h6>
                            <p class="small text-muted mb-0">Gunakan <strong>Tambah Pendaftar</strong> untuk entry data manual atau biarkan pendaftar mengisi mandiri via landing page.</p>
                        </div>
                        <hr class="opacity-10">
                        <div>
                            <h6 class="fw-bold text-info">4. Penjadwalan Tes</h6>
                            <p class="small text-muted mb-0">Kelompokkan pendaftar ke dalam jadwal tes seleksi di menu <strong>Jadwal Tes</strong> untuk diuji.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Phase 3: Verifikasi & Final -->
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-lg" style="border-radius: 24px;">
                    <div class="card-header bg-success text-white p-4 border-0" style="border-radius: 24px 24px 0 0;">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-award-fill me-2"></i>Fase 3: Verifikasi & Final</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <h6 class="fw-bold text-success">5. Verifikasi Berkas</h6>
                            <p class="small text-muted mb-0">Cek kelengkapan fisik dokumen pendaftar melalui menu <strong>Verifikasi Berkas</strong> sebagai tahapan akhir validasi.</p>
                        </div>
                        <hr class="opacity-10">
                        <div>
                            <h6 class="fw-bold text-success">6. Penetapan & Daftar Ulang</h6>
                            <p class="small text-muted mb-0">Tentukan status <strong>Lulus</strong> di daftar pendaftar, lalu arahkan untuk pembayaran biaya masuk.</p>
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
                    <a href="<?= base_url('ppdb/dashboard') ?>" class="btn btn-primary px-4 py-2 shadow-sm" style="border-radius: 12px;">
                        <i class="bi bi-house-door me-2"></i> Ke Dashboard PPDB
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
    .x-small { font-size: 0.75rem; }
    .card { transition: transform 0.3s; }
    .card:hover { transform: translateY(-5px); }
</style>
<?= $this->endSection() ?>
