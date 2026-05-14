<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0"><?= $title ?></h5>
            <small class="text-muted">Total koleksi: <?= count($buku) ?> buku</small>
        </div>
        <a href="<?= base_url('perpustakaan/tambah/' . strtolower($lokasi)) ?>" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg me-2"></i> Tambah Buku
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3">Buku</th>
                        <th class="py-3">Info Penerbit</th>
                        <th class="py-3">Kategori</th>
                        <th class="py-3 text-center">Stok</th>
                        <th class="py-3 text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($buku)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-book fs-1 text-muted opacity-25"></i>
                            <p class="mt-3 text-muted">Belum ada koleksi buku di kategori ini.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php foreach ($buku as $b): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-3 p-1 me-3">
                                    <?php if ($b['cover']): ?>
                                        <img src="<?= base_url('uploads/perpus/cover/' . $b['cover']) ?>" alt="" width="40" height="55" class="rounded-2 object-fit-cover">
                                    <?php else: ?>
                                        <div class="bg-secondary bg-opacity-10 text-secondary rounded-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 55px;">
                                            <i class="bi bi-book"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold"><?= $b['judul'] ?></h6>
                                    <small class="text-muted"><?= $b['kode_buku'] ?> • <?= $b['pengarang'] ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small">
                                <div class="fw-semibold"><?= $b['penerbit'] ?></div>
                                <div class="text-muted"><?= $b['tahun_terbit'] ?></div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark fw-normal rounded-pill px-3"><?= $b['kategori'] ?></span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold"><?= $b['stok'] ?></span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle p-2 shadow-none" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3">
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2 text-primary"></i> Edit</a></li>
                                    <?php if ($lokasi == 'Digital'): ?>
                                        <?php if ($b['link_eksternal']): ?>
                                            <li><a class="dropdown-item" href="<?= $b['link_eksternal'] ?>" target="_blank"><i class="bi bi-google me-2 text-primary"></i> Buka di Drive</a></li>
                                        <?php elseif ($b['file_digital']): ?>
                                            <li><a class="dropdown-item" href="<?= base_url('uploads/perpus/digital/' . $b['file_digital']) ?>" target="_blank"><i class="bi bi-download me-2 text-success"></i> Download</a></li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="<?= base_url('perpustakaan/hapus/' . $b['id']) ?>" onclick="return confirm('Hapus buku ini?')"><i class="bi bi-trash me-2"></i> Hapus</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
