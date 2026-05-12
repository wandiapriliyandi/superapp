<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header text-white p-4 border-0" style="background-color: #6610f2;">
                <div class="d-flex align-items-center">
                    <a href="<?= base_url('e-learning/ujian') ?>" class="btn btn-sm btn-light rounded-circle me-3" style="color: #6610f2;">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h4 class="fw-bold mb-0">Buat Ujian / Tugas Online Baru</h4>
                        <p class="small text-white-50 mb-0">Atur parameter dan rentang waktu akses pengerjaan soal bagi santri.</p>
                    </div>
                </div>
            </div>
            
            <form action="<?= base_url('e-learning/ujian/save') ?>" method="post" class="card-body p-4 p-md-5">
                <div class="mb-4">
                    <label class="form-label fw-bold">Judul Ujian / Kuis / Tugas <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control form-control-lg rounded-3" placeholder="Contoh: Ulangan Harian Nahwu Shorof Bab 1" required>
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
                        <label class="form-label fw-bold">Sasaran Kelas <span class="text-danger">*</span></label>
                        <select name="kelas" class="form-select form-select-lg rounded-3" required>
                            <option value="">-- Pilih Kelas --</option>
                            <option value="Kelas 10">Kelas 10</option>
                            <option value="Kelas 11">Kelas 11</option>
                            <option value="Kelas 12">Kelas 12</option>
                            <option value="Semua Tingkatan">Semua Tingkatan</option>
                        </select>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Durasi Pengerjaan</label>
                        <div class="input-group">
                            <input type="number" name="durasi_menit" class="form-control" value="60" min="5" max="300" required>
                            <span class="input-group-text bg-light">Menit</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Waktu Mulai Akses</label>
                        <input type="datetime-local" name="tgl_mulai" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Waktu Ditutup</label>
                        <input type="datetime-local" name="tgl_selesai" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime('+3 days')) ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Status Publikasi</label>
                    <select name="status" class="form-select rounded-3">
                        <option value="Aktif">Aktif (Langsung bisa diakses sesuai jadwal)</option>
                        <option value="Draf">Draf (Disembunyikan sementara)</option>
                        <option value="Selesai">Selesai (Ditutup)</option>
                    </select>
                </div>

                <div class="mb-5">
                    <label class="form-label fw-bold">Petunjuk Pengerjaan & Cakupan Soal</label>
                    <textarea name="deskripsi" rows="4" class="form-control rounded-3" placeholder="Tuliskan tata tertib ujian atau rincian penugasan yang perlu diperhatikan santri..."></textarea>
                </div>

                <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                    <a href="<?= base_url('e-learning/ujian') ?>" class="btn btn-light px-4 py-2 fw-semibold rounded-pill">Batal</a>
                    <button type="submit" class="btn text-white px-5 py-2 fw-semibold rounded-pill shadow-sm" style="background-color: #6610f2;">
                        <i class="bi bi-calendar-check me-2"></i>Jadwalkan Ujian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
