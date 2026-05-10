# Workflow Modul PPDB (Penerimaan Peserta Didik Baru)

Dokumen ini menjelaskan alur kerja aplikasi PPDB di dalam sistem SuperApp, mulai dari pendaftaran calon santri hingga menjadi santri aktif.

## 1. Alur Utama (Main Flow)

1. **Pendaftaran Online**: Calon santri mengisi data biodata awal.
2. **Pembayaran Registrasi**: Upload bukti bayar dan verifikasi oleh Admin Keuangan.
3. **Penjadwalan Tes Seleksi**: Admin menentukan jadwal tes (waktu & tempat).
4. **Pelaksanaan Tes**: Input nilai hasil seleksi ke dalam sistem.
5. **Verifikasi Berkas**: Validasi dokumen fisik (KK, Akta, Ijazah).
6. **Keputusan Kelulusan**: Penentuan apakah calon santri diterima atau tidak.
7. **Daftar Ulang**: Proses administrasi bagi yang sudah diterima.
8. **Kirim ke Data Santri**: Data dipindahkan ke tabel utama `santri` dan mendapatkan NIS.

---

## 2. Detail Tahapan

### Tahap 1: Pendaftaran Calon Santri
*   **Aktor:** Calon Santri / Orang Tua.
*   **Proses:** Mengisi form biodata dasar (Nama, Alamat, Pilihan Kelas/Jenjang).
*   **Output:** Nomor Pendaftaran (Contoh: `PPDB-2026-0001`).
*   **Status:** `Baru`.

### Tahap 2: Pembayaran Registrasi
*   **Aktor:** Calon Santri & Admin Keuangan.
*   **Proses:** Upload bukti transfer. Admin memverifikasi di modul Keuangan.
*   **Status:** `Lunas`.

### Tahap 3: Penentuan Jadwal Tes Seleksi
*   **Aktor:** Admin PPDB.
*   **Proses:** Penentuan tanggal, waktu, dan ruangan tes. Calon santri cetak kartu ujian.
*   **Status:** `Terjadwal`.

### Tahap 4: Verifikasi Berkas
*   **Aktor:** Panitia PPDB.
*   **Proses:** Validasi fisik dokumen (KK, Ijazah, Akta Kelahiran, dll).
*   **Status:** `Berkas Valid`.

### Tahap 5: Sinkronisasi ke Data Santri Utama
*   **Aktor:** Sistem (Trigger Admin).
*   **Proses:** 
    1.  Cek semua syarat (Lunas, Lulus Tes, Berkas Valid).
    2.  Salin data dari tabel `ppdb_pendaftaran` ke tabel `santri`.
    3.  Generate **NIS (Nomor Induk Santri)** secara otomatis.
    4.  Update status di PPDB menjadi `Selesai (Aktif)`.

---

## 3. Rancangan Tabel Database

### A. Tabel `ppdb_pendaftaran`
Tabel ini menampung data sementara calon santri.
*   `id` (PK)
*   `no_pendaftaran` (Unique)
*   `pilihan_kelas`
*   `nama_lengkap`
*   `nik`
*   `status_ppdb` (Enum: Baru, Lunas, Terjadwal, Lulus, Ditolak, Selesai)

### B. Tabel `ppdb_pembayaran`
*   `id` (PK)
*   `pendaftaran_id` (FK)
*   `jumlah_bayar`
*   `bukti_bayar` (Filename)
*   `status_bayar` (Enum: Menunggu, Terverifikasi, Ditolak)

---

## 4. Logika Perpindahan Data (Promotion Logic)

Saat calon santri dinyatakan diterima, sistem akan menjalankan fungsi untuk menyalin data ke tabel utama.

```php
// Contoh Logika (Pseudo-code)
public function terimaSantri($id) {
    $calon = $this->ppdbModel->find($id);
    $nisBaru = $this->santriModel->generateNis($calon['pilihan_kelas']);

    $this->santriModel->save([
        'nis' => $nisBaru,
        'nama_lengkap' => $calon['nama_lengkap'],
        'nik' => $calon['nik'],
        'status_santri' => 'Aktif'
    ]);
}
```
