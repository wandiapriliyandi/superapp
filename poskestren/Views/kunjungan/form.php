<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 p-4">
                <div class="d-flex align-items-center">
                    <a href="<?= base_url('poskestren/kunjungan') ?>" class="btn btn-light rounded-circle me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h5 class="fw-bold mb-0">Catat Kunjungan Santri Baru</h5>
                </div>
            </div>
            <div class="card-body p-4 pt-0">
                <form action="<?= base_url('poskestren/kunjungan/simpan') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">SANTRI</label>
                            <select name="santri_id" class="form-select border-0 bg-light rounded-3 py-2" required>
                                <option value="">Pilih Santri...</option>
                                <?php foreach($santri as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= $s['nama_lengkap'] ?> (<?= $s['nis'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">WAKTU KUNJUNGAN</label>
                            <input type="datetime-local" name="tgl_kunjungan" class="form-select border-0 bg-light rounded-3 py-2" value="<?= date('Y-m-d\TH:i') ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted">KELUHAN</label>
                            <textarea name="keluhan" class="form-control border-0 bg-light rounded-3" rows="3" placeholder="Jelaskan keluhan santri..." required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">DIAGNOSA (OPSIONAL)</label>
                            <input type="text" name="diagnosa" class="form-control border-0 bg-light rounded-3 py-2" placeholder="Hasil diagnosa awal">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">STATUS AKHIR</label>
                            <select name="status" class="form-select border-0 bg-light rounded-3 py-2">
                                <option value="Sembuh">Sembuh / Kembali ke Kamar</option>
                                <option value="Observasi">Observasi / Rawat Inap</option>
                                <option value="Rujuk">Rujuk ke RS / Klinik Luar</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted">TINDAKAN / CATATAN MEDIS</label>
                            <textarea name="tindakan" class="form-control border-0 bg-light rounded-3" rows="2" placeholder="Tindakan yang diberikan..."></textarea>
                        </div>

                        <!-- Bagian Pemberian Obat -->
                        <div class="col-md-12 mt-5">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="fw-bold mb-0"><i class="bi bi-capsule me-2"></i>Pemberian Obat</h6>
                                <button type="button" class="btn btn-sm btn-light text-primary rounded-pill fw-bold" id="addObat">
                                    <i class="bi bi-plus"></i> Tambah Obat
                                </button>
                            </div>
                            
                            <div id="obatWrapper">
                                <div class="row g-2 mb-2 obat-row">
                                    <div class="col-md-5">
                                        <select name="obat_id[]" class="form-select border-0 bg-light rounded-3 py-2">
                                            <option value="">Pilih Obat...</option>
                                            <?php foreach($obat as $o): ?>
                                                <option value="<?= $o['id'] ?>"><?= $o['nama_obat'] ?> (Stok: <?= $o['stok'] ?> <?= $o['satuan'] ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="jumlah[]" class="form-control border-0 bg-light rounded-3 py-2" placeholder="Jml">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="dosis[]" class="form-control border-0 bg-light rounded-3 py-2" placeholder="Dosis (mis: 3x1 hari)">
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <button type="button" class="btn btn-light text-danger rounded-3 py-2 remove-obat"><i class="bi bi-x"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-5">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 w-100 fw-bold shadow-sm">
                                Simpan Rekam Medis
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('addObat').addEventListener('click', function() {
        const wrapper = document.getElementById('obatWrapper');
        const firstRow = wrapper.querySelector('.obat-row').cloneNode(true);
        
        // Reset inputs
        firstRow.querySelectorAll('input').forEach(input => input.value = '');
        firstRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
        
        wrapper.appendChild(firstRow);

        // Add event listener to remove button
        firstRow.querySelector('.remove-obat').addEventListener('click', function() {
            if (wrapper.querySelectorAll('.obat-row').length > 1) {
                firstRow.remove();
            }
        });
    });

    document.querySelectorAll('.remove-obat').forEach(btn => {
        btn.addEventListener('click', function() {
            const wrapper = document.getElementById('obatWrapper');
            if (wrapper.querySelectorAll('.obat-row').length > 1) {
                this.closest('.obat-row').remove();
            }
        });
    });
</script>
<?= $this->endSection() ?>
