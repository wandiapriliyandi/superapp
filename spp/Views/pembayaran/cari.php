<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-search me-2"></i> Cari Santri</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('spp/pembayaran/cari') ?>" method="get" class="mb-4" id="search-form">
                    <div class="input-group">
                        <input type="text" name="q" id="search-q" class="form-control rounded-start-pill ps-4" placeholder="Ketik Nama atau NISN..." value="<?= esc($q) ?>" autofocus>
                        <button class="btn btn-outline-secondary" type="button" id="btn-scan-qr" title="Scan QR Code NISN">
                            <i class="bi bi-qr-code-scan"></i>
                        </button>
                        <button class="btn btn-primary rounded-end-pill px-4" type="submit">Cari</button>
                    </div>
                </form>

                <!-- Container untuk Kamera QR Scan -->
                <div id="qr-reader-container" style="display: none;" class="mb-4">
                    <div class="card border-primary shadow-sm">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <span class="small fw-bold">📷 SCAN QR NISN</span>
                            <button type="button" class="btn-close btn-close-white" id="btn-stop-qr"></button>
                        </div>
                        <div class="card-body p-0">
                            <div id="qr-reader" style="width: 100%;"></div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($results)): ?>
                    <div class="list-group list-group-flush border rounded-4 overflow-hidden">
                        <?php foreach ($results as $s): ?>
                            <a href="<?= base_url('spp/pembayaran/cari?santri_id=' . $s['id'] . '&q=' . $q) ?>" 
                               class="list-group-item list-group-item-action py-3 <?= (isset($selected_santri) && $selected_santri['id'] == $s['id']) ? 'active bg-primary border-primary' : '' ?>">
                                <div class="fw-bold"><?= $s['nama_lengkap'] ?></div>
                                <small class="<?= (isset($selected_santri) && $selected_santri['id'] == $s['id']) ? 'text-white-50' : 'text-muted' ?>">
                                    NISN: <?= $s['nisn'] ?>
                                </small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php elseif ($q): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-emoji-frown fs-2 d-block mb-2"></i>
                        Santri tidak ditemukan.
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 text-muted small">
                        Masukkan nama santri di atas untuk memproses pembayaran.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <?php if ($selected_santri): ?>
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-success"><i class="bi bi-wallet2 me-2"></i> Daftar Tagihan</h5>
                    <span class="badge bg-success rounded-pill"><?= count($tagihan) ?> Tagihan Aktif</span>
                </div>
                <div class="card-body">
                    <div class="p-3 bg-light rounded-4 mb-4 border">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="bg-white rounded-circle p-2 shadow-sm">
                                    <span class="fs-3">👤</span>
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="fw-bold mb-0"><?= $selected_santri['nama_lengkap'] ?></h6>
                                <small class="text-muted">NISN: <?= $selected_santri['nisn'] ?></small>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($tagihan)): ?>
                        <form action="<?= base_url('spp/pembayaran/bayar-massal') ?>" method="post" id="form-keranjang">
                            <?= csrf_field() ?>
                            <input type="hidden" name="santri_id" value="<?= $selected_santri['id'] ?>">
                            
                            <div class="table-responsive mb-4">
                                <table class="table table-hover align-middle" id="table-tagihan">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40" class="text-center">
                                                <input type="checkbox" class="form-check-input" id="check-all-tagihan">
                                            </th>
                                            <th>Item Tagihan</th>
                                            <th class="text-end">Sisa</th>
                                            <th width="180" class="text-center">Bayar (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $months = [
                                            0 => 'Tahunan', 1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                        ];
                                        foreach ($tagihan as $t): 
                                            $sisa = $t['nominal_tagihan'] - $t['total_terbayar'];
                                        ?>
                                            <tr class="tagihan-row" data-id="<?= $t['id'] ?>">
                                                <td class="text-center">
                                                    <input type="checkbox" name="tagihan_ids[]" value="<?= $t['id'] ?>" class="form-check-input check-tagihan" data-sisa="<?= $sisa ?>">
                                                </td>
                                                <td>
                                                    <div class="fw-bold small"><?= $t['nama_tarif'] ?></div>
                                                    <small class="text-muted"><?= $months[$t['bulan']] ?> <?= $t['tahun'] ?></small>
                                                </td>
                                                <td class="text-end fw-bold text-danger small">
                                                    <?= number_format($sisa, 0, ',', '.') ?>
                                                </td>
                                                <td>
                                                    <input type="number" name="nominal_bayar[<?= $t['id'] ?>]" 
                                                           class="form-control form-control-sm rounded-pill px-3 input-bayar text-end" 
                                                           value="<?= $sisa ?>" max="<?= $sisa ?>" disabled>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div id="checkout-section" class="p-4 bg-primary text-white rounded-4 shadow-sm" style="display: none;">
                                <div class="row align-items-center">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <div class="small opacity-75">Total Pembayaran</div>
                                        <h3 class="fw-bold mb-0" id="total-bayar-display">Rp 0</h3>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <div class="row g-2 justify-content-md-end">
                                            <div class="col-auto">
                                                <select name="metode_pembayaran" class="form-select form-select-sm rounded-pill border-0 shadow-sm" required>
                                                    <option value="Tunai">Tunai</option>
                                                    <option value="Transfer">Transfer</option>
                                                </select>
                                            </div>
                                            <div class="col-auto">
                                                <button type="button" id="btn-show-confirm" class="btn btn-warning btn-sm rounded-pill px-4 fw-bold shadow-sm">
                                                    <i class="bi bi-check2-circle me-1"></i> Proses Bayar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>


                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i>
                            <p class="text-muted">Santri ini tidak memiliki tagihan aktif.<br>Semua pembayaran sudah lunas.</p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($history)): ?>
                        <div class="mt-5 pt-4 border-top">
                            <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-clock-history me-2"></i> Riwayat Pembayaran Terakhir</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover small">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Deskripsi Tagihan</th>
                                            <th class="text-end">Jumlah</th>
                                            <th class="text-center">Metode</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($history as $h): ?>
                                            <tr>
                                                <td><?= date('d/m/Y', strtotime($h['created_at'])) ?></td>
                                                <td>
                                                    <div class="fw-bold"><?= $h['nama_tarif'] ?></div>
                                                    <small class="text-muted">
                                                        Periode: <?= $h['bulan'] == 0 ? 'Tahunan' : $months[$h['bulan']] ?> <?= $h['tahun'] ?>
                                                    </small>
                                                </td>
                                                <td class="text-end fw-bold text-success">
                                                    Rp <?= number_format($h['nominal_bayar'], 0, ',', '.') ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-light text-dark border"><?= $h['metode_pembayaran'] ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-4 h-100 d-flex align-items-center justify-content-center bg-light border-dashed">
                <div class="text-center p-5 opacity-50">
                    <i class="bi bi-person-bounding-box display-1 mb-3"></i>
                    <h5>Pilih Santri Terlebih Dahulu</h5>
                    <p>Gunakan form pencarian di sebelah kiri.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Konfirmasi Pembayaran (Pindah ke sini agar stabil) -->
<div class="modal fade" id="modalConfirmBayar" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-body p-4 text-center">
                <div class="display-1 text-warning mb-3">
                    <i class="bi bi-question-circle-fill"></i>
                </div>
                <h4 class="fw-bold">Konfirmasi Pembayaran</h4>
                <p class="text-muted small">Anda akan memproses pembayaran untuk <strong id="confirm-count">0</strong> item:</p>
                
                <div class="bg-light rounded-4 p-3 mb-3 text-start" style="max-height: 200px; overflow-y: auto;">
                    <table class="table table-sm table-borderless mb-0 small" id="confirm-list">
                        <!-- Isi rincian akan muncul di sini via JS -->
                    </table>
                </div>

                <p class="text-muted mb-1 small">Total Bayar:</p>
                <h2 class="fw-bold text-primary mb-4" id="confirm-total">Rp 0</h2>
                
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btn-final-confirm" class="btn btn-primary rounded-pill px-4 fw-bold">Ya, Proses Sekarang</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    $(document).ready(function() {
        let html5QrCode;

        $('#btn-scan-qr').on('click', function() {
            $('#qr-reader-container').slideDown();
            $(this).prop('disabled', true);
            
            html5QrCode = new Html5Qrcode("qr-reader");
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            html5QrCode.start({ facingMode: "environment" }, config, (decodedText, decodedResult) => {
                // Success: Fill search input and submit
                $('#search-q').val(decodedText);
                stopScanner();
                $('#search-form').submit();
            }).catch((err) => {
                console.error("Gagal memulai kamera: ", err);
                alert("Kamera tidak dapat diakses atau diblokir.");
                stopScanner();
            });
        });

        $('#btn-stop-qr').on('click', function() {
            stopScanner();
        });

        function stopScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then((ignore) => {
                    $('#qr-reader-container').slideUp();
                    $('#btn-scan-qr').prop('disabled', false);
                }).catch((err) => {
                    console.error("Gagal stop kamera: ", err);
                    $('#qr-reader-container').slideUp();
                    $('#btn-scan-qr').prop('disabled', false);
                });
            }
        }

        function calculateTotal() {
            let total = 0;
            let checkedCount = 0;
            $('.check-tagihan:checked').each(function() {
                let id = $(this).val();
                let nominal = parseInt($('.input-bayar[name="nominal_bayar['+id+']"]').val()) || 0;
                total += nominal;
                checkedCount++;
            });

            $('#total-bayar-display').text('Rp ' + new Intl.NumberFormat('id-ID').format(total));
            $('#confirm-total').text('Rp ' + new Intl.NumberFormat('id-ID').format(total));
            $('#confirm-count').text(checkedCount);
            
            if (checkedCount > 0) {
                $('#checkout-section').slideDown();
            } else {
                $('#checkout-section').slideUp();
            }
        }

        $('#check-all-tagihan').on('change', function() {
            let isChecked = $(this).is(':checked');
            $('.check-tagihan').prop('checked', isChecked).trigger('change');
        });

        $('.check-tagihan').on('change', function() {
            let id = $(this).val();
            let input = $('.input-bayar[name="nominal_bayar['+id+']"]');
            
            if ($(this).is(':checked')) {
                input.prop('disabled', false);
                $(this).closest('tr').addClass('table-primary');
            } else {
                input.prop('disabled', true);
                $(this).closest('tr').removeClass('table-primary');
            }
            calculateTotal();
        });

        $('.input-bayar').on('input', function() {
            calculateTotal();
        });

        // Tampilkan Modal Konfirmasi
        $('#btn-show-confirm').on('click', function() {
            let listHtml = '';
            $('.check-tagihan:checked').each(function() {
                let id = $(this).val();
                let row = $(this).closest('tr');
                let itemName = row.find('td:eq(1) .fw-bold').text();
                let itemPeriode = row.find('td:eq(1) small').text();
                let nominal = parseInt($('.input-bayar[name="nominal_bayar['+id+']"]').val()) || 0;
                
                listHtml += `<tr>
                                <td>${itemName} <span class="text-muted">(${itemPeriode})</span></td>
                                <td class="text-end fw-bold">Rp ${new Intl.NumberFormat('id-ID').format(nominal)}</td>
                             </tr>`;
            });
            
            $('#confirm-list').html(listHtml);
            $('#modalConfirmBayar').modal('show');
        });

        // Submit Form Final dari Modal
        $('#btn-final-confirm').on('click', function() {
            $('#form-keranjang').submit();
        });
    });
</script>
<?= $this->endSection() ?>

<style>
    .border-dashed { border: 2px dashed #dee2e6 !important; }
    .list-group-item.active small { color: rgba(255,255,255,0.7) !important; }
    .tagihan-row td { transition: background 0.2s; }
</style>
