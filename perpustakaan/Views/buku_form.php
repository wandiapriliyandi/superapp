<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 p-4">
                <div class="d-flex align-items-center">
                    <a href="<?= base_url('perpustakaan/list/' . strtolower($lokasi)) ?>" class="btn btn-light rounded-circle me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h5 class="fw-bold mb-0"><?= $title ?></h5>
                </div>
            </div>
            <div class="card-body p-4 pt-0">
                <form action="<?= base_url('perpustakaan/simpan') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="lokasi" value="<?= $lokasi ?>">
                    
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="p-4 bg-light rounded-4 text-center">
                                <div class="mb-3">
                                    <i class="bi bi-image fs-1 text-muted opacity-50"></i>
                                </div>
                                <h6 class="fw-bold mb-1">Cover Buku</h6>
                                <p class="small text-muted mb-3">Format JPG/PNG, Max 2MB</p>
                                <input type="file" name="cover" class="form-control form-control-sm rounded-pill" accept="image/*">
                            </div>

                            <?php if ($lokasi == 'Digital'): ?>
                            <div class="p-4 bg-dark bg-opacity-10 rounded-4 text-center mt-4 border border-dark border-opacity-10">
                                <div class="mb-3 text-dark">
                                    <i class="bi bi-file-earmark-pdf-fill fs-1"></i>
                                </div>
                                <h6 class="fw-bold mb-1">File Digital</h6>
                                <div class="nav nav-pills mb-3 justify-content-center" id="pills-tab" role="tablist">
                                    <button class="nav-link active btn-sm py-1 px-3 me-2 small rounded-pill" id="pills-upload-tab" data-bs-toggle="pill" data-bs-target="#pills-upload" type="button">Upload PDF</button>
                                    <button class="nav-link btn-sm py-1 px-3 small rounded-pill" id="pills-link-tab" data-bs-toggle="pill" data-bs-target="#pills-link" type="button">Google Drive Link</button>
                                </div>
                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="pills-upload">
                                        <input type="file" name="file_digital" class="form-control form-control-sm rounded-pill" accept=".pdf">
                                        <p class="small text-muted mt-2 mb-0">Format PDF, Max 20MB</p>
                                    </div>
                                    <div class="tab-pane fade" id="pills-link">
                                        <input type="url" name="link_eksternal" class="form-control form-control-sm rounded-pill" placeholder="https://drive.google.com/...">
                                        <p class="small text-muted mt-2 mb-0">Paste link sharing file dari G-Drive</p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Kode Buku</label>
                                    <input type="text" name="kode_buku" class="form-control rounded-3" placeholder="B-001" required>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold small">Judul Buku</label>
                                    <input type="text" name="judul" class="form-control rounded-3" placeholder="Contoh: Fathul Qarib" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Pengarang</label>
                                    <input type="text" name="pengarang" class="form-control rounded-3" placeholder="Nama penulis">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Penerbit</label>
                                    <input type="text" name="penerbit" class="form-control rounded-3" placeholder="Nama penerbit">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Tahun Terbit</label>
                                    <input type="number" name="tahun_terbit" class="form-control rounded-3" placeholder="YYYY" min="1900" max="<?= date('Y') ?>">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold small">Kategori</label>
                                    <select name="kategori" class="form-select rounded-3">
                                        <option value="Agama">Agama / Kitab</option>
                                        <option value="Sains">Sains & Teknologi</option>
                                        <option value="Bahasa">Bahasa</option>
                                        <option value="Sastra">Sastra & Novel</option>
                                        <option value="Umum">Umum</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold small">Stok</label>
                                    <input type="number" name="stok" class="form-control rounded-3" value="1" min="0">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small">Deskripsi Singkat</label>
                                    <textarea name="deskripsi" class="form-control rounded-3" rows="4" placeholder="Sinopsis atau info tambahan..."></textarea>
                                </div>
                            </div>

                            <div class="mt-5 d-flex gap-2">
                                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">Simpan Buku</button>
                                <button type="reset" class="btn btn-light rounded-pill px-4">Reset</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
