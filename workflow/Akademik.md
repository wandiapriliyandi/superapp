# Workflow Modul Akademik (Kurikulum Formal & Diniyah Pesantren)

Dokumen ini menjelaskan alur kerja komprehensif pengelolaan Akademik Formal (Kemdikbud/Kemenag) dan Akademik Diniyah Pesantren (Kitab Kuning/Salafiyah), penataan Rombel, presensi KBM tap kartu, sistem penilaian terstruktur, kenaikan kelas otomatis, hingga penerbitan Rapor Digital di dalam sistem SuperApp.

---

## 1. Alur Utama (Main Flow)

1. **Pengaturan Tahun Ajaran & Semester**: Admin Kurikulum menetapkan Tahun Ajaran (contoh: `2026/2027 Ganjil`) dan kalender akademik.
2. **Manajemen Rombel & Dual Kurikulum**:
   - **Kurikulum Formal:** Penataan kelas SMP/SMA/MTs/MA dan pemetaan mata pelajaran kurikulum nasional.
   - **Kurikulum Diniyah:** Penataan jenjang Diniyah (Ula, Wustho, Ulya / Kitab Kuning) dan kelas Sorogan/Bandongan.
3. **Penugasan Guru & Plotting Jadwal**: Pemetaan Guru Pengampu ke Mata Pelajaran/Kitab serta pembuatan Jadwal Pelajaran Mingguan per ruangan.
4. **Presensi KBM Harian (Tap Kartu / Portal Guru)**:
   - Guru membuka jam KBM dan melakukan presensi santri via scan Tap Kartu Pelajar (RFID/QR/Barcode) atau checklist HP Guru.
   - Mengisi Jurnal Mengajar Harian (Materi & Bab yang diajarkan).
5. **Asesmen & Penilaian Multi-Komponen**:
   - Input Nilai Harian (Formatif), Tugas, PTS (Mid), PAS/PAT (Akhir Semester), serta Nilai Praktek/Ujian Syafahi (Lisan Kitab).
6. **Kenaikan Kelas & Kelulusan Automated**:
   - Evaluasi multi-syarat kenaikan kelas: **Syarat KKM Nilai**, **Presensi KBM (Min. 85%)**, **Bebas SP3 Kedisiplinan**, dan **Capaian Target Hafalan Al-Qur'an**.
7. **Generate Leger & Lock Rapor Digital**: Pembentukan Leger Nilai, cetak Rapor PDF (Format Formal & Format Diniyah), serta penguncian (*locking*) nilai semester.

---

## 2. Detail Tahapan

### Tahap 1: Dual Kurikulum (Formal & Diniyah)
* **Aktor:** Admin Kurikulum & Kasi Akademik.
* **Proses:** 
  1. Input Mata Pelajaran Formal (Matematika, IPA, Bahasa Indonesia, dll).
  2. Input Mata Pelajaran Diniyah (Nahwu, Shorof, Fiqih, Aqidah, Hadits, Tajwid).
  3. Mengatur bobot komponen penilaian masing-masing kurikulum.
* **Output:** Master Kurikulum Formal & Diniyah.

### Tahap 2: Evaluasi Kenaikan Kelas (Promotion Engine)
* **Aktor:** Sistem (Trigger Wali Kelas & Kurikulum).
* **Proses:**
  Sistem mengecek 4 syarat kelayakan naik kelas untuk setiap santri:
  1. **Nilai Akademik:** Rata-rata nilai akhir >= KKM (misal KKM = 75).
  2. **Presensi KBM:** Kehadiran kelas >= 85%.
  3. **Kedisiplinan:** Poin pelanggaran < 100 (Tidak sedang SP3).
  4. **Tahfidh:** Memenuhi target minimal hafalan juz semesteran.
* **Output:** Draf Keputusan Kenaikan Kelas (Naik Kelas / Tinggal Kelas).

---

## 3. Rancangan Tabel Database

### A. Tabel `akademik_tahun_ajaran`
* `id` (PK)
* `tahun` (varchar - Contoh: 2026/2027)
* `semester` (Enum: `Ganjil`, `Genap`)
* `tgl_mulai` (date)
* `tgl_selesai` (date)
* `is_active` (boolean)
* `is_locked` (boolean - Penguncian Rapor)

### B. Tabel `akademik_mapel`
* `id` (PK)
* `kode_mapel` (varchar Unique)
* `nama_mapel` (varchar)
* `kategori_kurikulum` (Enum: `Formal`, `Diniyah`)
* `kkm` (decimal)

### C. Tabel `akademik_rombel` (Kelas)
* `id` (PK)
* `tahun_ajaran_id` (FK `akademik_tahun_ajaran.id`)
* `nama_kelas` (varchar - Contoh: X IPA 1 / Wustho B)
* `tingkat` (varchar)
* `kategori` (Enum: `Formal`, `Diniyah`)
* `wali_kelas_id` (FK `karyawan.id`)

### D. Tabel `akademik_jadwal`
* `id` (PK)
* `rombel_id` (FK `akademik_rombel.id`)
* `mapel_id` (FK `akademik_mapel.id`)
* `guru_id` (FK `karyawan.id`)
* `hari_index` (int - 1: Senin s/d 7: Minggu)
* `jam_mulai` (time)
* `jam_selesai` (time)
* `ruangan` (varchar)

### E. Tabel `akademik_presensi_kbm`
* `id` (PK)
* `jadwal_id` (FK `akademik_jadwal.id`)
* `santri_id` (FK `santri.id`)
* `tanggal` (date)
* `status` (Enum: `Hadir`, `Izin`, `Sakit`, `Alpa`)
* `materi_jurnal` (text nullable)

### F. Tabel `akademik_nilai`
* `id` (PK)
* `rombel_id` (FK `akademik_rombel.id`)
* `mapel_id` (FK `akademik_mapel.id`)
* `santri_id` (FK `santri.id`)
* `nilai_tugas` (decimal)
* `nilai_uts` (decimal)
* `nilai_uas` (decimal)
* `nilai_syafahi` (decimal nullable - Ujian Lisan Kitab)
* `nilai_akhir` (decimal)
* `predikat` (Enum: `A`, `B`, `C`, `D`)
* `catatan_guru` (text nullable)

---

## 4. Logika Utama (Pseudo-code)

### A. Kalkulasi Nilai Akhir Multi-Bobot & Predikat
```php
public function hitungNilaiAkhir($santriId, $mapelId, $rombelId) {
    $n = $this->nilaiModel->where([
        'santri_id' => $santriId,
        'mapel_id' => $mapelId,
        'rombel_id' => $rombelId
    ])->first();

    $mapel = $this->mapelModel->find($mapelId);

    // Hitung Nilai Akhir (30% Tugas + 30% UTS + 40% UAS / Syafahi)
    if ($mapel['kategori_kurikulum'] === 'Diniyah') {
        // Kurikulum Diniyah ada Ujian Syafahi (Lisan)
        $nilaiAkhir = ($n['nilai_tugas'] * 0.2) + ($n['nilai_uts'] * 0.2) + ($n['nilai_uas'] * 0.3) + ($n['nilai_syafahi'] * 0.3);
    } else {
        // Kurikulum Formal Standard
        $nilaiAkhir = ($n['nilai_tugas'] * 0.3) + ($n['nilai_uts'] * 0.3) + ($n['nilai_uas'] * 0.4);
    }

    // Penentuan Predikat (A, B, C, D)
    $predikat = 'D';
    if ($nilaiAkhir >= 90) $predikat = 'A';
    else if ($nilaiAkhir >= 80) $predikat = 'B';
    else if ($nilaiAkhir >= $mapel['kkm']) $predikat = 'C';

    $this->nilaiModel->update($n['id'], [
        'nilai_akhir' => $nilaiAkhir,
        'predikat' => $predikat
    ]);
}
```

### B. Engine Evaluasi Kelayakan Kenaikan Kelas Santri
```php
public function evaluasiKenaikanKelas($santriId, $tahunAjaranId) {
    // 1. Cek Rata-Rata Nilai Akademik
    $nilaiList = $this->nilaiModel->getNilaiSantriSemester($santriId, $tahunAjaranId);
    $mapelDiBawahKkm = 0;
    foreach ($nilaiList as $n) {
        if ($n['nilai_akhir'] < $n['kkm']) $mapelDiBawahKkm++;
    }

    // 2. Cek Persentase Kehadiran KBM
    $persenHadir = $this->presensiKbmModel->getPersentaseHadir($santriId, $tahunAjaranId);

    // 3. Cek Status Kedisiplinan (Poin Pelanggaran OSIS)
    $totalPoin = $this->osisService->getTotalPoinPelanggaran($santriId);

    // 4. Keputusan Kenaikan Kelas
    if ($mapelDiBawahKkm <= 2 && $persenHadir >= 85.0 && $totalPoin < 100) {
        return [
            'status' => 'Naik Kelas',
            'catatan' => 'Lolos seluruh kriteria kelayakan akademik & karakter.'
        ];
    } else {
        return [
            'status' => 'Tinggal Kelas / Pertimbangan',
            'catatan' => "Mapel < KKM: {$mapelDiBawahKkm}, Kehadiran: {$persenHadir}%, Poin Pelanggaran: {$totalPoin}"
        ];
    }
}
```
