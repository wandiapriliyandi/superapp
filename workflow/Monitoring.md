# Workflow Modul Monitoring & Analytics

Dokumen ini menjelaskan alur kerja pemantauan sistem, dashboard executive, audit trail/log aktivitas pengguna di dalam sistem SuperApp.

## 1. Alur Utama (Main Flow)

1. **Agregasi Data Realtime**: Sistem mengumpulkan statistik aktivitas harian dari seluruh modul (PPDB, SPP, Perijinan, Akademik, dll.).
2. **Executive Dashboard**: Visualisasi ringkas statistik santri, rekapitulasi pembayaran SPP harian, persentase kehadiran KBM, dan status santri di luar pesantren.
3. **Audit Trail (Activity Log)**: Pencatatan otomatis setiap tindakan pengguna (Create, Update, Delete, Login, Logout) beserta IP Address dan User-Agent.
4. **Sistem Pengingat (Alert & Notification System)**: Pemicu notifikasi otomatis untuk tagihan jatuh tempo, batas waktu perizinan kembali, atau stok barang habis.
5. **Eksportasi Laporan Management**: Penarikan laporan gabungan dalam format Excel/PDF untuk Pimpinan Pesantren / Yayasan.

---

## 2. Detail Tahapan

### Tahap 1: Monitoring Dashboard Pimpinan
* **Aktor:** Pengasuh / Pimpinan Pesantren / Kepala Sekolah.
* **Proses:** Membuka Dashboard Utama -> Melihat widget ringkasan statistik (Santri Aktif, Total Kas, Santri Izin Pulang, Calon Santri PPDB).
* **Output:** Informasi Eksekutif Realtime.

---

## 3. Rancangan Tabel Database

### A. Tabel `activity_logs`
* `id` (PK)
* `user_id` (FK)
* `modul` (varchar)
* `aksi` (varchar)
* `deskripsi` (text)
* `ip_address` (varchar)
* `user_agent` (varchar)
* `created_at` (datetime)

---

## 4. Logika Utama (Pseudo-code)

```php
// Record Log Aktivitas Pengguna
public function logAktivitas($modul, $aksi, $deskripsi) {
    $request = \Config\Services::request();
    
    $this->activityLogModel->insert([
        'user_id' => session()->get('user_id'),
        'modul' => $modul,
        'aksi' => $aksi,
        'deskripsi' => $deskripsi,
        'ip_address' => $request->getIPAddress(),
        'user_agent' => (string) $request->getUserAgent(),
        'created_at' => date('Y-m-d H:i:s')
    ]);
}
```
