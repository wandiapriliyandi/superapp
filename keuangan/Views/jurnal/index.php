<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold">Jurnal Umum 📖</h3>
            <p class="text-muted">Riwayat transaksi keuangan yang tercatat dalam sistem.</p>
        </div>
    </div>

    <!-- Quick Action Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 btn-action-card bg-primary text-white" data-bs-toggle="modal" data-bs-target="#modalPemasukan">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3">
                        <i class="fas fa-arrow-down fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Kas Masuk</h6>
                        <small class="opacity-75">Catat pendapatan / hibah</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 btn-action-card bg-danger text-white" data-bs-toggle="modal" data-bs-target="#modalPengeluaran">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3">
                        <i class="fas fa-arrow-up fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Kas Keluar</h6>
                        <small class="opacity-75">Catat biaya / belanja</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <a href="<?= base_url('keuangan/jurnal/add') ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 h-100 btn-action-card bg-dark text-white">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3">
                            <i class="fas fa-plus fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Jurnal Umum</h6>
                            <small class="opacity-75">Entry akuntansi manual</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <style>
        .btn-action-card { transition: all 0.3s ease; cursor: pointer; border: 1px solid transparent !important; }
        .btn-action-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; filter: brightness(1.1); }
    </style>

    <?php if (empty($jurnal)): ?>
        <div class="card border-0 shadow-sm rounded-4 py-5 text-center">
            <div class="card-body">
                <i class="fas fa-receipt fa-4x text-light mb-3"></i>
                <h5>Belum Ada Transaksi</h5>
                <p class="text-muted">Mulai catat transaksi pertama Anda hari ini.</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($jurnal as $row): ?>
            <div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
                <div class="card-header bg-light border-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-primary me-2"><?= $row['nomor_jurnal'] ?></span>
                        <span class="fw-bold text-dark"><?= date('d M Y', strtotime($row['tanggal'])) ?></span>
                    </div>
                    <div>
                        <span class="text-muted small"><?= $row['keterangan'] ?></span>
                        <a href="<?= base_url('keuangan/jurnal/delete/' . $row['id']) ?>" class="btn btn-sm btn-link text-danger ms-3" onclick="return confirm('Hapus jurnal ini?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr class="bg-white border-bottom">
                                <th class="ps-4">Akun</th>
                                <th width="200" class="text-end">Debit</th>
                                <th width="200" class="text-end pe-4">Kredit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $tot_debit = 0;
                            $tot_kredit = 0;
                            foreach ($row['details'] as $detail): 
                                $tot_debit += $detail['debit'];
                                $tot_kredit += $detail['kredit'];
                            ?>
                                <tr>
                                    <td class="ps-4">
                                        <?php if ($detail['kredit'] > 0): ?>
                                            <span class="ms-4"></span>
                                        <?php endif; ?>
                                        <?= $detail['kode_akun'] ?> - <?= $detail['nama_akun'] ?>
                                        <?php if ($detail['keterangan_item']): ?>
                                            <br><small class="text-muted ms-5 fst-italic"><?= $detail['keterangan_item'] ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end text-success"><?= $detail['debit'] > 0 ? number_format($detail['debit'], 2) : '-' ?></td>
                                    <td class="text-end text-danger pe-4"><?= $detail['kredit'] > 0 ? number_format($detail['kredit'], 2) : '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal Kas Masuk -->
<div class="modal fade" id="modalPemasukan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-arrow-down me-2"></i>Catat Kas Masuk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('keuangan/jurnal/save_simple') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="type" value="pemasukan">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control rounded-3" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Masuk ke Akun (Kas/Bank)</label>
                        <select name="akun_kas_id" class="form-select rounded-3" required>
                            <option value="">-- Pilih Akun Kas --</option>
                            <?php foreach($akun_kas as $a): ?>
                                <option value="<?= $a['id'] ?>"><?= $a['kode_akun'] ?> - <?= $a['nama_akun'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Sumber Pendapatan / Lawan</label>
                        <select name="akun_lawan_id" class="form-select rounded-3" required>
                            <option value="">-- Pilih Akun Sumber --</option>
                            <?php foreach($akun_lawan as $a): ?>
                                <option value="<?= $a['id'] ?>"><?= $a['kode_akun'] ?> - <?= $a['nama_akun'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nominal (Rp)</label>
                        <input type="number" name="nominal" class="form-control form-control-lg fw-bold text-primary rounded-3" placeholder="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Keterangan</label>
                        <textarea name="keterangan" class="form-control rounded-3" rows="2" placeholder="Keterangan transaksi..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Simpan Pemasukan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Kas Keluar -->
<div class="modal fade" id="modalPengeluaran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-arrow-up me-2"></i>Catat Kas Keluar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('keuangan/jurnal/save_simple') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="type" value="pengeluaran">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control rounded-3" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Bayar Melalui (Kas/Bank)</label>
                        <select name="akun_kas_id" class="form-select rounded-3" required>
                            <option value="">-- Pilih Akun Kas --</option>
                            <?php foreach($akun_kas as $a): ?>
                                <option value="<?= $a['id'] ?>"><?= $a['kode_akun'] ?> - <?= $a['nama_akun'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Tujuan Biaya / Lawan</label>
                        <select name="akun_lawan_id" class="form-select rounded-3" required>
                            <option value="">-- Pilih Akun Tujuan --</option>
                            <?php foreach($akun_lawan as $a): ?>
                                <option value="<?= $a['id'] ?>"><?= $a['kode_akun'] ?> - <?= $a['nama_akun'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nominal (Rp)</label>
                        <input type="number" name="nominal" class="form-control form-control-lg fw-bold text-danger rounded-3" placeholder="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Keterangan</label>
                        <textarea name="keterangan" class="form-control rounded-3" rows="2" placeholder="Keterangan transaksi..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 shadow">Simpan Pengeluaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
