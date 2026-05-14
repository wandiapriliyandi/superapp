<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0">Pemetaan Tarif & Kesepakatan Bayaran</h5>
                    <p class="text-muted small mb-0">Pilih santri untuk mengatur jenis tagihan yang harus dibayar setiap bulannya.</p>
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-success btn-sm rounded-pill px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-download me-1"></i>Eksport
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                        <li><a class="dropdown-item" href="<?= base_url('spp/mapping/export/excel?' . http_build_query($filter)) ?>"><i class="bi bi-file-earmark-excel me-2 text-success"></i>Excel (.xlsx)</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('spp/mapping/export/word?' . http_build_query($filter)) ?>"><i class="bi bi-file-earmark-word me-2 text-primary"></i>Word (.doc)</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('spp/mapping/export/pdf?' . http_build_query($filter)) ?>" target="_blank"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF / Cetak</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body bg-light border-bottom py-2 px-3">
                <form action="<?= base_url('spp/mapping') ?>" method="GET" class="row row-cols-auto gx-2 gy-0 align-items-center">
                    <div class="col-auto">
                        <input type="text" name="q" class="form-control form-control-sm rounded-pill" placeholder="Cari Nama / NISN..." value="<?= $filter['q'] ?>" style="width: 250px;">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3">Filter</button>
                        <a href="<?= base_url('spp/mapping') ?>" class="btn btn-light btn-sm rounded-pill px-3 border text-dark">Reset</a>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="table-mapping">
                        <thead>
                            <tr>
                                <th>Santri</th>
                                <th>Jenis Kelamin</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($santri as $s): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?= $s['nama_lengkap'] ?></div>
                                    <small class="text-muted">NISN: <?= $s['nisn'] ?></small>
                                </td>
                                <td><?= $s['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?= base_url('spp/mapping/santri/' . $s['id']) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 me-1">
                                            <i class="bi bi-gear me-1"></i> Atur Tarif
                                        </a>
                                        <a href="<?= base_url('spp/tagihan/generate-santri/' . $s['id']) ?>" class="btn btn-primary btn-sm rounded-pill px-3" title="Generate tagihan bulan ini">
                                            <i class="bi bi-magic me-1"></i> Generate
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
    </div>
</div>
<?= $this->endSection() ?>
