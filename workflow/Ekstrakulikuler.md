# Workflow Modul Ekstrakulikuler & Minat Bakat Santri

Dokumen ini menjelaskan alur kerja pendaftaran kegiatan ekstrakulikuler/klub santri, presensi latihan mingguan, dan penilaian bakat santri di dalam sistem SuperApp.

---

## 1. Alur Utama (Main Flow)

1. **Pengaturan Master Ekskul**: Admin Kesiswaan mendaftarkan jenis ekskul (Pramuka, Pencak Silat, Kaligrafi, Bahasa Arab/Inggris, Hadroh, Qira'ah, Olahraga) dan penunjukan Pembina/Pelatih Ekskul.
2. **Pendaftaran Ekskul (Open Recruitment)**: Santri memilih ekskul wajib & pilihan melalui Portal Santri / Wali.
3. **Presensi Latihan Mingguan**: Pelatih/Pembina Ekskul mencatat kehadiran latihan santri menggunakan scan Kartu Pelajar Multiguna (RFID/QR/Barcode) atau checklist HP.
4. **Penilaian Capaian & Sertifikasi**: Pembina menginput nilai keaktifan, kedisiplinan, dan prestasi ekskul di akhir semester.
5. **Cetak Rapor Ekstrakulikuler**: Nilai ekskul terintegrasi otomatis ke Rapor Akademik/Keasramaan Santri.

---

## 2. Detail Tahapan

### Tahap 1: Presensi Latihan & Penilaian Ekskul
* **Aktor:** Pembina / Pelatih Ekstrakulikuler.
* **Proses:** 
  1. Pembina membuka menu **Presensi Ekskul** pada jadwal latihan.
  2. Santri men-scan Kartu Pelajar pada alat/HP Pembina.
  3. Di akhir semester, Pembina mengisi predikat (A: Sangat Baik, B: Baik, C: Cukup) dan deskripsi capaian bakat.
* **Output:** Presensi Latihan & Nilai Rapor Ekskul.

---

## 3. Rancangan Tabel Database

### A. Tabel `ekskul_master`
* `id` (PK)
* `nama_ekskul` (varchar)
* `kategori` (Enum: `Wajib`, `Pilihan`)
* `pembina_id` (FK `karyawan.id`)
* `hari_latihan` (varchar)
* `jam_latihan` (time)

### B. Tabel `ekskul_anggota`
* `id` (PK)
* `ekskul_id` (FK `ekskul_master.id`)
* `santri_id` (FK `santri.id`)
* `tahun_ajaran_id` (FK)
* `status` (Enum: `Aktif`, `Lulus`)

### C. Tabel `ekskul_nilai`
* `id` (PK)
* `anggota_id` (FK `ekskul_anggota.id`)
* `predikat` (Enum: `A`, `B`, `C`, `D`)
* `deskripsi_capaian` (text)

---

## 4. Logika Utama (Pseudo-code)

```php
// Simpan Nilai Ekskul Semesteran
public function simpanNilaiEkskul($anggotaId, $predikat, $deskripsi) {
    $this->nilaiEkskulModel->updateOrCreate(
        ['anggota_id' => $anggotaId],
        [
            'predikat' => $predikat,
            'deskripsi_capaian' => $deskripsi
        ]
    );
}
```
