# Workflow Modul Perijinan Santri

Dokumen ini menjelaskan alur kerja perizinan keluar/pulang santri di dalam sistem SuperApp, mulai dari pengajuan izin hingga presensi kembali ke pesantren.

## 1. Alur Utama (Main Flow)

1. **Pengajuan Izin**: Santri/Wali Santri mengajukan izin keluar/pulang melalui aplikasi atau petugas perizinan.
2. **Pemeriksaan & Persetujuan**: Pengasuh / Wali Kelas / Ketertiban meninjau alasan dan memberikan persetujuan (Approve/Reject).
3. **Cetak Surat Izin**: Petugas Keamanan/Keasramaan mencetak Surat Jalan / Izin Keluar.
4. **Checkout (Keberangkatan)**: Santri melakukan scan barcode/kartu saat meninggalkan gerbang pesantren.
5. **Checkin (Kedatangan Kembali)**: Santri melakukan scan barcode/kartu saat kembali ke pesantren.
6. **Penanganan Keterlambatan**: Jika kembali melebihi batas waktu izin, sistem mencatat keterlambatan dan meneruskan ke modul Kedisiplinan/OSIS.

---

## 2. Detail Tahapan

### Tahap 1: Pengajuan Perizinan
* **Aktor:** Santri / Wali Santri / Petugas Keasramaan.
* **Proses:** Input jenis izin (Pulang, Sambangan, Berobat, Keluar Komplek), alasan, tanggal berangkat, dan rencana tanggal kembali.
* **Status:** `Pending`.

### Tahap 2: Verifikasi & Persetujuan
* **Aktor:** Wali Kelas / Ustaz Pengasuh.
* **Proses:** Memeriksa keabsahan alasan izin dan catatan kedisiplinan/SPP santri.
* **Status:** `Disetujui` atau `Ditolak`.

### Tahap 3: Pelaksanaan & Presensi Gerbang
* **Aktor:** Petugas Keamanan (Pos Satpam).
* **Proses:**
  1. Santri menunjukkan QR Code / Surat Izin saat keluar -> Status `Keluar`.
  2. Santri menunjukkan QR Code saat kembali -> Status `Selesai` (atau `Terlambat` jika lewat tenggat).
* **Output:** Log riwayat perizinan realtime.

---

## 3. Rancangan Tabel Database

### A. Tabel `perijinan`
* `id` (PK)
* `santri_id` (FK)
* `kategori_ijin` (Enum: Pulang, Berobat, Keluar Komplek, Tugas)
* `alasan` (text)
* `tgl_mulai` (datetime)
* `tgl_kembali_rencana` (datetime)
* `tgl_kembali_realisasi` (datetime)
* `status_persetujuan` (Enum: Pending, Disetujui, Ditolak)
* `status_keberadaan` (Enum: Di Dalam, Di Luar, Terlambat)
* `approved_by` (FK User)

---

## 4. Logika Utama (Pseudo-code)

```php
// Check-in Kembali Santri di Gerbang
public function scanKembali($perijinanId) {
    $ijin = $this->perijinanModel->find($perijinanId);
    $now = date('Y-m-d H:i:s');
    
    $statusKeberadaan = ($now > $ijin['tgl_kembali_rencana']) ? 'Terlambat' : 'Di Dalam';
    
    $this->perijinanModel->update($perijinanId, [
        'tgl_kembali_realisasi' => $now,
        'status_keberadaan' => $statusKeberadaan
    ]);

    if ($statusKeberadaan === 'Terlambat') {
        $this->poinPelanggaranModel->catatKeterlambatan($ijin['santri_id'], $now, $ijin['tgl_kembali_rencana']);
    }
}
```
