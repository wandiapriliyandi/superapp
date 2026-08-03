# Workflow Modul Perpustakaan

Dokumen ini menjelaskan alur kerja pengelolaan pustaka, sirkulasi peminjaman, pengembalian, dan denda buku di dalam sistem SuperApp.

## 1. Alur Utama (Main Flow)

1. **Katalogisasi Buku**: Petugas meregistrasi pustaka baru (ISBN, Judul, Pengarang, Kategori, Stok).
2. **Kartu Anggota Perpustakaan**: Santri & Guru terdaftar sebagai anggota peminjam.
3. **Peminjaman Buku**: Santri memilih buku dan petugas mencatat batas tanggal pengembalian.
4. **Pengembalian Buku**: Petugas memverifikasi kondisi fisik buku dan tanggal pengembalian.
5. **Perhitungan Denda**: Jika pengembalian terlambat atau fisik buku rusak/hilang, sistem menghitung denda otomatis.
6. **Laporan Sirkulasi**: Rekapitulasi buku populer, riwayat peminjaman, dan pembayaran denda.

---

## 2. Detail Tahapan

### Tahap 1: Peminjaman & Pengembalian Buku
* **Aktor:** Petugas Perpustakaan & Santri.
* **Proses:** 
  1. Scan barcode anggota santri.
  2. Scan kode ISBN/Eksemplar buku.
  3. Sistem menetapkan batas pinjam (misal: 7 hari).
* **Output:** Struk Peminjaman & Update Stok Buku (`stok_tersedia` berkurang 1).

---

## 3. Rancangan Tabel Database

### A. Tabel `perpus_buku`
* `id` (PK)
* `isbn` (varchar)
* `judul` (varchar)
* `pengarang` (varchar)
* `stok_total` (int)
* `stok_tersedia` (int)

### B. Tabel `perpus_peminjaman`
* `id` (PK)
* `santri_id` (FK)
* `buku_id` (FK)
* `tgl_pinjam` (date)
* `tgl_kembali_rencana` (date)
* `tgl_kembali_realisasi` (date)
* `denda` (decimal)
* `status` (Enum: Dipinjam, Kembali, Denda)

---

## 4. Logika Utama (Pseudo-code)

```php
// Hitung Denda Keterlambatan
public function prosesPengembalian($peminjamanId) {
    $p = $this->peminjamanModel->find($peminjamanId);
    $tglKembali = date('Y-m-d');
    
    $dendaPerHari = 1000;
    $dendaTotal = 0;

    if ($tglKembali > $p['tgl_kembali_rencana']) {
        $hariTerlambat = (strtotime($tglKembali) - strtotime($p['tgl_kembali_rencana'])) / 86400;
        $dendaTotal = $hariTerlambat * $dendaPerHari;
    }

    $this->peminjamanModel->update($peminjamanId, [
        'tgl_kembali_realisasi' => $tglKembali,
        'denda' => $dendaTotal,
        'status' => 'Kembali'
    ]);

    $this->bukuModel->incrementStok($p['buku_id']);
}
```
