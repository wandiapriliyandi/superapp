<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3">Filter Penilaian</h5>
        <form action="<?= base_url('akademik/nilai') ?>" method="get" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Kelas</label>
                <select name="kelas" class="form-select rounded-pill" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($kelas as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= $selected_kelas == $k['id'] ? 'selected' : '' ?>><?= $k['nama_kelas'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Mata Pelajaran</label>
                <select name="mapel" class="form-select rounded-pill" required>
                    <option value="">-- Pilih Mapel --</option>
                    <?php foreach ($mapel as $m): ?>
                        <option value="<?= $m['id'] ?>" <?= $selected_mapel == $m['id'] ? 'selected' : '' ?>><?= $m['nama_mapel'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary rounded-pill w-100">Buka Form</button>
            </div>
        </form>
    </div>
</div>

<?php if ($selected_kelas && $selected_mapel): ?>
<form action="<?= base_url('akademik/nilai/store') ?>" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="id_mapel" value="<?= $selected_mapel ?>">
    <input type="hidden" name="id_tahun_ajaran" value="<?= $tahun_ajaran['id'] ?>">

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent border-0 p-4">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Form Input Nilai: <?= $tahun_ajaran['tahun_ajaran'] ?> - <?= $tahun_ajaran['semester'] ?></h6>
                <button type="submit" class="btn btn-success rounded-pill px-4">Simpan Semua Nilai</button>
            </div>
        </div>
        <div class="card-body p-4 pt-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 rounded-start">Santri</th>
                            <th class="border-0 text-center" style="width: 120px;">Tugas (40%)</th>
                            <th class="border-0 text-center" style="width: 120px;">UTS (30%)</th>
                            <th class="border-0 text-center" style="width: 120px;">UAS (30%)</th>
                            <th class="border-0">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($santri as $s): ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?= $s['nama_lengkap'] ?></div>
                                <small class="text-muted"><?= $s['nis'] ?></small>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="nilai[<?= $s['id'] ?>][tugas]" class="form-control form-control-sm rounded-3 text-center" value="0">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="nilai[<?= $s['id'] ?>][uts]" class="form-control form-control-sm rounded-3 text-center" value="0">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="nilai[<?= $s['id'] ?>][uas]" class="form-control form-control-sm rounded-3 text-center" value="0">
                            </td>
                            <td>
                                <input type="text" name="nilai[<?= $s['id'] ?>][keterangan]" class="form-control form-control-sm rounded-3" placeholder="Opsional">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<?php endif; ?>
<?= $this->endSection() ?>
