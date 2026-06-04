<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-primary bg-opacity-10 border-0 p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="<?= base_url('poskestren/kunjungan') ?>" class="btn btn-white shadow-sm rounded-circle me-3">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <h5 class="fw-bold mb-0 text-primary">Detail Rekam Medis</h5>
                    </div>
                    <span class="badge bg-primary rounded-pill px-3 py-2 small">ID #<?= $kunjungan['id'] ?></span>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Kop Surat untuk Cetak -->
                <div class="d-none d-print-block text-center pb-2 mb-4">
                    <h3 class="fw-bold mb-0 text-uppercase text-primary"><?= esc($setting['app_name'] ?? 'SuperApp Pesantren') ?></h3>
                    <h5 class="fw-semibold mb-1"><?= esc($setting['pesantren_name'] ?? 'Pesantren Modern Digital') ?></h5>
                    <p class="mb-0 text-muted small" style="font-size: 11px;">Sistem Rekam Medis Poskestren Terintegrasi</p>
                    <hr class="my-2 border-dark" style="border-top: 3px double #000; opacity: 1;">
                </div>
                <div class="row mb-5">
                    <div class="col-md-6 border-end">
                        <label class="text-muted small fw-bold mb-1">DATA SANTRI</label>
                        <h5 class="fw-bold mb-1"><?= $kunjungan['nama_santri'] ?></h5>
                        <p class="text-muted mb-0"><?= $kunjungan['nis'] ?> | Kelas <?= $kunjungan['nama_kelas'] ?></p>
                    </div>
                    <div class="col-md-6 ps-md-4">
                        <label class="text-muted small fw-bold mb-1">WAKTU KUNJUNGAN</label>
                        <h5 class="fw-bold mb-1"><?= date('d F Y', strtotime($kunjungan['tgl_kunjungan'])) ?></h5>
                        <p class="text-muted mb-0">Jam <?= date('H:i', strtotime($kunjungan['tgl_kunjungan'])) ?> WIB</p>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-muted small fw-bold mb-2">KELUHAN</label>
                    <div class="p-3 bg-light rounded-3">
                        <?= nl2br($kunjungan['keluhan']) ?>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold mb-2">DIAGNOSA</label>
                        <p class="fw-bold text-dark"><?= $kunjungan['diagnosa'] ?: 'Tidak ada diagnosa spesifik' ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold mb-2">STATUS AKHIR</label>
                        <div>
                            <?php 
                                $badge_class = 'bg-success';
                                if($kunjungan['status'] == 'Observasi') $badge_class = 'bg-warning';
                                if($kunjungan['status'] == 'Rujuk') $badge_class = 'bg-danger';
                            ?>
                            <span class="badge <?= $badge_class ?> rounded-pill px-3">
                                <?= $kunjungan['status'] ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-muted small fw-bold mb-2">TINDAKAN / CATATAN</label>
                    <p class="text-dark"><?= $kunjungan['tindakan'] ?: '-' ?></p>
                </div>

                <?php if (!empty($obat)): ?>
                <div class="mt-5">
                    <label class="text-muted small fw-bold mb-3 d-block"><i class="bi bi-capsule me-1"></i> OBAT YANG DIBERIKAN</label>
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3 py-2 small">Nama Obat</th>
                                    <th class="py-2 small">Jumlah</th>
                                    <th class="py-2 small">Dosis / Aturan Pakai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($obat as $o): ?>
                                <tr>
                                    <td class="ps-3 py-3 border-bottom">
                                        <div class="fw-bold"><?= $o['nama_obat'] ?></div>
                                    </td>
                                    <td class="py-3 border-bottom"><?= $o['jumlah'] ?> <?= $o['satuan'] ?></td>
                                    <td class="py-3 border-bottom">
                                        <span class="badge bg-light text-dark rounded-pill"><?= $o['dosis'] ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-light border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Dicatat oleh ID Petugas: <?= $kunjungan['petugas_id'] ?: '-' ?></span>
                    <div>
                        <button class="btn btn-outline-primary rounded-pill btn-sm px-4 me-2" data-bs-toggle="modal" data-bs-target="#updateModal" <?= in_array($kunjungan['status'], ['Sembuh', 'Rujuk']) ? 'disabled' : '' ?>>
                            <i class="bi bi-pencil-square me-2"></i> Update Perkembangan
                        </button>
                        <button class="btn btn-primary rounded-pill btn-sm px-4" onclick="window.print()">
                            <i class="bi bi-printer me-2"></i> Cetak Rekam Medis
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        /* Sembunyikan elemen non-print */
        .btn, .card-header, .card-footer, #sidebarBackdrop, .sidebar, .topbar { 
            display: none !important; 
        }
        
        /* Reset layout admin */
        .main-content {
            margin-left: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        .container-fluid {
            padding: 0 !important;
        }
        body { 
            background: white !important; 
            color: black !important;
            font-size: 12pt !important;
            line-height: 1.5 !important;
        }
        .card { 
            border: none !important; 
            box-shadow: none !important; 
            background: transparent !important;
        }
        .card-body {
            padding: 0 !important;
        }
        
        /* Atur ukuran font standar untuk cetak */
        h5, .h5, p, td, th, div {
            font-size: 11pt !important;
        }
        .text-muted {
            color: #555 !important;
        }
        .bg-light {
            background-color: #f8f9fa !important;
            border: 1px solid #ddd !important;
        }
        
        /* Set margin kertas */
        @page {
            size: portrait;
            margin: 20mm;
        }
    }
</style>
<!-- Modal Update Perkembangan -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form action="<?= base_url('poskestren/kunjungan/update/' . $kunjungan['id']) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold" id="updateModalLabel">Update Perkembangan Rekam Medis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">STATUS PERKEMBANGAN</label>
                            <select name="status" class="form-select border-0 bg-light rounded-3 py-2" required>
                                <option value="Sakit" <?= $kunjungan['status'] == 'Sakit' ? 'selected' : '' ?>>Sakit (Dalam Perawatan)</option>
                                <option value="Observasi" <?= $kunjungan['status'] == 'Observasi' ? 'selected' : '' ?>>Observasi / Rawat Inap</option>
                                <option value="Sembuh" <?= $kunjungan['status'] == 'Sembuh' ? 'selected' : '' ?>>Sembuh / Kembali ke Kamar</option>
                                <option value="Rujuk" <?= $kunjungan['status'] == 'Rujuk' ? 'selected' : '' ?>>Rujuk ke RS / Klinik Luar</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">DIAGNOSA (DAPAT DIPERBARUI)</label>
                            <input type="text" name="diagnosa" class="form-control border-0 bg-light rounded-3 py-2" value="<?= esc($kunjungan['diagnosa']) ?>" placeholder="Update diagnosa jika ada perubahan">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted">TINDAKAN / CATATAN MEDIS BARU</label>
                            <textarea name="tindakan" class="form-control border-0 bg-light rounded-3" rows="3" placeholder="Tuliskan perkembangan/tindakan medis terbaru..."></textarea>
                        </div>

                        <!-- Sub-form Pemberian Obat Tambahan -->
                        <div class="col-md-12 mt-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fw-bold mb-0 text-secondary" style="font-size: 0.85rem;"><i class="bi bi-capsule me-2"></i>Pemberian Obat Tambahan (Opsional)</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill fw-bold" id="addObatUpdate" style="font-size: 0.75rem;">
                                    <i class="bi bi-plus"></i> Tambah Obat
                                </button>
                            </div>
                            <div id="obatWrapperUpdate">
                                <div class="row g-2 mb-2 obat-row-update">
                                    <div class="col-md-6">
                                        <select name="obat_id[]" class="form-select border-0 bg-light rounded-3 py-2">
                                            <option value="">Pilih Obat...</option>
                                            <?php foreach($obat_list as $o): ?>
                                                <option value="<?= $o['id'] ?>"><?= esc($o['nama_obat']) ?> (Stok: <?= (int)$o['stok'] ?> <?= esc($o['satuan']) ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="jumlah[]" class="form-control border-0 bg-light rounded-3 py-2" placeholder="Jml">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="dosis[]" class="form-control border-0 bg-light rounded-3 py-2" placeholder="Dosis (3x1 hari)">
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <button type="button" class="btn btn-light text-danger rounded-3 py-2 remove-obat-update"><i class="bi bi-x"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light px-4 py-2 fw-semibold" data-bs-dismiss="modal" style="border-radius: 12px;">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm" style="border-radius: 12px;">Simpan Perkembangan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addBtn = document.getElementById('addObatUpdate');
        if (addBtn) {
            addBtn.addEventListener('click', function() {
                const wrapper = document.getElementById('obatWrapperUpdate');
                const row = wrapper.querySelector('.obat-row-update');
                if (row) {
                    const firstRow = row.cloneNode(true);
                    
                    // Reset inputs
                    firstRow.querySelectorAll('input').forEach(input => input.value = '');
                    firstRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
                    
                    wrapper.appendChild(firstRow);
                    
                    // Add event listener to remove button
                    firstRow.querySelector('.remove-obat-update').addEventListener('click', function() {
                        if (wrapper.querySelectorAll('.obat-row-update').length > 1) {
                            firstRow.remove();
                        } else {
                            // Jika sisa 1 baris, cukup reset nilainya
                            firstRow.querySelectorAll('input').forEach(input => input.value = '');
                            firstRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
                        }
                    });
                }
            });
        }

        $(document).on('click', '.remove-obat-update', function() {
            const wrapper = document.getElementById('obatWrapperUpdate');
            if (wrapper.querySelectorAll('.obat-row-update').length > 1) {
                $(this).closest('.obat-row-update').remove();
            } else {
                $(this).closest('.obat-row-update').find('input').val('');
                $(this).closest('.obat-row-update').find('select').val('');
            }
        });
    });
</script>
<?= $this->endSection() ?>
