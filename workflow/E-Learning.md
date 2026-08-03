# Workflow Modul E-Learning

Dokumen ini menjelaskan alur kerja pembelajaran digital, distribusi bahan ajar, dan ujian CBT di dalam sistem SuperApp.

## 1. Alur Utama (Main Flow)

1. **Pembuatan Bank Materi & Tugas**: Pengajar mengunggah modul belajar (PDF, Video, PPT) dan membuat penugasan.
2. **Akses Materi Santri**: Santri membaca/mengunduh materi dan melihat tenggat waktu tugas.
3. **Pengumpulan Tugas**: Santri mengunggah file hasil pengerjaan tugas sebelum deadline.
4. **Pembuatan Bank Soal Ujian (CBT)**: Pengajar menyusun soal pilihan ganda & esai.
5. **Pelaksanaan Ujian Online**: Santri mengerjakan ujian berbasis komputer/mobile dengan batas waktu.
6. **Penilaian Otomatis & Koreksi**: Sistem secara otomatis mengevaluasi pilihan ganda dan mengkalkulasi nilai total.

---

## 2. Detail Tahapan

### Tahap 1: Pembagian Materi & Tugas
* **Aktor:** Guru / Ustaz.
* **Proses:** Memilih kelas sasaran, memasukkan judul materi, lampiran, dan instruksi tugas.
* **Output:** Materi publikasi di portal E-Learning santri.

### Tahap 2: Ujian Online (CBT)
* **Aktor:** Santri.
* **Proses:** 
  1. Santri memasukkan token ujian yang diberikan panitia.
  2. Mengerjakan soal dalam durasi menit terhitung mundur.
  3. Mengklik tombol 'Selesai' / Autocommit saat waktu habis.
* **Output:** Hasil Ujian & Analisis Jawaban.

---

## 3. Rancangan Tabel Database

### A. Tabel `elearning_materi`
* `id` (PK)
* `mapel_id` (FK)
* `guru_id` (FK)
* `judul` (varchar)
* `konten` (text)
* `file_attachment` (varchar)

### B. Tabel `elearning_ujian`
* `id` (PK)
* `judul_ujian` (varchar)
* `durasi_menit` (int)
* `token` (varchar)
* `status` (Enum: Draft, Aktif, Selesai)

---

## 4. Logika Utama (Pseudo-code)

```php
// Auto Koreksi Pilihan Ganda Ujian CBT
public function hitungSkorCbt($pesertaUjianId) {
    $jawabanSantri = $this->jawabanModel->where('peserta_id', $pesertaUjianId)->findAll();
    $skor = 0;

    foreach ($jawabanSantri as $j) {
        $soal = $this->soalModel->find($j['soal_id']);
        if ($j['jawaban_pilihan'] === $soal['kunci_jawaban']) {
            $skor += $soal['bobot'];
        }
    }

    $this->pesertaUjianModel->update($pesertaUjianId, ['total_nilai' => $skor]);
}
```
