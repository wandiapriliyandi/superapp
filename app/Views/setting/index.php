<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-gear-fill me-2 text-primary"></i> Pengaturan Profil & Aplikasi</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('setting/update') ?>" method="POST" enctype="multipart/form-data">
                    <div class="row g-4">
                        <!-- Profil Pesantren Section -->
                        <div class="col-12">
                            <h6 class="fw-bold border-bottom pb-2 mb-3">Profil Pesantren</h6>
                            <div class="row">
                                <div class="col-md-3 text-center mb-3">
                                    <?php 
                                        $logo = base_url('assets/img/logo.png');
                                        if($setting && $setting['app_logo'] && file_exists(FCPATH . 'uploads/img/' . $setting['app_logo'])) {
                                            $logo = base_url('uploads/img/' . $setting['app_logo']);
                                        }
                                    ?>
                                    <img src="<?= $logo ?>" class="img-thumbnail rounded-circle shadow-sm mb-2" style="width: 120px; height: 120px; object-fit: cover;" alt="Logo">
                                    <input type="file" name="app_logo" class="form-control form-control-sm border-0 shadow-sm mt-2">
                                    <div class="small text-muted mt-1">Upload Logo (.png, .jpg)</div>
                                </div>
                                <div class="col-md-9">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-primary">Nama Aplikasi (Branding)</label>
                                        <input type="text" name="app_name" class="form-control border-0 shadow-sm fw-bold" value="<?= $setting['app_name'] ?? '' ?>" placeholder="Contoh: SIPESADIG" required>
                                        <div class="form-text small">Nama ini akan muncul di judul browser dan sidebar.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Nama Pesantren</label>
                                        <input type="text" name="pesantren_name" class="form-control border-0 shadow-sm" value="<?= $setting['pesantren_name'] ?? '' ?>" placeholder="Contoh: Pesantren Modern Al-Hikmah" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Alamat Lengkap</label>
                                        <textarea name="alamat" class="form-control border-0 shadow-sm" rows="3" placeholder="Alamat lengkap pesantren..."><?= $setting['alamat'] ?? '' ?></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label small fw-bold">Nomor Telepon</label>
                                            <input type="text" name="telepon" class="form-control border-0 shadow-sm" value="<?= $setting['telepon'] ?? '' ?>" placeholder="(021) xxxxx">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label small fw-bold">Email Pesantren</label>
                                            <input type="email" name="email" class="form-control border-0 shadow-sm" value="<?= $setting['email'] ?? '' ?>" placeholder="info@pesantren.ac.id">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pengaturan Aplikasi Section (DIHAPUS KARENA PINDAH MENU) -->
                        <div class="col-12 pt-3 border-top text-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow fw-bold">
                                <i class="bi bi-save me-1"></i> Simpan Profil Pesantren
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
