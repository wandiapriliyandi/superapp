# Workflow Portal Wali Santri & Santri (Mobile / Web App)

Dokumen ini menjelaskan alur kerja, hak akses, dan fitur-fitur portal mandiri (*Self-Service Portal*) untuk Wali Santri dan Santri di dalam sistem SuperApp.

---

## 1. Peran & Akses Pengguna

* **Wali Santri**: Memiliki akses ke data satu atau beberapa santri yang menjadi anak/ampuannya (Multi-Santri Switcher jika ortu memiliki >1 anak di pesantren).
* **Santri**: Memiliki akses tampilan khusus santri (Materi E-Learning, Jadwal Pelajaran, Nilai, Kartu Digital, & Saldo Tabungan).

---

## 2. Fitur Utama Portal Wali Santri & Santri

### A. Fitur Keuangan & Tabungan
1. **Top-Up Saldo Tabungan**: Isi saldo tabungan jajan via Virtual Account (BSI, Mandiri, BCA, BRI) / QRIS.
2. **Atur Limit Jajan Harian**: Wali santri menentukan batas maksimal belanja santri per hari (contoh: Maksimal Rp 15.000 / hari).
3. **Restriksi Kategori & Barang Belanja (Smart Spending Lock)**:
   - Wali murid memilih & mengunci kategori barang/produk tertentu yang dilarang dibeli oleh anak (misal: *Mie Instan*, *Minuman Soda*, *Rokok*, *Snack Tinggi Gula*).
   - Kasir kantin akan otomatis menolak transaksi jika santri mencoba membeli barang tersebut.
4. **Riwayat Transaksi Jajan**: Pemantauan transaksi kantin/penarikan uang secara realtime lengkap dengan rincian nama item barang yang dibeli.
5. **Pembayaran SPP Online**: Cek tagihan bulanan SPP & bayar langsung secara online dengan kuitansi digital.

### B. Fitur Perijinan & Keasramaan
1. **Pengajuan Izin Online**: Wali santri mengajukan izin pulang/keluar santri beserta alasannya dari rumah.
2. **Tracking Status Izin**: Pemantauan persetujuan izin (Disetujui/Ditolak) & status keberadaan santri (Di Pesantren / Di Luar / Terlambat).
3. **Monitoring Poin Kedisiplinan**: Melihat rekap poin pelanggaran/prestasi & status Surat Peringatan (SP).

### C. Fitur Akademik & Tahfidh
1. **Kartu Pelajar / Santri Digital**: Tampilan Kartu Digital dengan Dynamic QR Code untuk presensi & transaksi.
2. **Capaian Hafalan Al-Qur'an**: Pemantauan setoran Ziyadah/Muroja'ah harian dan Juz yang telah dicapai.
3. **Presensi KBM & Rapor Digital**: Pemantauan kehadiran kelas harian dan pengunduhan Rapor Akademik.

---

## 3. Alur Utama (Main Flow)

1. **Autentikasi & Login**: Wali santri login menggunakan No. HP (OTP WhatsApp) atau No. Rekening/NISN.
2. **Switching Anak (Multi-Santri)**: Jika wali murid memiliki >1 anak, dapat memilih santri yang ingin dipantau.
3. **Pengaturan Restriksi Belanja**:
   - Buka Menu **Tabungan -> Restriksi Belanja**.
   - Centang kategori barang yang ingin dilarang (misal: `Mie Instan`, `Minuman Bersoda`).
   - Simpan -> Sistem langsung menerapkan aturan ke Kasir Kantin secara realtime.
4. **Interaksi Realtime**:
   - Menerima Notifikasi WhatsApp / Push Notification saat santri jajan, izin keluar, atau ada tagihan baru.
   - Mengajukan perizinan atau melakukan pembayaran tagihan SPP.

---

## 4. Rancangan Tabel Database terkait Portal Wali

### A. Tabel `wali_santri`
* `id` (PK)
* `user_id` (FK `users.id`)
* `nama_wali` (varchar)
* `no_hp_wa` (varchar Unique)
* `hubungan` (Enum: Ayah, Ibu, Wali)

### B. Tabel `tabungan_restriksi` (Kunci Kategori Barang)
* `id` (PK)
* `santri_id` (FK `santri.id`)
* `kategori_id` (FK `kantin_kategori.id` nullable)
* `barang_id` (FK `kantin_barang.id` nullable)
* `created_by` (FK `users.id`)

---

## 5. Logika Utama (Pseudo-code)

```php
// Simpan Pengaturan Restriksi Belanja Anak oleh Wali Santri
public function saveRestriksiBelanja($santriId, $selectedKategoriIds, $userId) {
    $this->db->transStart();

    // 1. Hapus restriksi lama santri ini
    $this->restriksiModel->where('santri_id', $santriId)->delete();

    // 2. Simpan daftar kategori yang dikunci wali santri
    foreach ($selectedKategoriIds as $katId) {
        $this->restriksiModel->insert([
            'santri_id' => $santriId,
            'kategori_id' => $katId,
            'created_by' => $userId
        ]);
    }

    $this->db->transComplete();
}
```
