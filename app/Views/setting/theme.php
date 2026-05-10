<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-palette-fill me-2 text-primary"></i> Pengaturan Tema Visual</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('setting/update') ?>" method="POST">
                    <!-- Keep existing profile data hidden so they don't get overwritten with null if not handled correctly -->
                    <!-- But our controller update() handles it well by getting current data first -->
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Mode Tampilan</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="theme_mode" id="theme-light" value="light" <?= ($setting['theme_mode'] ?? 'light') == 'light' ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-primary w-100 py-3 rounded-4" for="theme-light">
                                        <i class="bi bi-sun fs-4 d-block mb-1"></i> Light Mode
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="theme_mode" id="theme-dark" value="dark" <?= ($setting['theme_mode'] ?? 'light') == 'dark' ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-primary w-100 py-3 rounded-4" for="theme-dark">
                                        <i class="bi bi-moon-stars fs-4 d-block mb-1"></i> Dark Mode
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Warna Utama (Aksen)</label>
                            <input type="color" name="theme_primary" class="form-control form-control-color w-100 border-0 shadow-sm rounded-4" style="height: 65px;" value="<?= $setting['theme_primary'] ?? '#1a5928' ?>" title="Pilih warna utama">
                            <div class="form-text small mt-2">Warna ini akan digunakan untuk tombol, sidebar, dan aksen navigasi.</div>
                        </div>

                        <div class="col-12 mt-4 pt-3 border-top text-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow fw-bold">
                                <i class="bi bi-check-lg me-1"></i> Terapkan Tema
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="alert alert-info border-0 shadow-sm mt-4 rounded-4">
            <div class="d-flex gap-3">
                <i class="bi bi-info-circle-fill fs-4"></i>
                <div>
                    <h6 class="fw-bold mb-1">Tips Kustomisasi</h6>
                    <p class="small mb-0">Pilihlah warna yang memiliki kontras yang baik dengan teks putih agar navigasi tetap mudah dibaca.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
