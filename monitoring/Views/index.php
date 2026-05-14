<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h3 class="fw-bold text-dark mb-1">Laporan & Analisis Eksekutif</h3>
                <p class="text-muted">Pusat pemantauan seluruh unit kerja pesantren.</p>
            </div>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-white shadow-sm rounded-pill px-4">
                    <i class="bi bi-printer me-2"></i> Cetak Laporan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Stats Overview -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white p-2">
            <div class="card-body">
                <h6 class="opacity-75 mb-2">Total Santri Aktif</h6>
                <div class="d-flex align-items-center">
                    <h2 class="fw-bold mb-0 me-3"><?= number_format($stats['total_santri']) ?></h2>
                    <span class="badge bg-white bg-opacity-25 rounded-pill small">+2% bln ini</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-success text-white p-2">
            <div class="card-body">
                <h6 class="opacity-75 mb-2">Pendaftar PPDB</h6>
                <div class="d-flex align-items-center">
                    <h2 class="fw-bold mb-0 me-3"><?= number_format($stats['pendaftar_ppdb']) ?></h2>
                    <span class="badge bg-white bg-opacity-25 rounded-pill small">Target 500</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-warning text-dark p-2">
            <div class="card-body">
                <h6 class="opacity-75 mb-2">Kas & Setara Kas</h6>
                <div class="d-flex align-items-center">
                    <h4 class="fw-bold mb-0 me-2">Rp <?= number_format($stats['total_kas'], 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-info text-white p-2">
            <div class="card-body">
                <h6 class="opacity-75 mb-2">Total Pegawai</h6>
                <div class="d-flex align-items-center">
                    <h2 class="fw-bold mb-0"><?= number_format($stats['total_pegawai']) ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Chart 1: Pendaftaran Trend -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 p-4 pb-0">
                <h6 class="fw-bold mb-0">Tren Pendaftaran Santri Baru</h6>
            </div>
            <div class="card-body p-4">
                <canvas id="ppdbChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart 2: Kesehatan -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 p-4 pb-0">
                <h6 class="fw-bold mb-0">Analisis Kesehatan (Poskestren)</h6>
            </div>
            <div class="card-body p-4">
                <canvas id="healthChart" style="max-height: 300px;"></canvas>
                <div class="mt-3 small text-muted text-center">
                    5 Diagnosa Penyakit Terbanyak
                </div>
            </div>
        </div>
    </div>

    <!-- Chart 3: Keuangan -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 p-4 pb-0">
                <h6 class="fw-bold mb-0">Performa Keuangan (Cash Flow)</h6>
            </div>
            <div class="card-body p-4">
                <canvas id="financeChart" style="max-height: 350px;"></canvas>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. PPDB Chart
    const ppdbCtx = document.getElementById('ppdbChart').getContext('2d');
    new Chart(ppdbCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($chart_pendaftaran, 'bulan')) ?>,
            datasets: [{
                label: 'Jumlah Pendaftar',
                data: <?= json_encode(array_column($chart_pendaftaran, 'jumlah')) ?>,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#0d6efd'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // 2. Health Chart (Doughnut)
    const healthCtx = document.getElementById('healthChart').getContext('2d');
    new Chart(healthCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($chart_kesehatan, 'diagnosa')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($chart_kesehatan, 'jumlah')) ?>,
                backgroundColor: ['#f43f5e', '#fbbf24', '#10b981', '#3b82f6', '#8b5cf6'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 10 } } }
        }
    });

    // 3. Finance Chart
    const financeCtx = document.getElementById('financeChart').getContext('2d');
    new Chart(financeCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($chart_keuangan, 'bulan')) ?>,
            datasets: [
                {
                    label: 'Pemasukan',
                    data: <?= json_encode(array_column($chart_keuangan, 'masuk')) ?>,
                    backgroundColor: '#10b981',
                    borderRadius: 5
                },
                {
                    label: 'Pengeluaran',
                    data: <?= json_encode(array_column($chart_keuangan, 'keluar')) ?>,
                    backgroundColor: '#f43f5e',
                    borderRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
<?= $this->endSection() ?>
