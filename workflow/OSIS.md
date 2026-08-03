# Workflow Modul OSIS & Kedisiplinan Santri

Dokumen ini menjelaskan alur kerja kegiatan keorganisasian santri (OSIS/ISMP) serta pencatatan poin kedisiplinan & pelanggaran santri di dalam sistem SuperApp.

## 1. Alur Utama (Main Flow)

1. **Struktur & Kepengurusan OSIS**: Pendataan pengurus OSIS santri (Ketua, Sekretaris, Bendahara, Departemen).
2. **Program Kerja & Event**: Pengajuan proposal kegiatan santri dan persetujuan Pengasuhan/Kesiswaan.
3. **Pencatatan Poin Pelanggaran**: Input pelanggaran aturan pesantren (Ringan, Sedang, Berat) oleh petugas ketertiban/OSIS.
4. **Pencatatan Poin Prestasi**: Input apresiasi poin kebaikan/prestasi santri.
5. **Akumulasi Poin & Akumulasi SP**: Sistem mengkalkulasi saldo poin santri dan otomatis memicu pemberian Surat Peringatan (SP 1, SP 2, SP 3).
6. **Sidang Kedisiplinan**: Penanganan santri dengan akumulasi poin pelanggaran batas maksimal.

---

## 2. Detail Tahapan

### Tahap 1: Pencatatan Pelanggaran Santri
* **Aktor:** Tim Ketertiban / Pengurus OSIS Departemen Keamanan.
* **Proses:** 
  1. Cari nama santri pelanggar.
  2. Pilih jenis pelanggaran dari master aturan (misal: Terlambat Berjamaah = 5 Poin, Merokok = 50 Poin).
  3. Input tanggal, tempat, dan foto bukti jika ada.
* **Output:** Record Pelanggaran & Poin Berkurang/Bertambah.

---

## 3. Rancangan Tabel Database

### A. Tabel `osis_pelanggaran_master`
* `id` (PK)
* `nama_pelanggaran` (varchar)
* `kategori` (Enum: Ringan, Sedang, Berat)
* `poin` (int)

### B. Tabel `osis_santri_pelanggaran`
* `id` (PK)
* `santri_id` (FK)
* `pelanggaran_id` (FK)
* `tanggal` (date)
* `keterangan` (text)
* `petugas_id` (FK User)

---

## 4. Logika Utama (Pseudo-code)

```php
// Input Pelanggaran & Cek Ambang Batas SP
public function catatPelanggaran($santriId, $pelanggaranId) {
    $pelanggaran = $this->masterPelanggaranModel->find($pelanggaranId);
    
    $this->santriPelanggaranModel->insert([
        'santri_id' => $santriId,
        'pelanggaran_id' => $pelanggaranId,
        'tanggal' => date('Y-m-d')
    ]);

    // Hitung Total Poin Pelanggaran Santri
    $totalPoin = $this->santriPelanggaranModel->getTotalPoin($santriId);

    if ($totalPoin >= 100) {
        $this->spModel->terbitkanSp($santriId, 'SP3 (Tindakan Lanjutan)');
    } else if ($totalPoin >= 50) {
        $this->spModel->terbitkanSp($santriId, 'SP2');
    } else if ($totalPoin >= 25) {
        $this->spModel->terbitkanSp($santriId, 'SP1');
    }
}
```
