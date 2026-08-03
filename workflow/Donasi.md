# Workflow Modul Donasi, Wakaf, & ZIS (Zakat, Infaq, Shadaqah)

Dokumen ini menjelaskan alur kerja pengelolaan donasi online/offline, wakaf pembangunan, infaq santri yatim, dan penerbitan bukti kuitansi donasi digital di dalam sistem SuperApp.

---

## 1. Alur Utama (Main Flow)

1. **Pengaturan Campaign / Program Donasi**: Admin Keuangan/Yayasan membuat program donasi (contoh: *Wakaf Pembangunan Asrama Putra*, *Infaq Beasiswa Santri Yatim*, *Bantuan Operasional Masjid*).
2. **Penerimaan Donasi (Online & Offline)**:
   - **Online:** Donatur / Wali Santri berdonasi via Payment Gateway (Virtual Account, QRIS, Credit Card) melalui Portal.
   - **Offline:** Donatur menyetor tunai/transfer langsung di kantor kasir penerimaan donasi.
3. **Verifikasi & Alokasi Dana**: Sistem memverifikasi pembayaran dan mengalokasikan dana ke rekening program terkait.
4. **Penerbitan Bukti Kuitansi Donasi Digital**: Kuitansi digital terbit secara otomatis dan dikirimkan via WhatsApp/Email donatur.
5. **Pelaporan & Akuntabilitas**: Laporan rekapitulasi penerimaan donasi dan penyaluran dana secara transparan.

---

## 2. Detail Tahapan

### Tahap 1: Pembayaran & Kuitansi Donasi
* **Aktor:** Donatur / Wali Santri & Kasir Donasi.
* **Proses:** 
  1. Donatur memilih Program Donasi & memasukkan nominal donasi.
  2. Pembayaran diverifikasi sistem / kasir.
  3. Kuitansi berstempel digital diterbitkan & laporan donasi diperbarui realtime.
* **Output:** Kuitansi Digital PDF & Laporan Realisasi Donasi.

---

## 3. Rancangan Tabel Database

### A. Tabel `donasi_program`
* `id` (PK)
* `nama_program` (varchar)
* `target_dana` (decimal)
* `dana_terkumpul` (decimal)
* `tgl_mulai` (date)
* `tgl_selesai` (date nullable)
* `status` (Enum: `Aktif`, `Selesai`)

### B. Tabel `donasi_transaksi`
* `id` (PK)
* `no_kuitansi` (varchar Unique)
* `program_id` (FK `donasi_program.id`)
* `nama_donatur` (varchar)
* `no_hp_donatur` (varchar)
* `nominal` (decimal)
* `metode_pembayaran` (varchar)
* `status` (Enum: `Pending`, `Lunas`)
* `created_at` (datetime)

---

## 4. Logika Utama (Pseudo-code)

```php
// Verifikasi & Penerbitan Kuitansi Donasi
public function verifikasiDonasi($donasiId) {
    $this->db->transStart();

    $donasi = $this->donasiModel->find($donasiId);
    $this->donasiModel->update($donasiId, ['status' => 'Lunas']);

    // Increment Dana Terkumpul di Program
    $this->programModel->where('id', $donasi['program_id'])
                       ->increment('dana_terkumpul', $donasi['nominal']);

    // Auto-Journaling ke Modul Keuangan General
    $this->keuanganService->postJurnalDonasi($donasi['nominal'], $donasi['program_id']);

    // Kirim Kuitansi via WhatsApp
    $this->notifService->sendKuitansiDonasiWa($donasiId);

    $this->db->transComplete();
}
```
