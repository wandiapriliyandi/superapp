<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-4 rounded-4 mb-4">
            <div class="mb-3">
                <div class="bg-primary bg-opacity-10 rounded shadow-sm overflow-hidden mx-auto" style="width: 150px; height: 180px;">
                    <?php if($k['foto']): ?>
                        <img src="<?= base_url('uploads/karyawan/'.$k['foto']) ?>" class="w-100 h-100" style="object-fit: cover;">
                    <?php else: ?>
                        <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                            <i class="bi bi-person-badge text-primary fs-1"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <h5 class="fw-bold mb-0"><?= $k['nama_lengkap'] ?></h5>
            <p class="text-muted small mb-3"><?= $k['jabatan'] ?></p>
            <span class="badge bg-primary rounded-pill px-3"><?= $k['status_pegawai'] ?></span>
            
            <hr class="my-4">
            
            <div class="text-start">
                <div class="mb-3">
                    <label class="small text-muted d-block">Nomor HP</label>
                    <span class="fw-semibold"><?= $k['no_hp'] ?: '-' ?></span>
                </div>
                <div class="mb-3">
                    <label class="small text-muted d-block">Email</label>
                    <span class="fw-semibold text-primary"><?= $k['email'] ?: '-' ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-0">Informasi Lengkap Pegawai</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="small text-muted d-block">NIP</label>
                        <span class="fs-5 fw-semibold"><?= $k['nip'] ?: '-' ?></span>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted d-block">NIK</label>
                        <span class="fs-5 fw-semibold"><?= $k['nik'] ?: '-' ?></span>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted d-block">Tempat, Tanggal Lahir</label>
                        <span class="fw-semibold"><?= $k['tempat_lahir'] ?>, <?= $k['tanggal_lahir'] ? date('d F Y', strtotime($k['tanggal_lahir'])) : '-' ?></span>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted d-block">Jenis Kelamin</label>
                        <span class="fw-semibold"><?= $k['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></span>
                    </div>
                    <div class="col-md-12">
                        <label class="small text-muted d-block">Alamat</label>
                        <span class="fw-semibold"><?= $k['alamat'] ?: '-' ?></span>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted d-block">Pendidikan Terakhir</label>
                        <span class="fw-semibold"><?= $k['pendidikan_terakhir'] ?: '-' ?></span>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted d-block">Tanggal Masuk</label>
                        <span class="fw-semibold"><?= $k['tanggal_masuk'] ? date('d F Y', strtotime($k['tanggal_masuk'])) : '-' ?></span>
                    </div>
                </div>
                
                <div class="mt-5">
                    <a href="<?= base_url('kepegawaian/karyawan') ?>" class="btn btn-light border px-4">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
