<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-0">Form Pengajuan Cuti</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <form action="<?= base_url('kepegawaian/cuti/save') ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Asatidz / Staff</label>
                        <select name="pegawai_id" class="form-select" required>
                            <option value="">Pilih SDM</option>
                            <?php foreach($pegawai as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= $p['nama_lengkap'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Jenis Izin / Cuti</label>
                        <select name="jenis_cuti" class="form-select" required>
                            <option value="Tahunan">Cuti Tahunan</option>
                            <option value="Sakit">Izin Sakit</option>
                            <option value="Urusan Keluarga">Izin Urusan Keluarga</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small">Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small">Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Alasan / Keperluan</label>
                        <textarea name="alasan" class="form-control" rows="3" placeholder="Tuliskan alasan pengajuan..."></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-pill py-2">Kirim Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold mb-0">Daftar Pengajuan Cuti & Izin</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">SDM</th>
                                <th>Jenis</th>
                                <th>Periode</th>
                                <th>Status</th>
                                <th class="pe-4 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cuti as $c): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?= $c['nama_lengkap'] ?></div>
                                    <div class="small text-muted"><?= $c['jenis_cuti'] ?></div>
                                </td>
                                <td><?= $c['jenis_cuti'] ?></td>
                                <td>
                                    <div class="small fw-semibold"><?= date('d/m/y', strtotime($c['tanggal_mulai'])) ?> s/d <?= date('d/m/y', strtotime($c['tanggal_selesai'])) ?></div>
                                </td>
                                <td>
                                    <?php 
                                        $badge = 'bg-warning';
                                        if($c['status'] == 'Disetujui') $badge = 'bg-success';
                                        elseif($c['status'] == 'Ditolak') $badge = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $badge ?> bg-opacity-10 <?= str_replace('bg-', 'text-', $badge) ?> px-3 py-2 rounded-pill">
                                        <?= $c['status'] ?>
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <?php if($c['status'] == 'Pending'): ?>
                                    <a href="<?= base_url('kepegawaian/cuti/approve/' . $c['id']) ?>" class="btn btn-sm btn-success rounded-pill px-3">
                                        Approve
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($cuti)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada pengajuan cuti.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
