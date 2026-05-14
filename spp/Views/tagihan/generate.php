<?= $this->extend('layout/main') ?>

<?= $this->section('breadcrumb') ?>
    <li class="breadcrumb-item"><a href="<?= base_url('spp') ?>" class="text-decoration-none">SPP</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('spp/tagihan') ?>" class="text-decoration-none">Tagihan</a></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0">Generate Tagihan Masal</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info rounded-4 border-0 mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    Gunakan fitur ini untuk membuat tagihan secara kolektif sesuai jenis tarif atau pemetaan santri.
                </div>
                <form action="<?= base_url('spp/tagihan/process-generate') ?>" method="post">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Pilih Metode Generate</label>
                        
                        <div class="card border mb-2 p-3 rounded-4 mode-card" id="card-mode1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode" id="mode1" value="single" checked>
                                <label class="form-check-label ms-2" for="mode1">
                                    <div class="fw-bold">Per Jenis Tarif (1 Bulan)</div>
                                    <small class="text-muted">Generate 1 item tarif tertentu untuk semua santri aktif di bulan terpilih.</small>
                                </label>
                            </div>
                        </div>

                        <div class="card border mb-2 p-3 rounded-4 mode-card" id="card-mode2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode" id="mode2" value="mapping">
                                <label class="form-check-label ms-2" for="mode2">
                                    <div class="fw-bold">Berdasarkan Kesepakatan (1 Bulan)</div>
                                    <small class="text-muted">Generate tagihan sesuai item yang sudah dipetakan ke masing-masing santri di bulan terpilih.</small>
                                </label>
                            </div>
                        </div>

                        <div class="card border mb-4 p-3 rounded-4 mode-card" id="card-mode3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode" id="mode3" value="yearly">
                                <label class="form-check-label ms-2" for="mode3">
                                    <div class="fw-bold text-warning">Generate 1 Tahun Penuh (Juli - Juni)</div>
                                    <small class="text-muted">Otomatis membuat tagihan untuk 12 bulan sekaligus sesuai pemetaan masing-masing santri.</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="section-monthly" class="bg-light p-4 rounded-4 mb-4 border">
                        <h6 class="fw-bold mb-3">Pengaturan Bulan & Tahun</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small">Pilih Bulan</label>
                                <select name="bulan" class="form-select rounded-3">
                                    <?php
                                    $months = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ];
                                    foreach ($months as $num => $name):
                                    ?>
                                    <option value="<?= $num ?>" <?= $num == date('n') ? 'selected' : '' ?>><?= $name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Tahun</label>
                                <input type="number" name="tahun" class="form-control rounded-3" value="<?= date('Y') ?>">
                            </div>
                        </div>
                    </div>

                    <div id="section-yearly" class="bg-light p-4 rounded-4 mb-4 border" style="display:none;">
                        <h6 class="fw-bold mb-3 text-warning">Pengaturan Tahun Akademik</h6>
                        <div>
                            <label class="form-label small">Pilih Tahun Ajaran</label>
                            <select name="id_tahun_akademik" class="form-select rounded-3">
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                <?php 
                                $taModel = new \App\Models\TahunAkademikModel();
                                $allTA = $taModel->orderBy('nama_tahun', 'DESC')->findAll();
                                foreach($allTA as $ta): ?>
                                    <option value="<?= $ta['id'] ?>"><?= $ta['nama_tahun'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted mt-2 d-block">Sistem akan membuat tagihan bulanan (Juli-Juni) dan tagihan tahunan sekali bayar.</small>
                        </div>
                    </div>

                    <div id="select-tarif-wrapper" class="mb-4 bg-light p-4 rounded-4 border">
                        <label class="form-label fw-bold small">Pilih Item Tarif</label>
                        <select name="tarif_id" class="form-select rounded-3" id="tarif_id">
                            <option value="">-- Pilih Tarif --</option>
                            <?php foreach ($tarif as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= $t['nama_tarif'] ?> (Rp <?= number_format($t['nominal'], 0, ',', '.') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('spp') ?>" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Generate Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('input[name="mode"]').on('change', function() {
            let mode = $(this).val();
            
            $('.mode-card').removeClass('border-primary bg-light');
            $('#card-' + $(this).attr('id')).addClass('border-primary bg-light');

            if (mode === 'single') {
                $('#section-monthly').slideDown();
                $('#section-yearly').slideUp();
                $('#select-tarif-wrapper').slideDown();
                $('#tarif_id').attr('required', true);
            } else if (mode === 'mapping') {
                $('#section-monthly').slideDown();
                $('#section-yearly').slideUp();
                $('#select-tarif-wrapper').slideUp();
                $('#tarif_id').removeAttr('required');
            } else if (mode === 'yearly') {
                $('#section-monthly').slideUp();
                $('#section-yearly').slideDown();
                $('#select-tarif-wrapper').slideUp();
                $('#tarif_id').removeAttr('required');
            }
        });
        
        // Initial state
        $('#card-mode1').addClass('border-primary bg-light');
        $('#tarif_id').attr('required', true);
    });
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
