# Workflow Portal Guru & Ustaz (Web / Mobile App)

Dokumen ini menjelaskan alur kerja, hak akses, dan fitur-fitur portal mandiri pengajar (*Teacher Portal*) untuk Guru, Ustaz Pengampu, dan Wali Kelas di dalam sistem SuperApp.

---

## 1. Peran & Akses Pengguna

* **Guru / Ustaz Pengampu**: Memiliki akses ke kelas & mata pelajaran yang diampunya (Presensi KBM, Penilaian, E-Learning).
* **Musyrif / Ustaz Tahfidh**: Memiliki akses ke kelompok halaqah Al-Qur'an (Input setoran hafalan harian & nilai tajwid).
* **Wali Kelas / Pengasuh**: Memiliki akses persetujuan izin santri kelasnya, catatan kedisiplinan, dan verifikasi rapor.

---

## 2. Fitur Utama Portal Guru & Ustaz

### A. Fitur KBM & Akademik Harian
1. **Presensi KBM & Jurnal Mengajar**:
   - Guru membuka jam pelajaran -> Scan QR / Tap Kartu Pelajar santri atau checklist presensi.
   - Mengisi materi jurnal harian yang diajarkan pada jam KBM tersebut.
2. **Penginputan Nilai (Grading Center)**:
   - Input Nilai Tugas, UTS, UAS, dan Nilai Sikap santri.
   - Auto-calculate nilai akhir rapor.

### B. Fitur Tahfidh & Al-Qur'an (Untuk Musyrif)
1. **Setoran Hafalan Harian (Ziyadah & Muroja'ah)**:
   - Pilih nama santri di halaqah -> Input Surah, Ayat mulai s/d selesai, dan nilai tajwid/kelancaran (Mumtaz, Jayyid, dll).
2. **Mutaba'ah & Target Hafalan**:
   - Melihat grafik progres ketercapaian target hafalan per santri.

### C. Fitur Wali Kelas & Pengasuhan
1. **Persetujuan Perijinan Santri (Approval)**:
   - Meninjau pengajuan izin keluar/pulang dari wali santri -> Klik *Approve* atau *Reject*.
2. **Pencatatan Poin Kedisiplinan & Pelanggaran**:
   - Menginput catatan pelanggaran aturan atau apresiasi prestasi santri.

### D. Fitur Mandiri Kepegawaian (HRD Self-Service)
1. **Presensi Kehadiran Guru**: Scan masuk & pulang kerja pegawai.
2. **Pengajuan Cuti / Izin Mengajar**: Pengajuan izin tidak mengajar beserta ustaz pengganti (*inval*).
3. **Slip Gaji Digital**: Mengunduh slip gaji bulanan & rekap jam mengajar.

---

## 3. Alur Utama (Main Flow)

1. **Login Portal Guru**: Menggunakan NIP/NIY dan Password.
2. **Dashboard Harian Guru**:
   - Menampilkan Jadwal Mengajar Hari Ini.
   - Menampilkan Daftar Setoran Halaqah Hari Ini.
   - Menampilkan Notifikasi Pengajuan Izin Santri yang Membutuhkan Persetujuan.
3. **Eksekusi Tugas Harian**:
   - Melakukan Presensi KBM & Input Setoran Al-Qur'an secara mudah dari HP/Tablet.

---

## 4. Logika Utama (Pseudo-code)

```php
// Ambil Dashboard Harian Guru
public function getDashboardGuru($karyawanId) {
    $hariIni = date('N'); // 1 = Senin, dst
    
    // 1. Jadwal Mengajar Hari Ini
    $jadwalHariIni = $this->jadwalModel->where([
        'guru_id' => $karyawanId,
        'hari_index' => $hariIni
    ])->findAll();

    // 2. Pengajuan Izin Santri yang Butuh Approval (jika Guru = Wali Kelas)
    $pendingIjin = $this->perijinanModel->getPendingForWaliKelas($karyawanId);

    // 3. Kelompok Halaqah Tahfidh
    $halaqah = $this->halaqahModel->where('musyrif_id', $karyawanId)->first();

    return [
        'jadwal' => $jadwalHariIni,
        'pending_ijin' => $pendingIjin,
        'halaqah' => $halaqah
    ];
}
```
