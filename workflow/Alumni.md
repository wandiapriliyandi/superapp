# Workflow Modul Alumni & Tracer Study

Dokumen ini menjelaskan alur kerja pendataan lulusan (alumni), tracking perguruan tinggi/pekerjaan, jejaring alumni, dan kontribusi alumni di dalam sistem SuperApp.

---

## 1. Alur Utama (Main Flow)

1. **Migrasi Santri Lulus (Alumni Initialization)**: Saat santri dinyatakan Lulus dari sekolah/pesantren, status akun dipindahkan otomatis ke database Alumni.
2. **Pengisian Tracer Study**: Alumni mengisi kuesioner kelulusan (melanjutkan ke PTN/PTS mana, bekerja di mana, atau berwirausaha).
3. **Direktori & Forum Alumni**: Alumni dapat mencari sesama teman angkatan, peta persebaran alumni, dan jejaring karir.
4. **Donasi Ikatan Alumni (IAPA / Himmah Alumni)**: Alumni dapat menyalurkan donasi/iuran keanggotaan alumni untuk pengembangan almamater.

---

## 2. Detail Tahapan

### Tahap 1: Updating Data Tracer Study
* **Aktor:** Alumni Santri.
* **Proses:** 
  1. Alumni login ke Portal Alumni menggunakan NIS / Email.
  2. Memperbarui status saat ini (Kuliah / Bekerja / Usaha / Menikah).
  3. Mengunggah informasi kontak & alamat domisili terkini.
* **Output:** Peta Distribusi & Statistik Tracer Study Alumni Realtime.

---

## 3. Rancangan Tabel Database

### A. Tabel `alumni_profil`
* `id` (PK)
* `santri_id` (FK `santri.id`)
* `tahun_lulus` (int)
* `status_saat_ini` (Enum: `Kuliah`, `Bekerja`, `Wirausaha`, `Lainnya`)
* `nama_instansi` (varchar - Nama Kampus / Perusahaan)
* `jabatan_jurusan` (varchar)
* `alamat_domisili` (text)
* `no_hp` (varchar)

---

## 4. Logika Utama (Pseudo-code)

```php
// Simpan Tracer Study Alumni
public function updateTracerStudy($santriId, $data) {
    $this->alumniModel->updateOrCreate(
        ['santri_id' => $santriId],
        [
            'status_saat_ini' => $data['status'],
            'nama_instansi' => $data['instansi'],
            'jabatan_jurusan' => $data['jurusan'],
            'alamat_domisili' => $data['alamat'],
            'no_hp' => $data['no_hp']
        ]
    );
}
```
