# Workflow Modul Sarana & Prasarana (Sarpras)

Dokumen ini menjelaskan alur kerja inventarisasi aset, pemeliharaan, dan pengajuan sarana prasarana di dalam sistem SuperApp.

## 1. Alur Utama (Main Flow)

1. **Pencatatan Aset / Barang**: Admin Sarpras merata pendataan gedung, kamar, kelas, dan barang inventaris beserta kode QR.
2. **Penempatan & Mutasi Barang**: Alokasi aset ke lokasi gedung/ruangan tertentu dan pelabelan barang.
3. **Pelaporan Kerusakan**: Pengurus keasramaan/pengajar melaporkan barang rusak atau butuh perbaikan.
4. **Pengajuan Perbaikan / Pengadaan**: Admin Sarpras menyusun rencana perbaikan/pembelian baru ke modul Keuangan.
5. **Pemeliharaan (Maintenance)**: Pencatatan histori perbaikan dan penggantian suku cadang barang.
6. **Penghapusan Aset**: Pemutihan barang rusak berat yang tidak layak pakai.

---

## 2. Detail Tahapan

### Tahap 1: Inventarisasi & QR Labeling
* **Aktor:** Staff Sarpras.
* **Proses:** Input data barang (Merk, Tahun Pembelian, Kondisi Baik/Rusak ringan), cetak stiker QR Code aset.
* **Output:** Master Aset & QR Code Barang.

### Tahap 2: Laporan Kerusakan & Perbaikan
* **Aktor:** Pengguna (Ustaz/Pengurus) & Staff Sarpras.
* **Proses:** Scan QR barang -> Input foto kerusakan -> Verifikasi petugas -> Perbaikan selesai.
* **Output:** Riwayat Kerusakan & Perbaikan Aset.

---

## 3. Rancangan Tabel Database

### A. Tabel `sarpras_barang`
* `id` (PK)
* `kode_barang` (varchar Unique)
* `nama_barang` (varchar)
* `kategori` (varchar)
* `lokasi_ruang` (varchar)
* `kondisi` (Enum: Baik, Rusak Ringan, Rusak Berat)

### B. Tabel `sarpras_perbaikan`
* `id` (PK)
* `barang_id` (FK)
* `tgl_lapor` (date)
* `deskripsi_rusak` (text)
* `biaya_perbaikan` (decimal)
* `status` (Enum: Dilaporkan, Proses Perbaikan, Selesai)

---

## 4. Logika Utama (Pseudo-code)

```php
// Laporkan Kerusakan Barang
public function laporkanKerusakan($data) {
    $this->sarprasPerbaikanModel->insert([
        'barang_id' => $data['barang_id'],
        'tgl_lapor' => date('Y-m-d'),
        'deskripsi_rusak' => $data['deskripsi'],
        'status' => 'Dilaporkan'
    ]);

    $this->sarprasBarangModel->update($data['barang_id'], [
        'kondisi' => 'Rusak Ringan'
    ]);
}
```
