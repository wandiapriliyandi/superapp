<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 p-4">
                <div class="d-flex align-items-center">
                    <a href="<?= base_url('perpustakaan') ?>" class="btn btn-light rounded-circle me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h5 class="fw-bold mb-0">Pengaturan Google Drive</h5>
                </div>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="alert <?= $is_configured ? 'alert-success' : 'alert-warning' ?> border-0 rounded-4 d-flex align-items-center mb-4">
                    <i class="bi <?= $is_configured ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> fs-3 me-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Status: <?= $is_configured ? 'Terkonfigurasi' : 'Belum Konfigurasi' ?></h6>
                        <p class="mb-0 small"><?= $is_configured ? 'Sistem siap mengunggah file otomatis ke Google Drive.' : 'Silakan upload file kredensial JSON Service Account untuk mengaktifkan fitur ini.' ?></p>
                    </div>
                </div>

                <form action="<?= base_url('perpustakaan/simpan-konfigurasi') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="p-5 border-2 border-dashed rounded-4 text-center bg-light mb-4">
                        <i class="bi bi-filetype-json fs-1 text-primary mb-3"></i>
                        <h6 class="fw-bold">Upload Google Service Account (JSON)</h6>
                        <p class="small text-muted mb-4">Anda bisa mendapatkan file ini dari Google Cloud Console</p>
                        
                        <input type="file" name="google_json" id="google_json" class="d-none" accept=".json" required onchange="this.form.submit()">
                        <label for="google_json" class="btn btn-primary rounded-pill px-5 py-2 fw-bold">
                            Pilih File JSON
                        </label>
                    </div>
                </form>

                <div class="mt-5">
                    <h6 class="fw-bold"><i class="bi bi-info-circle me-2"></i>Cara mendapatkan Kredensial:</h6>
                    <ol class="small text-muted mt-3">
                        <li>Buka <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a>.</li>
                        <li>Buat Proyek baru (atau gunakan yang sudah ada).</li>
                        <li>Aktifkan <strong>Google Drive API</strong> di menu Library.</li>
                        <li>Masuk ke menu <strong>Credentials</strong> > <strong>Create Credentials</strong> > <strong>Service Account</strong>.</li>
                        <li>Setelah dibuat, masuk ke akun tersebut, pilih tab <strong>Keys</strong> > <strong>Add Key</strong> > <strong>Create New Key (JSON)</strong>.</li>
                        <li>File JSON akan terunduh otomatis. Upload file tersebut di sini.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-dashed { border-style: dashed !important; border-color: #dee2e6 !important; }
</style>
<?= $this->endSection() ?>
