# Workflow Modul Al-Qur'an & Tahfidh

Dokumen ini menjelaskan alur kerja bimbingan Al-Qur'an, setoran hafalan, mutaba'ah, dan ujian Tahfidh santri di dalam sistem SuperApp.

## 1. Alur Utama (Main Flow)

1. **Mapping Kelompok Halaqah**: Penentuan Ustaz/Musyrif pengampu halaqah Al-Qur'an beserta daftar santrinya.
2. **Setoran Harian (Ziyadah & Muroja'ah)**: Santri melakukan setoran hafalan baru (Ziyadah) atau ulangan (Muroja'ah) setiap sesi halaqah (Subuh/Maghrib).
3. **Pencatatan Penilaian & Tajwid**: Musyrif mencatat Juz, Surah, Ayat, serta kelancaran & tajwid.
4. **Target & Mutaba'ah Monthly**: Pemantauan pencapaian hafalan terhadap target bulanan/semesteran.
5. **Ujian Komprehensif Tahfidh**: Ujian kenaikan Juz atau Tasmi' 1-30 Juz secara sekali duduk.
6. **Sertifikat & Syahadah**: Penerbitan Syahadah/Sertifikat Capaian Hafalan Santri.

---

## 2. Detail Tahapan

### Tahap 1: Setoran Hafalan Harian
* **Aktor:** Musyrif / Ustaz Halaqah.
* **Proses:** 
  1. Memilih nama santri dalam kelompoknya.
  2. Memilih jenis setoran (Ziyadah / Muroja'ah).
  3. Memilih Surah, Ayat mulai s/d Ayat selesai.
  4. Memilih predikat nilai (A: Mumtaz, B: Jayyid Jiddan, C: Jayyid, D: Maqbul).
* **Output:** Catatan Mutaba'ah & Capaian Terakhir.

---

## 3. Rancangan Tabel Database

### A. Tabel `alquran_halaqah`
* `id` (PK)
* `nama_halaqah` (varchar)
* `musyrif_id` (FK Karyawan)

### B. Tabel `alquran_setoran`
* `id` (PK)
* `santri_id` (FK)
* `tgl_setoran` (date)
* `tipe` (Enum: Ziyadah, Murojaah)
* `juz` (int)
* `surah` (varchar)
* `ayat_mulai` (int)
* `ayat_selesai` (int)
* `nilai_nilai` (Enum: Mumtaz, Jayyid_Jiddan, Jayyid, Maqbul)
* `catatan` (text)

---

## 4. Logika Utama (Pseudo-code)

```php
// Catat Setoran Hafalan
public function simpanSetoran($data) {
    $this->setoranModel->insert([
        'santri_id' => $data['santri_id'],
        'tgl_setoran' => date('Y-m-d'),
        'tipe' => $data['tipe'],
        'juz' => $data['juz'],
        'surah' => $data['surah'],
        'ayat_mulai' => $data['ayat_mulai'],
        'ayat_selesai' => $data['ayat_selesai'],
        'nilai' => $data['nilai']
    ]);

    // Update progress hafalan di profil santri
    $this->santriModel->update($data['santri_id'], [
        'capaian_hafalan_juz' => $data['juz']
    ]);
}
```
