# Workflow Modul Keasramaan & Kamar Santri (Dormitory Management)

Dokumen ini menjelaskan alur kerja pengelolaan gedung asrama, pembagian kamar & kasur santri, penunjukan Pembina Asrama, presensi absensi malam harian, serta inspeksi kebersihan kamar di dalam sistem SuperApp.

---

## 1. Alur Utama (Main Flow)

1. **Pengaturan Master Kompleks Asrama & Kamar**: Admin Keasramaan mendata kompleks gedung, blok, daftar kamar, kapasitas kasur/lemari, serta menunjuk Ustaz Pembina Kamar.
2. **Plotting Kamar Santri (Room Allocation)**: Penataan alokasi kamar santri setiap awal semester/tahun ajaran.
3. **Presensi Absensi Malam Harian (Night Roll-Call)**:
   - Pembina Kamar melakukan ronda jam malam (pukul 21.30 / 22.00 WIB).
   - Melakukan scan Tap Kartu Pelajar santri atau checklist via Aplikasi Mobile Guru/Pembina.
   - Sistem mencocokkan dengan data modul Perijinan (apakah santri sedang izin pulang/berobat yang sah).
4. **Alert / Peringatan Santri Ghaib**:
   - Jika santri tidak ada di kamar dan TIDAK MEMILIKI surat izin sah di sistem, status dicatat sebagai `Ghaib / Tanpa Keterangan`.
   - Sistem mengirim notifikasi darurat realtime ke Tim Keamanan Pesantren & Pengasuh.
5. **Inspeksi Kebersihan & Kerapihan Kamar**: Pembina melakukan penilaian kebersihan harian/mingguan untuk penghargaan kamar terbersih.
6. **Mutasi & Perpindahan Kamar**: Pencatatan riwayat kepindahan kamar santri.

---

## 2. Detail Tahapan

### Tahap 1: Presensi Malam Harian & Integrasi Perijinan
* **Aktor:** Ustaz Pembina Kamar / Pengasuh Asrama.
* **Proses:** 
  1. Pembina membuka aplikasi mobile -> Memilih Kamar yang dibina.
  2. Sistem menampilkan daftar santri kamar tersebut beserta indikator status izin otomatis:
     - 🟢 *Di Pesantren (Harus Ada)*
     - 🔵 *Izin Pulang / Berobat (Sah)*
     - 🟡 *Izin Keluar Komplek (Tenggat Kembali 21.00)*
  3. Pembina men-scan kartu / memverifikasi keberadaan fisik santri di kasurnya.
  4. Jika santri fisik tidak ada tanpa surat izin -> tandai `Ghaib`.
* **Output:** Rekap Presensi Malam & Alert Keamanan Realtime.

### Tahap 2: Penilaian Kebersihan & Kerapihan Kamar
* **Aktor:** Tim Inspeksi Keasramaan / Pengasuh.
* **Proses:**
  1. Penilaian indikator: Kerapihan Kasur, Kebersihan Lantai, Kerapihan Lemari, & Bebas Sampah.
  2. Input skor (1-100) dan unggah foto kondisi kamar.
* **Output:** Leaderboard Kamar Terbersih & Terkotor Mingguan.

---

## 3. Rancangan Tabel Database

### A. Tabel `asrama_kamar`
* `id` (PK)
* `nama_gedung` (varchar - Contoh: Gedung Abu Bakar)
* `nomor_kamar` (varchar - Contoh: Kamar 102)
* `kapasitas_kasur` (int)
* `pembina_id` (FK `karyawan.id` - Ustaz Pembina Kamar)

### B. Tabel `asrama_santri_kamar` (Plotting Santri)
* `id` (PK)
* `santri_id` (FK `santri.id`)
* `kamar_id` (FK `asrama_kamar.id`)
* `nomor_kasur` (varchar)
* `nomor_lemari` (varchar)
* `tgl_masuk` (date)
* `status` (Enum: `Aktif`, `Pindah`)

### C. Tabel `asrama_presensi_malam`
* `id` (PK)
* `kamar_id` (FK `asrama_kamar.id`)
* `santri_id` (FK `santri.id`)
* `tanggal` (date)
* `jam_presensi` (time)
* `status` (Enum: `Hadir_Kamar`, `Ijin_Sah`, `Sakit_UKS`, `Ghaib_Alpa`)
* `catatan` (text)
* `pembina_id` (FK `users.id`)

### D. Tabel `asrama_penilaian_kebersihan`
* `id` (PK)
* `kamar_id` (FK `asrama_kamar.id`)
* `tanggal` (date)
* `skor_kebersihan` (int)
* `foto_kamar` (varchar)
* `catatan_inspeksi` (text)
* `penilai_id` (FK `users.id`)

---

## 4. Logika Utama (Pseudo-code)

```php
// Process Presensi Malam & Trigger Alert Santri Ghaib
public function simpanPresensiMalam($kamarId, $presensiList, $pembinaId) {
    $this->db->transStart();
    $tanggal = date('Y-m-d');
    $jam = date('H:i:s');

    foreach ($presensiList as $p) {
        $santriId = $p['santri_id'];
        
        // 1. Cek apakah santri punya surat izin aktif di Modul Perijinan
        $isIjinSah = $this->perijinanModel->cekIjinAktif($santriId, $tanggal);
        
        $statusFinal = $p['status'];
        if (!$isIjinSah && $statusFinal === 'Ghaib_Alpa') {
            // Trigger Notifikasi Darurat Keamanan & Pengasuh
            $this->notifService->sendAlertSantriGhaib($santriId, $kamarId);
        } else if ($isIjinSah) {
            $statusFinal = 'Ijin_Sah';
        }

        // 2. Simpan Presensi Malam
        $this->presensiMalamModel->insert([
            'kamar_id' => $kamarId,
            'santri_id' => $santriId,
            'tanggal' => $tanggal,
            'jam_presensi' => $jam,
            'status' => $statusFinal,
            'catatan' => $p['catatan'] ?? '',
            'pembina_id' => $pembinaId
        ]);
    }

    $this->db->transComplete();
}
```
