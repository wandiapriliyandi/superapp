# Workflow Modul SPP (Sumbangan Pembinaan Pendidikan)

Dokumen ini menjelaskan alur kerja pengelolaan tagihan dan pembayaran SPP bulanan santri di dalam sistem SuperApp.

## 1. Alur Utama (Main Flow)

1. **Penetapan Master Tarif SPP**: Admin Keuangan menentukan kategori & besaran tarif SPP per tingkat/kategori santri.
2. **Mapping Tarif Santri**: Sistem/Admin mencocokkan tarif SPP khusus ke masing-masing santri (jika ada beasiswa/potongan).
3. **Generate Tagihan Bulanan**: Sistem membuat tagihan SPP secara otomatis setiap awal bulan.
4. **Pembayaran Tagihan**: Santri/Wali Murid melakukan pembayaran via kasir atau upload bukti transfer.
5. **Verifikasi & Pelunasan**: Admin/Sistem memverifikasi pembayaran dan mengubah status tagihan menjadi `Lunas`.
6. **Kuitansi & Laporan**: Cetak bukti bayar (kuitansi) dan rekap laporan tunggakan/pelunasan SPP.

---

## 2. Detail Tahapan

### Tahap 1: Pengaturan Master Tarif SPP
* **Aktor:** Admin Keuangan.
* **Proses:** Mengatur nama tarif, nominal standar per bulan, dan tingkat kelas.
* **Output:** Data Master Tarif.

### Tahap 2: Pembentukan Tagihan Bulanan (Generate Bill)
* **Aktor:** Sistem (Trigger Rutin Bulanan / Manual Admin).
* **Proses:** Mengiterasi data santri aktif dan membuat record tagihan baru untuk bulan & tahun berjalan.
* **Output:** Record tagihan status `Belum Bayar`.

### Tahap 3: Pembayaran & Pembukuan
* **Aktor:** Wali Santri / Kasir Keuangan.
* **Proses:** 
  1. Input nominal bayar dan metode bayar (Tunai / Transfer).
  2. Sistem memvalidasi kelengkapan pembayaran.
  3. Mengubah status tagihan menjadi `Lunas` atau `Sebagian`.
* **Output:** Bukti Pembayaran (Kuitansi PDF / Struk).

---

## 3. Rancangan Tabel Database

### A. Tabel `spp_tarif`
* `id` (PK)
* `nama_tarif` (varchar)
* `nominal` (decimal)
* `tingkat` (varchar)

### B. Tabel `spp_santri_tarif`
* `id` (PK)
* `santri_id` (FK)
* `tarif_id` (FK)
* `potongan` (decimal)

### C. Tabel `spp_tagihan`
* `id` (PK)
* `santri_id` (FK)
* `bulan` (int)
* `tahun` (int)
* `nominal_tagihan` (decimal)
* `status` (Enum: Belum Bayar, Sebagian, Lunas)

### D. Tabel `spp_pembayaran`
* `id` (PK)
* `tagihan_id` (FK)
* `tgl_bayar` (datetime)
* `jumlah_bayar` (decimal)
* `metode_bayar` (Enum: Tunai, Transfer, Payment Gateway)
* `bukti_bayar` (varchar)
* `status_verifikasi` (Enum: Pending, Verifikasi, Ditolak)

---

## 4. Logika Utama (Pseudo-code)

```php
// Generate Tagihan Bulanan
public function generateTagihanBulanan($bulan, $tahun) {
    $santriAktif = $this->santriModel->where('status_santri', 'Aktif')->findAll();
    
    foreach ($santriAktif as $santri) {
        $tarif = $this->sppTarifModel->getTarifForSantri($santri['id']);
        $this->sppTagihanModel->insert([
            'santri_id' => $santri['id'],
            'bulan' => $bulan,
            'tahun' => $tahun,
            'nominal_tagihan' => $tarif['nominal'] - $tarif['potongan'],
            'status' => 'Belum Bayar'
        ]);
    }
}
```
