# Workflow Modul WhatsApp Gateway & Center Notifikasi

Dokumen ini menjelaskan alur kerja mesin notifikasi otomatis (WhatsApp, Email, Push Notification), pengiriman pesan massal (*broadcast*), dan manajemen template pesan di dalam sistem SuperApp.

---

## 1. Alur Utama (Main Flow)

1. **Pengaturan WA Gateway & Provider**: Admin mengkonfigurasikan koneksi API WhatsApp Gateway (Vendor WA Gateway / Fonta / Whacenter / Fonnte / Official WA Business API).
2. **Penyusunan Template Pesan Dynamic**: Pengaturan template pesan otomatis menggunakan variabel dinamis (contoh: `{nama_santri}`, `{nominal}`, `{tanggal}`).
3. **Pengiriman Notifikasi Otomatis (Event-Driven Notification)**:
   - Terjadi transaksi tap kartu di kantin -> Kirim Notif WA ke Wali Santri.
   - Presensi gerbang / perizinan disetujui -> Kirim Notif WA ke Wali Santri.
   - Tagihan SPP terbit -> Kirim Reminder WA otomatis.
4. **Broadcast Pesan Massal (Bulk Messenger)**:
   - Admin/Pengasuh mengirim pengumuman ke grup filter tertentu (per kelas, per angkatan, atau seluruh wali santri).
5. **Monitoring Queue & Delivery Status**: Pemantauan status pengiriman (Sent, Delivered, Read, Failed) dan retry pengiriman otomatis jika gagal.

---

## 2. Detail Tahapan

### Tahap 1: Pengiriman Notifikasi Otomatis (Trigger Realtime)
* **Aktor:** Sistem (Trigger Events dari modul lain).
* **Proses:** 
  1. Modul (misal SPP / Kantin / Perijinan) memanggil `NotificationService->sendWa()`.
  2. Sistem memasukkan pesan ke dalam antrean (`notification_queue`).
  3. Worker background / Cronjob mengirim pesan via WhatsApp Gateway API.
* **Output:** Status Pengiriman Realtime & Log Pesan Terkirim.

---

## 3. Rancangan Tabel Database

### A. Tabel `notification_template`
* `id` (PK)
* `kode_event` (varchar Unique - Contoh: `WA_KANTIN_TRANSACTION`, `WA_SPP_TAGIHAN`)
* `judul` (varchar)
* `template_pesan` (text)

### B. Tabel `notification_queue`
* `id` (PK)
* `no_hp_tujuan` (varchar)
* `pesan` (text)
* `status` (Enum: `Pending`, `Sending`, `Sent`, `Failed`)
* `attempts` (int)
* `last_error` (text nullable)
* `scheduled_at` (datetime)
* `sent_at` (datetime nullable)

---

## 4. Logika Utama (Pseudo-code)

```php
// Kirim WhatsApp Notifikasi Transaksi Kantin
public function triggerNotifKantin($santriId, $nominal, $sisaSaldo, $namaItemStr) {
    $santri = $this->santriModel->find($santriId);
    $wali = $this->waliModel->getWaliPrimary($santriId);

    if (!$wali || empty($wali['no_hp_wa'])) return;

    $template = $this->templateModel->where('kode_event', 'WA_KANTIN_TRANSACTION')->first();
    
    // Replace Dynamic Variables
    $pesan = str_replace(
        ['{nama_santri}', '{nominal}', '{sisa_saldo}', '{items}'],
        [$santri['nama_lengkap'], number_format($nominal), number_format($sisaSaldo), $namaItemStr],
        $template['template_pesan']
    );

    // Push to Queue
    $this->queueModel->insert([
        'no_hp_tujuan' => $wali['no_hp_wa'],
        'pesan' => $pesan,
        'status' => 'Pending',
        'scheduled_at' => date('Y-m-d H:i:s')
    ]);
}
```
