<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-primary bg-gradient text-white p-4 border-0">
                <div class="d-flex align-items-center">
                    <a href="<?= base_url('e-learning/materi') ?>" class="btn btn-sm btn-light text-primary rounded-circle me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h4 class="fw-bold mb-0">Unggah Materi Belajar Baru</h4>
                        <p class="small text-white-50 mb-0">Tambahkan bahan ajar digital, dokumen, atau rujukan video YouTube.</p>
                    </div>
                </div>
            </div>
            
            <form action="<?= base_url('e-learning/materi/save') ?>" method="post" enctype="multipart/form-data" class="card-body p-4 p-md-5">
                <div class="mb-4">
                    <label class="form-label fw-bold">Judul Materi Pembelajaran <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control form-control-lg rounded-3" placeholder="Contoh: Bab 1 Thaharah & Dalil-dalilnya" required>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Mata Pelajaran <span class="text-danger">*</span></label>
                        <select name="mata_pelajaran" class="form-select form-select-lg rounded-3" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            <option value="Fiqih">Fiqih</option>
                            <option value="Bahasa Arab">Bahasa Arab</option>
                            <option value="Sejarah Islam">Sejarah Islam</option>
                            <option value="Aqidah Akhlak">Aqidah Akhlak</option>
                            <option value="Al-Qur'an Hadits">Al-Qur'an Hadits</option>
                            <option value="Umum / Tematik">Umum / Tematik</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Kelas / Sasaran Tingkatan <span class="text-danger">*</span></label>
                        <select name="kelas" class="form-select form-select-lg rounded-3" required>
                            <option value="">-- Pilih Sasaran Kelas --</option>
                            <option value="Kelas 10">Kelas 10</option>
                            <option value="Kelas 11">Kelas 11</option>
                            <option value="Kelas 12">Kelas 12</option>
                            <option value="Semua Tingkatan">Semua Tingkatan</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Guru Pengampu</label>
                    <input type="text" name="guru_pengampu" class="form-control rounded-3" placeholder="Nama Guru / Pengajar (Opsional)">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Tautan Video Edukasi (YouTube / Drive) <span class="text-muted fw-normal">(Opsional)</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-youtube text-danger"></i></span>
                        <input type="url" name="link_video" class="form-control border-start-0" placeholder="https://www.youtube.com/watch?v=...">
                    </div>
                    <div class="form-text">Masukkan URL lengkap video untuk ditonton langsung oleh santri.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Unggah Dokumen Materi <span class="text-muted fw-normal">(Opsional)</span></label>
                    <input type="file" name="file_materi" class="form-control" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx">
                    <div class="form-text">Mendukung file berformat: PDF, Word, PowerPoint, atau Excel.</div>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bold">Deskripsi & Rangkuman Singkat</label>
                    <textarea name="deskripsi" rows="4" class="form-control rounded-3" placeholder="Tuliskan petunjuk pembelajaran atau intisari materi di sini..."></textarea>
                </div>

                <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                    <a href="<?= base_url('e-learning/materi') ?>" class="btn btn-light px-4 py-2 fw-semibold rounded-pill">Batal</a>
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold rounded-pill shadow-sm">
                        <i class="bi bi-check-circle me-2"></i>Simpan & Publikasikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
