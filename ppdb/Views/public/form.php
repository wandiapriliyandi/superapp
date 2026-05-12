<?= $this->extend('Ppdb\Views\public\layout') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-lg">
            <div class="card-body p-5">
                <div class="alert alert-info border-0 mb-4 shadow-sm">
                    <i class="bi bi-info-circle-fill me-2"></i> 
                    Pendaftaran sedang dibuka untuk <strong><?= $wave['gelombang'] ?></strong> sampai tanggal <strong><?= date('d M Y', strtotime($wave['tgl_tutup'])) ?></strong>.
                </div>

                <form id="publicDaftarForm" action="" method="POST">
                    <script>
                        // Menjamin form action secara presisi menggunakan protokol HTTPS dan host riil sesuai address bar browser saat ini
                        // Menghindari terhapusnya data POST akibat pengalihan HTTP ke HTTPS dan pemblokiran "Insecure Form Submission"
                        document.addEventListener('DOMContentLoaded', function() {
                            let currentUrl = window.location.href.split('#')[0].split('?')[0].replace(/\/$/, '');
                            document.getElementById('publicDaftarForm').action = currentUrl + '/submit';
                        });
                    </script>
                    <h5 class="fw-bold mb-4 border-bottom pb-2 text-primary">Data Diri Calon Santri</h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" placeholder="Masukkan nama sesuai ijazah" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="">Pilih...</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">No. HP Orang Tua / WA</label>
                            <input type="text" name="no_hp_ortu" class="form-control" placeholder="08xxxxxxxxxx" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" rows="3" placeholder="Jl. Nama Jalan, Desa, Kec, Kab/Kota" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 shadow rounded-pill py-3 fw-bold">
                        <i class="bi bi-send-fill me-2"></i> Kirim Pendaftaran Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
