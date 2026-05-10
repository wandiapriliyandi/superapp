<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">Tambah/Edit Gelombang</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('ppdb/pengaturan/save') ?>" method="POST">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Gelombang</label>
                        <input type="text" name="gelombang" id="edit-gelombang" class="form-control shadow-sm" placeholder="Contoh: Gelombang 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kuota Santri</label>
                        <input type="number" name="kuota" id="edit-kuota" class="form-control shadow-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal Buka</label>
                        <input type="date" name="tgl_buka" id="edit-tgl_buka" class="form-control shadow-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal Tutup</label>
                        <input type="date" name="tgl_tutup" id="edit-tgl_tutup" class="form-control shadow-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <select name="status" id="edit-status" class="form-select shadow-sm">
                            <option value="Buka">Buka</option>
                            <option value="Tutup">Tutup</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 shadow-sm mt-3">💾 Simpan Gelombang</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">Riwayat Gelombang</h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Gelombang</th>
                                <th>Kuota</th>
                                <th>Periode</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($gelombang as $g): ?>
                            <tr>
                                <td class="fw-bold"><?= $g['gelombang'] ?></td>
                                <td><?= $g['kuota'] ?></td>
                                <td><small><?= $g['tgl_buka'] ?> s/d <?= $g['tgl_tutup'] ?></small></td>
                                <td><span class="badge bg-<?= $g['status'] == 'Buka' ? 'success' : 'danger' ?>"><?= $g['status'] ?></span></td>
                                <td>
                                    <div class="d-flex gap-3">
                                        <button class="btn btn-sm btn-outline-primary btn-edit" 
                                                data-id="<?= $g['id'] ?>"
                                                data-gelombang="<?= $g['gelombang'] ?>"
                                                data-kuota="<?= $g['kuota'] ?>"
                                                data-tgl_buka="<?= $g['tgl_buka'] ?>"
                                                data-tgl_tutup="<?= $g['tgl_tutup'] ?>"
                                                data-status="<?= $g['status'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal" 
                                                data-href="<?= base_url('ppdb/pengaturan/delete/'.$g['id']) ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('edit-id').value = this.dataset.id;
        document.getElementById('edit-gelombang').value = this.dataset.gelombang;
        document.getElementById('edit-kuota').value = this.dataset.kuota;
        document.getElementById('edit-tgl_buka').value = this.dataset.tgl_buka;
        document.getElementById('edit-tgl_tutup').value = this.dataset.tgl_tutup;
        document.getElementById('edit-status').value = this.dataset.status;
    });
});
</script>
<?= $this->endSection() ?>
