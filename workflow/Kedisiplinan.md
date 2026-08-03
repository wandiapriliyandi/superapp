# Workflow Modul Kedisiplinan, Tab Tatib (Tata Tertib), & Ta'zir Santri

Dokumen ini menjelaskan alur kerja pengelolaan **Tab Tata Tertib (Tatib & Buku Saku Digital)**, pencatatan poin pelanggaran, poin apresiasi prestasi, penanganan sanksi (*Ta'zir*), pemanggilan orang tua, serta pemicu dinamis Surat Peringatan (SP 1, SP 2, SP 3) di dalam sistem SuperApp.

---

## 1. Konsep Tab Tatib (Tata Tertib & Buku Saku Digital)

> [!IMPORTANT]
> **Aksesibilitas Tab Tatib:**
> **Tab Tatib** tersedia di Portal Admin Pengasuhan, Portal Guru, dan Portal Wali Santri/Santri sebagai acuan transparansi aturan:
>
> 1. **Sub-Tab Buku Saku Tatib (PDF / Digital Rulebook):** Menampilkan dokumen resmi pasal-pasal aturan pesantren & perundangan santri.
> 2. **Sub-Tab Katalog Poin Pelanggaran:** Daftar lengkap jenis pelanggaran (Ringan, Sedang, Berat) beserta bobot poinnya.
> 3. **Sub-Tab Katalog Poin Prestasi:** Daftar bentuk apresiasi kebaikan/prestasi yang dapat mengurangi poin pelanggaran.
> 4. **Sub-Tab Ambang Batas SP (Khusus Admin):** Pengaturan fleksibel angka batas poin pemicu SP1, SP2, SP3, dan DO.

---

## 2. Alur Utama (Main Flow)

1. **Pengaturan Master Tata Tertib (Tab Tatib)**:
   - Tim Pengasuhan/Kesiswaan mengunggah Buku Saku Tatib Digital dan menyusun Katalog Pelanggaran & Prestasi.
   - **Pengaturan Dinamis Batas SP:** Admin dapat mengatur batas nilai akumulasi poin pemicu SP (contoh: `SP1 = 20 Poin`, `SP2 = 40 Poin`, `SP3 = 60 Poin`, `DO = 80 Poin`).
2. **Pencatatan Pelanggaran / Prestasi Harian**:
   - Guru / Ustaz / Tim Keamanan menginput temuan pelanggaran santri via Portal Guru/Mobile dengan melampirkan foto bukti & lokasi kejadian.
   - Integrasi Otomatis: Mangkir Piket, Mangkir KBM, Mangkir Presensi Malam, atau Terlambat Kembali Izin secara otomatis menambah poin pelanggaran.
3. **Akumulasi Poin Realtime & Notifikasi WA**:
   - Sistem mengkalkulasi saldo total poin pelanggaran santri.
   - Setiap kali terjadi penambahan poin, sistem langsung mengirim Notifikasi WhatsApp ke Wali Santri.
4. **Pemicu Dinamis Surat Peringatan (Dynamic SP Trigger Engine)**:
   - Sistem membaca pengaturan ambang batas SP dari database:
     * **Poin >= Batas SP 1:** Terbit **SP 1** (Peringatan Tertulis ke Wali).
     * **Poin >= Batas SP 2:** Terbit **SP 2** & Surat Pemanggilan Wali Santri ke Pesantren.
     * **Poin >= Batas SP 3:** Terbit **SP 3** & Skorsing / Pembinaan Khusus.
     * **Poin >= Batas DO:** Sidang Tim Pengasuhan / Pengembalian Santri ke Orang Tua.
5. **Eksekusi Sanksi Edukatif (Ta'zir) & Pemutihan Poin**:
   - Santri yang menjalani sanksi edukatif (*Ta'zir*, misal: menghafal surah/piket ekstra) dan diverifikasi oleh Ustaz akan mendapatkan pemulihan/pemutihan poin.

---

## 3. Detail Tahapan

### Tahap 1: Pengelolaan & Transparansi Tab Tatib
* **Aktor:** Tim Pengasuhan (Admin) & Wali Santri/Santri (Pengguna).
* **Proses:** 
  1. Admin mengunggah/memperbarui isi pasal-pasal Tata Tertib di **Tab Tatib**.
  2. Wali Santri & Santri dapat membuka **Tab Tatib** dari HP kapan saja untuk mempelajari pasal-pasal dan bobot poin pelanggaran.
* **Output:** Buku Saku Digital Tatib & Katalog Poin Transparan.

---

## 4. Rancangan Tabel Database

### A. Tabel `disiplin_tatib_buku_saku` (Master Buku Saku Digital)
* `id` (PK)
* `judul_bab` (varchar - Contoh: Bab III Hak & Kewajiban Santri)
* `isi_pasal` (text)
* `file_pdf` (varchar nullable - URL File PDF Resmi)
* `urutan` (int)

### B. Tabel `disiplin_setting_sp` (Master Setting Ambang Batas SP)
* `id` (PK)
* `tingkat_sp` (Enum: `SP1`, `SP2`, `SP3`, `DO` Unique)
* `batas_poin_minimal` (int)
* `deskripsi_tindakan` (text)
* `updated_by` (FK `users.id`)

### C. Tabel `disiplin_katalog_pelanggaran`
* `id` (PK)
* `kode_pelanggaran` (varchar Unique - Contoh: `PLG-001`)
* `nama_pelanggaran` (varchar - Contoh: Merokok, Membawa HP)
* `kategori` (Enum: `Ringan`, `Sedang`, `Berat`, `Sangat_Berat`)
* `poin` (int)
* `sanksi_default` (text)

### D. Tabel `disiplin_katalog_prestasi` (Pengurang Poin)
* `id` (PK)
* `nama_prestasi` (varchar - Contoh: Juara Lomba, Khatam 30 Juz)
* `poin_apresiasi` (int - Mengurangi Poin Pelanggaran)

### E. Tabel `disiplin_santri_pelanggaran`
* `id` (PK)
* `santri_id` (FK `santri.id`)
* `pelanggaran_id` (FK `disiplin_katalog_pelanggaran.id`)
* `tanggal` (date)
* `jam` (time)
* `lokasi` (varchar)
* `foto_bukti` (varchar nullable)
* `catatan` (text)
* `pelapor_id` (FK `karyawan.id` - Guru/Ustaz yang Melaporkan)

### F. Tabel `disiplin_sp_history` (Surat Peringatan)
* `id` (PK)
* `no_surat` (varchar Unique)
* `santri_id` (FK `santri.id`)
* `tingkat_sp` (Enum: `SP1`, `SP2`, `SP3`, `DO`)
* `total_poin_saat_sp` (int)
* `tgl_terbit` (date)
* `tgl_pemanggilan_wali` (date nullable)
* `status` (Enum: `Aktif`, `Selesai_Pembinaan`)

---

## 5. Logika Utama (Pseudo-code)

```php
// Ambil Isi Tab Tatib Transparan untuk Wali Santri / Santri
public function getTabTatibTransparan() {
    $bukuSaku = $this->tatibModel->orderBy('urutan', 'ASC')->findAll();
    $pelanggaran = $this->katalogPelanggaranModel->orderBy('kategori', 'ASC')->findAll();
    $prestasi = $this->katalogPrestasiModel->findAll();
    $settingSp = $this->settingSpModel->orderBy('batas_poin_minimal', 'ASC')->findAll();

    return [
        'buku_saku' => $bukuSaku,
        'katalog_pelanggaran' => $pelanggaran,
        'katalog_prestasi' => $prestasi,
        'ambang_batas_sp' => $settingSp
    ];
}
```
