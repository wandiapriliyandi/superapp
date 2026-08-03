# Workflow Modul Keuangan General

Dokumen ini menjelaskan alur kerja transaksi kas, pemasukan, pengeluaran, dan pelaporan keuangan di dalam sistem SuperApp.

## 1. Alur Utama (Main Flow)

1. **Pengaturan Akun Kas/Bank & COA**: Admin Keuangan mendaftarkan Chart of Accounts (COA) serta akun kas/bank lembaga.
2. **Pencatatan Pemasukan (Cash In)**: Input transaksi penerimaan dana (Non-SPP, Donasi, Unit Usaha, dll.).
3. **Pengajuan & Pencatatan Pengeluaran (Cash Out)**: Pengajuan anggaran operasional, persetujuan pimpinan, dan pencatatan kas keluar.
4. **Jurnal Otomatis**: Setiap transaksi kas masuk/keluar serta integrasi SPP/PPDB secara otomatis membentuk jurnal akuntansi.
5. **Rekonsiliasi Kas**: Penyesuaian fisik kas/bank dengan catatan sistem.
6. **Laporan Keuangan**: Penyusunan Laporan BUKU KAS, Arus Kas, Neraca, dan Laba/Rugi.

---

## 2. Detail Tahapan

### Tahap 1: Pengajuan & Pencatatan Transaksi
* **Aktor:** Staf Keuangan / Bendahara.
* **Proses:** Mengisi form transaksi, memilih kategori COA, memasukkan nominal, tanggal, dan bukti nota/kuitansi.
* **Output:** Transaksi tercatat dengan nomor voucher kas.

### Tahap 2: Posting Jurnal & Buku Besar
* **Aktor:** Sistem Akuntansi.
* **Proses:** Membukukan transaksi ke akun Debet dan Kredit sesuai dengan kaidah akuntansi yang ditentukan.
* **Output:** Mutasi Akun & Saldo Realtime.

---

## 3. Rancangan Tabel Database

### A. Tabel `keuangan_coa` (Chart of Accounts)
* `id` (PK)
* `kode_akun` (varchar)
* `nama_akun` (varchar)
* `tipe_akun` (Enum: Aset, Kewajiban, Ekuitas, Pendapatan, Beban)

### B. Tabel `keuangan_transaksi`
* `id` (PK)
* `no_voucher` (varchar)
* `tanggal` (date)
* `jenis` (Enum: Pemasukan, Pengeluaran, Transfer)
* `coa_id` (FK)
* `nominal` (decimal)
* `keterangan` (text)
* `created_by` (FK User)

---

## 4. Logika Utama (Pseudo-code)

```php
// Catat Transaksi Pengeluaran
public function simpanPengeluaran($data) {
    $this->db->transStart();

    $voucherNo = $this->generateVoucherNo('OUT');
    $transId = $this->transaksiModel->insert([
        'no_voucher' => $voucherNo,
        'tanggal' => $data['tanggal'],
        'jenis' => 'Pengeluaran',
        'coa_id' => $data['coa_id'],
        'nominal' => $data['nominal'],
        'keterangan' => $data['keterangan']
    ]);

    // Journal Entry (Debet Beban, Kredit Kas)
    $this->jurnalModel->postDebitKredit($transId, $data['coa_id'], $data['kas_coa_id'], $data['nominal']);

    $this->db->transComplete();
}
```
