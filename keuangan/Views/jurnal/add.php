<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h3 class="fw-bold">Input Jurnal Baru 📝</h3>
            <p class="text-muted">Pastikan total Debit dan Kredit seimbang (Balance).</p>
        </div>
    </div>

    <form action="<?= base_url('keuangan/jurnal/save') ?>" method="post" id="formJurnal">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Informasi Header</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor Jurnal</label>
                            <input type="text" class="form-control bg-light" value="<?= $nomor_jurnal ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Keterangan Umum</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Penerimaan sumbangan renovasi masjid" required></textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold">Referensi / No Dokumen</label>
                            <input type="text" name="referensi" class="form-control" placeholder="Contoh: INV/2026/001">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Detail Transaksi</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="tableJurnal">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Akun</th>
                                        <th width="180">Debit</th>
                                        <th width="180">Kredit</th>
                                        <th width="50"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="row-jurnal">
                                        <td>
                                            <select name="akun_id[]" class="form-select select2" required>
                                                <option value="">-- Pilih Akun --</option>
                                                <?php foreach ($akun as $a): ?>
                                                    <option value="<?= $a['id'] ?>"><?= $a['kode_akun'] ?> - <?= $a['nama_akun'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <input type="text" name="keterangan_item[]" class="form-control form-control-sm mt-1" placeholder="Ket. Item (Opsional)">
                                        </td>
                                        <td>
                                            <input type="number" name="debit[]" class="form-control input-debit" value="0" step="0.01" min="0" required>
                                        </td>
                                        <td>
                                            <input type="number" name="kredit[]" class="form-control input-kredit" value="0" step="0.01" min="0" required>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr class="row-jurnal">
                                        <td>
                                            <select name="akun_id[]" class="form-select select2" required>
                                                <option value="">-- Pilih Akun --</option>
                                                <?php foreach ($akun as $a): ?>
                                                    <option value="<?= $a['id'] ?>"><?= $a['kode_akun'] ?> - <?= $a['nama_akun'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <input type="text" name="keterangan_item[]" class="form-control form-control-sm mt-1" placeholder="Ket. Item (Opsional)">
                                        </td>
                                        <td>
                                            <input type="number" name="debit[]" class="form-control input-debit" value="0" step="0.01" min="0" required>
                                        </td>
                                        <td>
                                            <input type="number" name="kredit[]" class="form-control input-kredit" value="0" step="0.01" min="0" required>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light fw-bold">
                                        <td class="text-end">TOTAL</td>
                                        <td id="totalDebit" class="text-end">0.00</td>
                                        <td id="totalKredit" class="text-end">0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btnAddRow">
                                <i class="fas fa-plus me-1"></i> Tambah Baris
                            </button>
                            <div id="statusBalance" class="badge bg-danger">Not Balanced</div>
                        </div>

                        <hr class="my-4">
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('keuangan/jurnal') ?>" class="btn btn-light px-4">Batal</a>
                            <button type="submit" class="btn btn-success px-4" id="btnSubmit" disabled>Simpan Jurnal</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('#tableJurnal tbody');
    const btnAddRow = document.getElementById('btnAddRow');
    const totalDebitEl = document.getElementById('totalDebit');
    const totalKreditEl = document.getElementById('totalKredit');
    const statusBalance = document.getElementById('statusBalance');
    const btnSubmit = document.getElementById('btnSubmit');

    function calculateTotal() {
        let totalDebit = 0;
        let totalKredit = 0;
        
        document.querySelectorAll('.input-debit').forEach(input => {
            totalDebit += parseFloat(input.value) || 0;
        });
        
        document.querySelectorAll('.input-kredit').forEach(input => {
            totalKredit += parseFloat(input.value) || 0;
        });

        totalDebitEl.textContent = totalDebit.toLocaleString('id-ID', {minimumFractionDigits: 2});
        totalKreditEl.textContent = totalKredit.toLocaleString('id-ID', {minimumFractionDigits: 2});

        if (totalDebit === totalKredit && totalDebit > 0) {
            statusBalance.textContent = 'Balanced';
            statusBalance.classList.remove('bg-danger');
            statusBalance.classList.add('bg-success');
            btnSubmit.disabled = false;
        } else {
            statusBalance.textContent = 'Not Balanced';
            statusBalance.classList.remove('bg-success');
            statusBalance.classList.add('bg-danger');
            btnSubmit.disabled = true;
        }
    }

    tableBody.addEventListener('input', function(e) {
        if (e.target.classList.contains('input-debit') || e.target.classList.contains('input-kredit')) {
            calculateTotal();
        }
    });

    btnAddRow.addEventListener('click', function() {
        const firstRow = document.querySelector('.row-jurnal');
        const newRow = firstRow.cloneNode(true);
        newRow.querySelectorAll('input').forEach(input => {
            if (input.type === 'number') input.value = 0;
            else input.value = '';
        });
        newRow.querySelector('select').selectedIndex = 0;
        
        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.className = 'btn btn-sm btn-link text-danger';
        deleteBtn.innerHTML = '<i class="fas fa-times"></i>';
        deleteBtn.onclick = function() {
            newRow.remove();
            calculateTotal();
        };
        
        newRow.cells[3].appendChild(deleteBtn);
        tableBody.appendChild(newRow);
    });
});
</script>
<?= $this->endSection() ?>
