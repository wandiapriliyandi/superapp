<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0">Jadwal Tes Seleksi</h5>
            <p class="text-muted small mb-0">Atur waktu dan lokasi tes untuk calon santri.</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalJadwal">
            <i class="bi bi-plus-lg me-1"></i> Buat Jadwal Baru
        </button>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama Tes</th>
                        <th>Waktu & Lokasi</th>
                        <th>Kuota</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($jadwal as $j): ?>
                    <tr>
                        <td>
                            <div class="fw-bold text-primary"><?= $j['nama_tes'] ?></div>
                            <div class="small text-muted"><i class="bi bi-calendar-event me-1"></i> <?= date('d M Y', strtotime($j['tanggal'])) ?></div>
                        </td>
                        <td>
                            <div class="small"><i class="bi bi-clock me-1 text-secondary"></i> <?= $j['jam'] ?></div>
                            <div class="small fw-bold"><i class="bi bi-geo-alt me-1 text-danger"></i> <?= $j['tempat'] ?></div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border"><?= $j['kuota'] ?> Orang</span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="<?= base_url('ppdb/jadwal/detail/'.$j['id']) ?>" class="btn btn-sm btn-info text-white rounded-pill px-3">
                                    <i class="bi bi-people me-1"></i> Peserta
                                </a>
                                <button class="btn btn-sm btn-light border-0" onclick="editJadwal(<?= htmlspecialchars(json_encode($j)) ?>)">
                                    <i class="bi bi-pencil-square text-primary"></i>
                                </button>
                                <a href="<?= base_url('ppdb/jadwal/delete/'.$j['id']) ?>" class="btn btn-sm btn-light border-0" onclick="return confirm('Hapus jadwal ini?')">
                                    <i class="bi bi-trash text-danger"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Jadwal -->
<div class="modal fade" id="modalJadwal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold" id="modalTitle">Tambah Jadwal Tes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('ppdb/jadwal/save') ?>" method="POST">
                <div class="modal-body p-4 pt-2">
                    <input type="hidden" name="id" id="id_jadwal">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Tes</label>
                        <input type="text" name="nama_tes" id="nama_tes" class="form-control border-0 bg-body-tertiary shadow-sm" placeholder="Contoh: Tes Akademik Gelombang 1" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control border-0 bg-body-tertiary shadow-sm" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Jam</label>
                            <input type="text" name="jam" id="jam" class="form-control border-0 bg-body-tertiary shadow-sm" placeholder="08:00 - Selesai" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tempat / Ruangan</label>
                        <input type="text" name="tempat" id="tempat" class="form-control border-0 bg-body-tertiary shadow-sm" placeholder="Contoh: Aula Serbaguna" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kuota Peserta</label>
                        <input type="number" name="kuota" id="kuota" class="form-control border-0 bg-body-tertiary shadow-sm" value="0">
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill shadow">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editJadwal(data) {
        document.getElementById('modalTitle').innerText = 'Edit Jadwal Tes';
        document.getElementById('id_jadwal').value = data.id;
        document.getElementById('nama_tes').value = data.nama_tes;
        document.getElementById('tanggal').value = data.tanggal;
        document.getElementById('jam').value = data.jam;
        document.getElementById('tempat').value = data.tempat;
        document.getElementById('kuota').value = data.kuota;
        new bootstrap.Modal(document.getElementById('modalJadwal')).show();
    }
</script>
<?= $this->endSection() ?>
