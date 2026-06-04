<?php 
/** @var array $santri */
/** @var string $title */
/** @var \CodeIgniter\Validation\Validation $validation */
?>
<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="bg-primary p-4 text-white">
                <h4 class="mb-0 fw-bold"><i class="bi bi-file-earmark-plus me-2"></i> <?= $title ?></h4>
                <p class="mb-0 opacity-75 small">Lengkapi formulir di bawah ini untuk mengajukan izin santri.</p>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('perijinan/simpan') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase tracking-wider">Pilih Santri</label>
                        <select name="nisn[]" class="form-select form-select-lg rounded-3 select2 <?= $validation->hasError('nisn') ? 'is-invalid' : '' ?>" id="nisn" multiple="multiple" data-placeholder="-- Cari & Pilih Nama-Nama Santri --">
                            <?php foreach ($santri as $s) : ?>
                                <option value="<?= $s['nisn'] ?>" <?= (old('nisn') && in_array($s['nisn'], (array)old('nisn'))) ? 'selected' : '' ?>>
                                    <?= $s['nama_lengkap'] ?> (<?= $s['nis'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"><?= $validation->getError('nisn') ?></div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase tracking-wider">Jenis Izin</label>
                            <select name="jenis_izin" class="form-select rounded-3 <?= $validation->hasError('jenis_izin') ? 'is-invalid' : '' ?>">
                                <option value="Keluar Lingkungan" <?= old('jenis_izin') == 'Keluar Lingkungan' ? 'selected' : '' ?>>Keluar Lingkungan (Singkat)</option>
                                <option value="Pulang" <?= old('jenis_izin') == 'Pulang' ? 'selected' : '' ?>>Pulang (Bermalam)</option>
                                <option value="Sakit" <?= old('jenis_izin') == 'Sakit' ? 'selected' : '' ?>>Sakit / Berobat</option>
                                <option value="Lainnya" <?= old('jenis_izin') == 'Lainnya' ? 'selected' : '' ?>>Kepentingan Lainnya</option>
                            </select>
                            <div class="invalid-feedback"><?= $validation->getError('jenis_izin') ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase tracking-wider">Alasan Izin</label>
                            <input type="text" name="alasan" class="form-control rounded-3" placeholder="Contoh: Menghadiri pernikahan kakak" value="<?= old('alasan') ?>">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase tracking-wider">Waktu Mulai</label>
                            <input type="datetime-local" name="tanggal_mulai" class="form-control rounded-3 <?= $validation->hasError('tanggal_mulai') ? 'is-invalid' : '' ?>" value="<?= old('tanggal_mulai', date('Y-m-d\TH:i')) ?>">
                            <div class="invalid-feedback"><?= $validation->getError('tanggal_mulai') ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase tracking-wider">Waktu Selesai (ESTIMASI)</label>
                            <input type="datetime-local" name="tanggal_selesai" class="form-control rounded-3 <?= $validation->hasError('tanggal_selesai') ? 'is-invalid' : '' ?>" value="<?= old('tanggal_selesai') ?>">
                            <div class="invalid-feedback"><?= $validation->getError('tanggal_selesai') ?></div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="<?= base_url('perijinan') ?>" class="btn btn-light px-4 rounded-pill">Batal</a>
                        <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm">Kirim Pengajuan <i class="bi bi-send-fill ms-2"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="alert alert-info mt-4 border-0 shadow-sm rounded-4">
            <div class="d-flex">
                <div class="me-3 fs-3"><i class="bi bi-info-circle-fill text-info"></i></div>
                <div>
                    <h6 class="fw-bold mb-1">Informasi Penting</h6>
                    <ul class="mb-0 small ps-3">
                        <li>Pengajuan akan ditinjau oleh bagian keamanan/kesantrian.</li>
                        <li>Pastikan santri telah menyelesaikan kewajibannya sebelum berangkat.</li>
                        <li>Waktu kembali harus sesuai dengan yang diajukan untuk menghindari poin pelanggaran.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .tracking-wider { letter-spacing: 0.05em; }
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#nisn').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Cari & Pilih Nama-Nama Santri --',
            closeOnSelect: false
        });
    });
</script>
<?= $this->endSection() ?>
