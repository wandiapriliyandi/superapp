<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 p-4">
                <div class="d-flex align-items-center">
                    <a href="<?= base_url('poskestren/obat') ?>" class="btn btn-light rounded-circle me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h5 class="fw-bold mb-0">Pengadaan Obat</h5>
                        <p class="text-muted small mb-0">Stok masuk — beli / terima dari apotek atau gudang secara massal</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-4 pt-0">
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger rounded-4"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <form action="<?= base_url('poskestren/stok/pengadaan/simpan') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="table-responsive mb-3">
                        <table class="table table-hover align-middle border" id="tablePengadaan">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th>Nama Obat</th>
                                    <th width="20%">Jumlah Masuk</th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="pengadaan-row">
                                    <td class="row-num text-center fw-bold">1</td>
                                    <td>
                                        <select name="items[0][obat_id]" class="form-select border-0 bg-light rounded-3 py-2" required>
                                            <option value="">Pilih obat...</option>
                                            <?php foreach ($obat as $o) : ?>
                                                <option value="<?= $o['id'] ?>">
                                                    <?= esc($o['nama_obat']) ?> — stok sekarang: <?= (int) $o['stok'] ?> <?= esc($o['satuan']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][jumlah]" min="1" class="form-control border-0 bg-light rounded-3 py-2" value="1" required>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle btn-remove-row" style="width: 32px; height: 32px; padding: 0;" disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-start mb-4">
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold" id="btnAddRow">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Baris Obat
                        </button>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">KETERANGAN GLOBAL (OPSIONAL)</label>
                        <textarea name="keterangan" class="form-control border-0 bg-light rounded-3" rows="2" placeholder="Mis: Pembelian Apotek X, tanggal 04/06/2026"><?= old('keterangan') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-success rounded-pill px-5 py-3 w-100 fw-bold">
                        <i class="bi bi-box-arrow-in-down me-2"></i> Simpan Catatan Pengadaan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        const obatOptions = `
            <option value="">Pilih obat...</option>
            <?php foreach ($obat as $o) : ?>
                <option value="<?= $o['id'] ?>">
                    <?= esc($o['nama_obat']) ?> — stok sekarang: <?= (int) $o['stok'] ?> <?= esc($o['satuan']) ?>
                </option>
            <?php endforeach; ?>
        `;

        function updateRowNumbers() {
            $('#tablePengadaan tbody tr').each(function(index) {
                $(this).find('.row-num').text(index + 1);
                
                // Update name attributes to maintain sequential arrays
                $(this).find('select').attr('name', `items[${index}][obat_id]`);
                $(this).find('input[type="number"]').attr('name', `items[${index}][jumlah]`);
            });
            
            // Disable delete button if only 1 row left
            const rows = $('#tablePengadaan tbody tr');
            if (rows.length <= 1) {
                rows.find('.btn-remove-row').attr('disabled', true);
            } else {
                rows.find('.btn-remove-row').removeAttr('disabled');
            }
        }

        $('#btnAddRow').click(function() {
            const newRow = `
                <tr class="pengadaan-row">
                    <td class="row-num text-center fw-bold"></td>
                    <td>
                        <select class="form-select border-0 bg-light rounded-3 py-2" required>
                            ${obatOptions}
                        </select>
                    </td>
                    <td>
                        <input type="number" min="1" class="form-control border-0 bg-light rounded-3 py-2" value="1" required>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle btn-remove-row" style="width: 32px; height: 32px; padding: 0;">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#tablePengadaan tbody').append(newRow);
            updateRowNumbers();
        });

        $(document).on('click', '.btn-remove-row', function() {
            $(this).closest('tr').remove();
            updateRowNumbers();
        });
    });
</script>
<?= $this->endSection() ?>
