# Workflow Modul Kepegawaian (HRD, Multi-Penugasan & Payroll SDM)

Dokumen ini menjelaskan alur kerja komprehensif pengelolaan data Ustaz/Guru/Staf, penugasan ganda (Multi-Role), presensi Geolocation/RFID, sistem Cuti & Inval (Guru Pengganti), kalkulasi Payroll terstruktur, hingga pencetakan Slip Gaji Digital di dalam sistem SuperApp.

---

## 1. Alur Utama (Main Flow)

1. **Pendataan Pegawai & Dokumen SDM**: Penginputan biodata baru guru/ustaz/staf, NIP/NIY, pendidikan terakhir, tanggal bergabung, serta dokumen SK Penugasan.
2. **Pengaturan Multi-Jabatan & Penugasan**:
   - Guru di pesantren dapat memegang **multi-penugasan** (contoh: *Guru Pengampu Mapel* + *Wali Kelas* + *Pembina Asrama* + *Kepala Musyrif*).
3. **Presensi Harian & Jam Mengajar**:
   - Presensi Harian Masuk/Pulang (via Scan RFID Tap / Fingerprint / Geolocation Selfie HP).
   - Presensi Jam Tatap Muka KBM (JTM) terintegrasi dengan Modul Akademik.
4. **Manajemen Cuti & Guru Pengganti (Inval)**:
   - Pengajuan cuti/izin tidak mengajar melalui Portal Guru.
   - Penunjukan **Guru Pengganti (Inval)** untuk mengisi kelas kosong beserta pencatatan insentif inval.
5. **Kalkulasi Payroll & Penggajian Terstruktur**:
   - **Komponen Penerimaan:** Gaji Pokok + Tunjangan Jabatan + Honor Jam Mengajar (JTM) + Insentif Inval + Tunjangan Kehadiran.
   - **Komponen Potongan:** Potongan Keterlambatan + Potongan Kasbon/Pinjaman + BPJS + Infaq.
6. **Slip Gaji Digital & Auto-Journaling**: Penerbitan Slip Gaji PDF via WhatsApp/Portal Guru dan posting otomatis ke Modul Keuangan General.

---

## 2. Detail Tahapan

### Tahap 1: Pengolahan Payroll Terstruktur (End of Month Processing)
* **Aktor:** HRD & Admin Payroll Keuangan.
* **Proses:** 
  1. Sistem melakukan agregasi data absensi bulan berjalan (Total Hadir, Terlambat, Izin, Alpa).
  2. Sistem menghitung Total Jam Mengajar (JTM) yang terealisasi dari Modul Akademik.
  3. Sistem menghitung total honor inval (menggantikan guru lain).
  4. Sistem mengkalkulasi potongan kasbon/pinjaman aktif pegawai.
  5. Menerbitkan Draf Slip Gaji -> Persetujuan Pimpinan -> Transfer / Bayar Kasir.
* **Output:** Slip Gaji Digital & Jurnal Pengeluaran Kas Gaji.

### Tahap 2: Manajemen Guru Pengganti (Inval System)
* **Aktor:** Guru Izin & Guru Pengganti (Inval).
* **Proses:**
  1. Guru A mengajukan izin tidak mengajar di Portal Guru untuk tanggal tertentu.
  2. Kurikulum/Sistem menunjuk Guru B sebagai **Guru Inval** pada jam pelajaran tersebut.
  3. Saat jam KBM berlangsung, Guru B melakukan presensi kelas.
  4. Sistem menambahkan **Insentif Inval** ke dalam slip gaji Guru B bulan berjalan.
* **Output:** Kelas tidak kosong & Insentif Inval Otomatis.

---

## 3. Rancangan Tabel Database

### A. Tabel `karyawan`
* `id` (PK)
* `nip` (varchar Unique - Nomor Induk Pegawai)
* `nama_lengkap` (varchar)
* `no_hp_wa` (varchar)
* `status_kepegawaian` (Enum: `Tetap`, `Kontrak`, `Honor`, `Pengabdi`)
* `gaji_pokok` (decimal)
* `tgl_masuk` (date)
* `status_aktif` (boolean)

### B. Tabel `karyawan_penugasan` (Handling Multi-Jabatan)
* `id` (PK)
* `karyawan_id` (FK `karyawan.id`)
* `jenis_jabatan` (Enum: `Guru_Pengampu`, `Wali_Kelas`, `Pembina_Asrama`, `Musyrif`, `Staf_Admin`, `Pimpinan`)
* `nominal_tunjangan` (decimal)

### C. Tabel `karyawan_presensi`
* `id` (PK)
* `karyawan_id` (FK `karyawan.id`)
* `tanggal` (date)
* `jam_masuk` (time)
* `jam_keluar` (time)
* `lat_masuk` (varchar nullable - GPS Geolocation)
* `long_masuk` (varchar nullable)
* `status` (Enum: `Hadir`, `Terlambat`, `Izin_Sah`, `Sakit`, `Alpa`)
* `menit_terlambat` (int)

### D. Tabel `karyawan_inval` (Guru Pengganti)
* `id` (PK)
* `jadwal_id` (FK `akademik_jadwal.id`)
* `guru_asal_id` (FK `karyawan.id`)
* `guru_inval_id` (FK `karyawan.id`)
* `tanggal` (date)
* `tarif_inval` (decimal)
* `status_presensi` (Enum: `Hadir`, `Batal`)

### E. Tabel `payroll_gaji`
* `id` (PK)
* `no_slip` (varchar Unique)
* `karyawan_id` (FK `karyawan.id`)
* `bulan` (int)
* `tahun` (int)
* `gaji_pokok` (decimal)
* `tunjangan_jabatan` (decimal)
* `total_honor_jtm` (decimal - Jam Mengajar)
* `total_insentif_inval` (decimal)
* `total_tunjangan_hadir` (decimal)
* `total_potongan` (decimal - Terlambat/Kasbon/BPJS)
* `gaji_bersih` (decimal)
* `status_bayar` (Enum: `Pending`, `Lunas`)
* `tgl_transfer` (datetime nullable)

---

## 4. Logika Utama (Pseudo-code)

### A. Kalkulasi Gaji Bersih Bulanan (Payroll Engine)
```php
public function hitungPayrollGaji($karyawanId, $bulan, $tahun) {
    $karyawan = $this->karyawanModel->find($karyawanId);

    // 1. Gaji Pokok & Tunjangan Multi-Jabatan
    $gajiPokok = $karyawan['gaji_pokok'];
    $tunjanganJabatan = $this->penugasanModel->where('karyawan_id', $karyawanId)->sum('nominal_tunjangan');

    // 2. Honor Jam Mengajar Realistis (JTM) dari Modul Akademik
    $totalJamMengajar = $this->presensiKbmModel->getTotalJamMengajar($karyawanId, $bulan, $tahun);
    $tarifPerJam = 25000;
    $honorJtm = $totalJamMengajar * $tarifPerJam;

    // 3. Insentif Guru Pengganti (Inval)
    $totalInval = $this->invalModel->where([
        'guru_inval_id' => $karyawanId,
        'status_presensi' => 'Hadir'
    ])->where('MONTH(tanggal)', $bulan)->sum('tarif_inval');

    // 4. Tunjangan Kehadiran & Potongan Keterlambatan
    $rekapPresensi = $this->presensiModel->getRekapBulan($karyawanId, $bulan, $tahun);
    $tunjanganHadir = $rekapPresensi['total_hadir'] * 15000; // Rp 15rb/hari
    $potonganTerlambat = $rekapPresensi['total_menit_terlambat'] * 1000; // Rp 1rb/menit

    // 5. Potongan Kasbon Pegawai
    $potonganKasbon = $this->kasbonModel->getAngsuranBulanIni($karyawanId, $bulan, $tahun);

    // 6. Hitung Gaji Bersih (THP)
    $gajiKotor = $gajiPokok + $tunjanganJabatan + $honorJtm + $totalInval + $tunjanganHadir;
    $totalPotongan = $potonganTerlambat + $potonganKasbon;
    $gajiBersih = $gajiKotor - $totalPotongan;

    // 7. Simpan Draf Payroll Gaji
    $this->payrollModel->insert([
        'no_slip' => 'SLIP-' . $tahun . sprintf('%02d', $bulan) . '-' . $karyawanId,
        'karyawan_id' => $karyawanId,
        'bulan' => $bulan,
        'tahun' => $tahun,
        'gaji_pokok' => $gajiPokok,
        'tunjangan_jabatan' => $tunjanganJabatan,
        'total_honor_jtm' => $honorJtm,
        'total_insentif_inval' => $totalInval,
        'total_tunjangan_hadir' => $tunjanganHadir,
        'total_potongan' => $totalPotongan,
        'gaji_bersih' => $gajiBersih,
        'status_bayar' => 'Pending'
    ]);
}
```
