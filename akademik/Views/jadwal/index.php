<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row g-4 mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form action="<?= base_url('akademik/jadwal') ?>" method="get" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Pilih Kelas untuk Melihat Jadwal</label>
                        <select name="kelas" class="form-select rounded-3" onchange="this.form.submit()">
                            <option value="">-- Semua Kelas --</option>
                            <?php foreach ($kelas as $k): ?>
                                <option value="<?= $k['id'] ?>" <?= $selected_kelas == $k['id'] ? 'selected' : '' ?>>
                                    <?= $k['nama_kelas'] ?> (Tingkat <?= $k['tingkat'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-8 text-end">
                        <a href="<?= base_url('akademik/jadwal/create') ?>" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-plus-lg me-2"></i> Tambah Jadwal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 rounded-start">Hari</th>
                        <th class="border-0">Waktu</th>
                        <th class="border-0">Mata Pelajaran</th>
                        <th class="border-0">Guru</th>
                        <th class="border-0">Kelas</th>
                        <th class="border-0">Ruangan</th>
                        <th class="border-0 text-end rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($jadwal)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Tidak ada jadwal pelajaran ditemukan</td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $current_hari = '';
                        foreach ($jadwal as $j): 
                        ?>
                        <tr>
                            <td>
                                <?php if ($current_hari != $j['hari']): ?>
                                    <span class="badge bg-primary px-3 py-2 rounded-pill"><?= $j['hari'] ?></span>
                                    <?php $current_hari = $j['hari']; ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small">
                                <i class="bi bi-clock me-1"></i>
                                <?= date('H:i', strtotime($j['jam_mulai'])) ?> - <?= date('H:i', strtotime($j['jam_selesai'])) ?>
                            </td>
                            <td class="fw-bold"><?= $j['nama_mapel'] ?></td>
                            <td><?= $j['nama_guru'] ?></td>
                            <td><?= $j['nama_kelas'] ?></td>
                            <td><span class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= $j['ruangan'] ?? '-' ?></span></td>
                            <td class="text-end">
                                <a href="<?= base_url('akademik/jadwal/delete/' . $j['id']) ?>" class="btn btn-sm btn-light-danger rounded-pill px-3" onclick="return confirm('Hapus jadwal ini?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
