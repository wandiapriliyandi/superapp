# Workflow Modul Piket Harian & Kerja Bakti (Ro'an Santri)

Dokumen ini menjelaskan alur kerja penjadwalan piket kebersihan harian, verifikasi pelaksanaan piket oleh **Guru/Ustaz Pembina Piket**, agenda kerja bakti masal (Ro'an), dan integrasinya dengan Modul Kedisiplinan & OSIS di dalam sistem SuperApp.

---

## 1. Konsep Verifikasi & Akses Perangkat

> [!IMPORTANT]
> **Aktor Verifikasi & Kebijakan Perangkat (Tanpa HP Santri):**
> * Karena santri (termasuk pengurus OSIS) **tidak diperkenankan membawa/menggunakan smartphone** di pesantren, seluruh proses pemindaian, verifikasi kebersihan, dan pengunggahan foto bukti piket dilakukan **SEPENUHNYA OLEH GURU / USTAZ PEMBINA PIKET YANG DITUNJUK**.
> * Pengurus OSIS fisik hanya bertindak sebagai koordinator kebersihan lapangan secara manual.

---

## 2. Alur Utama (Main Flow)

1. **Pengaturan Master Area & Shift Piket**: Admin Keasramaan/Guru Piket mendata lokasi area piket (Masjid, Halaman Utama, Dapur, Toilet/Kamar Mandi, Koridor Asrama) dan waktu shift piket (Piket Pagi & Sore).
2. **Penjadwalan Rotasi Piket (Auto-Roster)**: Sistem membagi giliran piket santri secara merata berdasarkan kelompok kamar/kelas per hari.
3. **Pelaksanaan & Verifikasi Piket Harian oleh Guru Pembina**:
   - Santri melaksanakan tugas piket di lokasi masing-masing.
   - **Guru/Ustaz Pembina Piket** yang ditunjuk menginspeksi area piket menggunakan HP/Tablet Guru.
   - Guru memeriksa daftar nama santri, mencatat status kehadiran piket, dan mengambil foto bukti kebersihan area.
4. **Agenda Kerja Bakti Masal (Ro'an Ahad)**:
   - Pengurus menjadwalkan kegiatan gotong royong masal (Ro'an).
   - Presensi kehadiran santri dilakukan oleh **Guru Pembina Pos** melalui scan Kartu Pelajar santri (RFID / QR Code / Barcode).
5. **Pencatatan Konsekuensi & Poin Kedisiplinan**:
   - Santri yang **mangkir piket/kerja bakti** tanpa alasan sah akan otomatis mendapatkan penambahan **Poin Pelanggaran** yang terintegrasi ke Modul OSIS/Kedisiplinan.
   - Santri piket teladan mendapatkan poin apresiasi/kebaikan.

---

## 3. Detail Tahapan

### Tahap 1: Verifikasi Pelaksanaan Piket oleh Guru Pembina
* **Aktor:** Guru / Ustaz Pembina Piket yang Ditungjuk.
* **Proses:** 
  1. Guru Pembina membuka Portal Guru di HP pada menu **Piket Harian**.
  2. Sistem menampilkan daftar nama santri bertugas dan lokasi area piketnya pada tanggal berjalan.
  3. Guru memeriksa kehadiran fisik santri & kualitas kebersihan area.
  4. Guru memilih status kehadiran: `Hadir (Bersih)`, `Terlambat`, atau `Mangkir (Alpa)`.
  5. Guru mengambil foto bukti kebersihan lokasi via kamera HP Guru.
* **Output:** Status piket terverifikasi sah oleh Guru & Poin Pelanggaran otomatis terkirim jika santri mangkir.

### Tahap 2: Presensi Kerja Bakti Masal (Ro'an)
* **Aktor:** Guru Pembina Pos & Santri.
* **Proses:**
  1. Guru Pembina membuka pos presensi kerja bakti per zona lokasi dengan membawa reader/HP.
  2. Santri menunjukkan Kartu Pelajar (RFID / QR / Barcode) untuk dipindai oleh Guru Pembina.
  3. Rekap presensi otomatis memisahkan santri yang hadir, sakit, izin, atau alpa.
* **Output:** Rekap Kehadiran Ro'an & Poin Kedisiplinan.

---

## 4. Rancangan Tabel Database

### A. Tabel `piket_area`
* `id` (PK)
* `nama_area` (varchar - Contoh: Masjid Utama, Toilet Lantai 1)
* `kategori` (Enum: `Harian`, `Kerja_Bakti`)
* `deskripsi_tugas` (text)

### B. Tabel `piket_jadwal_harian`
* `id` (PK)
* `santri_id` (FK `santri.id`)
* `area_id` (FK `piket_area.id`)
* `hari_index` (int - 1: Senin s/d 7: Minggu)
* `shift` (Enum: `Pagi`, `Sore`)
* `status_aktif` (boolean)

### C. Tabel `piket_presensi_log`
* `id` (PK)
* `jadwal_id` (FK `piket_jadwal_harian.id` nullable)
* `santri_id` (FK `santri.id`)
* `tanggal` (date)
* `status` (Enum: `Hadir_Bersih`, `Terlambat`, `Mangkir_Alpa`, `Ijin_Sah`)
* `foto_bukti` (varchar nullable)
* `guru_pembina_id` (FK `karyawan.id` - Guru / Ustaz Pembina yang Menverifikasi)

### D. Tabel `piket_kerja_bakti` (Ro'an Masal)
* `id` (PK)
* `nama_kegiatan` (varchar - Contoh: Ro'an Akbar Ahad Bersih)
* `tanggal` (date)
* `jam_mulai` (time)
* `jam_selesai` (time)
* `keterangan` (text)

---

## 5. Logika Utama (Pseudo-code)

```php
// Verifikasi Piket Harian oleh Guru Pembina & Auto-Trigger Poin Pelanggaran
public function verifikasiPiketOlehGuru($presensiData, $guruPembinaId) {
    $this->db->transStart();
    $tanggal = date('Y-m-d');

    foreach ($presensiData as $p) {
        $santriId = $p['santri_id'];
        $status = $p['status'];

        // 1. Simpan Presensi Log Piket yang Di-input oleh Guru Pembina
        $this->presensiPiketModel->insert([
            'jadwal_id' => $p['jadwal_id'],
            'santri_id' => $santriId,
            'tanggal' => $tanggal,
            'status' => $status,
            'foto_bukti' => $p['foto_bukti'] ?? null,
            'guru_pembina_id' => $guruPembinaId
        ]);

        // 2. Jika Status Mangkir (Alpa Piket), Trigger Poin Pelanggaran ke Modul OSIS
        if ($status === 'Mangkir_Alpa') {
            $this->osisService->catatPelanggaran(
                $santriId, 
                'PELANGGARAN_PIKET', 
                'Mangkir tugas piket kebersihan harian pada tanggal ' . $tanggal,
                5 // Tambah 5 Poin Pelanggaran
            );
        }
    }

    $this->db->transComplete();
}
```
