# Workflow Modul Banner Pengumuman & Pop-up Modal Login

Dokumen ini menjelaskan alur kerja pengelolaan banner promosi/pengumuman dalam bentuk pop-up modal yang tampil otomatis saat pengguna baru saja melakukan login ke dalam aplikasi SuperApp.

---

## 1. Alur Utama (Main Flow)

1. **Pengaturan Master Banner Popup**: Admin mengunggah gambar banner, memasukkan judul, teks pengumuman, serta link tombol aksi (*Call-to-Action / CTA*).
2. **Filter Target Audiens (Targeting)**: Admin memilih target penerima banner:
   - `Wali_Santri`
   - `Guru_Ustaz`
   - `Santri`
   - `Semua_User`
3. **Pengaturan Frekuensi Tampil (Display Frequency)**:
   - `Setiap_Login`: Banner selalu muncul setiap kali pengguna baru login.
   - `Sekali_Sehari`: Banner hanya muncul 1x dalam sehari untuk pengguna tersebut.
   - `Sekali_Permanen`: Banner muncul sekali sampai pengguna mengklik *"Jangan Tampilkan Lagi"* / close.
4. **Eksekusi Pop-up Modal saat Login**:
   - Pengguna berhasil login -> Sistem melakukan query banner aktif yang sesuai dengan role pengguna dan frekuensi tampil.
   - Jika ditemukan, modal pop-up banner muncul secara otomatis di atas layar dashboard.
5. **Analitik Impression & Klik**: Sistem mencatat statistik berapa kali banner dilihat (*impression*) dan berapa kali tombol link CTA diklik pengguna.

---

## 2. Detail Tahapan

### Tahap 1: Pengecekan & Display Banner Login
* **Aktor:** Sistem & Pengguna (Saat Login Selesai).
* **Proses:** 
  1. Pengguna membuka dashboard setelah sukses autentikasi.
  2. Frontend memanggil API `GET /api/banner/active-popup`.
  3. API memeriksa:
     - Range tanggal aktif (`tgl_mulai <= NOW() <= tgl_selesai`).
     - Matching target role pengguna.
     - Mengecek cookie / localStorage / database log apakah banner sudah pernah ditampilkan sesuai syarat frekuensi.
  4. Jika lolos, modal pop-up banner ditampilkan di layar dengan animasi smooth.
* **Output:** Modal Pop-up Banner Pengumuman di Dashboard.

---

## 3. Rancangan Tabel Database

### A. Tabel `banner_modal`
* `id` (PK)
* `judul` (varchar)
* `gambar_banner` (varchar - URL Image Flyer)
* `deskripsi` (text nullable)
* `link_url` (varchar nullable - URL Tombol CTA)
* `teks_tombol` (varchar nullable - Contoh: "Bayar SPP Sekarang", "Lihat Pengumuman")
* `target_role` (Enum: `Semua_User`, `Wali_Santri`, `Guru_Ustaz`, `Santri`)
* `frekuensi` (Enum: `Setiap_Login`, `Sekali_Sehari`, `Sekali_Permanen`)
* `prioritas` (int - Urutan Tampil)
* `tgl_mulai` (datetime)
* `tgl_selesai` (datetime)
* `is_active` (boolean)

### B. Tabel `banner_modal_log` (Log Impresion & Klik)
* `id` (PK)
* `banner_id` (FK `banner_modal.id`)
* `user_id` (FK `users.id`)
* `tgl_dilihat` (datetime)
* `is_clicked` (boolean)

---

## 4. Logika Utama (Pseudo-code)

```php
// Ambil Banner Modal Aktif yang Berhak Dilihat User
public function getActiveBannerPopup($userId, $userRole) {
    $now = date('Y-m-d H:i:s');
    $today = date('Y-m-d');

    // 1. Query Banner Aktif Sesuai Role & Tanggal
    $bannerList = $this->bannerModel
        ->where('is_active', 1)
        ->where('tgl_mulai <=', $now)
        ->where('tgl_selesai >=', $now)
        ->groupStart()
            ->where('target_role', 'Semua_User')
            ->orWhere('target_role', $userRole)
        ->groupEnd()
        ->orderBy('prioritas', 'ASC')
        ->findAll();

    $validBanners = [];

    // 2. Filter Berdasarkan Frekuensi Tampil User
    foreach ($bannerList as $b) {
        if ($b['frekuensi'] === 'Sekali_Permanen') {
            $hasSeen = $this->logModel->where(['banner_id' => $b['id'], 'user_id' => $userId])->countAllResults();
            if ($hasSeen > 0) continue; // Skip jika sudah pernah lihat
        } else if ($b['frekuensi'] === 'Sekali_Sehari') {
            $hasSeenToday = $this->logModel->where(['banner_id' => $b['id'], 'user_id' => $userId])
                                          ->where('DATE(tgl_dilihat)', $today)
                                          ->countAllResults();
            if ($hasSeenToday > 0) continue; // Skip jika sudah lihat hari ini
        }

        $validBanners[] = $b;
    }

    return $validBanners;
}
```
