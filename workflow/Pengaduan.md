# Workflow Modul Pengaduan & Layanan Aspirasi (Helpdesk & Ticketing)

Dokumen ini menjelaskan alur kerja penanganan keluhan, aspirasi, layanan tiket pengaduan wali santri & santri, serta sistem whistleblowing di dalam sistem SuperApp.

---

## 1. Alur Utama (Main Flow)

1. **Pengajuan Tiket Pengaduan**: Wali santri atau santri membuat tiket pengaduan baru melalui Portal dengan memilih kategori (Fasilitas/Sarpras, Keasramaan, Akademik, Keuangan, Kantin, atau Pelindungan Santri).
2. **Kerahasiaan & Opsi Anonim (Whistleblowing)**: Pelapor dapat memilih opsi *Anonim* untuk menyembunyikan identitas pelapor jika menyampaikan keluhan sensitif.
3. **Penugasan Tiket (Ticket Assignment)**: Sistem meneruskan tiket pengaduan ke departemen/penanggung jawab terkait secara otomatis berdasarkan kategori keluhan.
4. **Respon & Tindak Lanjut (Handling Progress)**: Petugas/Customer Service memperbarui status penanganan (`Open` -> `In Progress` -> `Resolved`) beserta catatan solusinya.
5. **Rating & Feedback Pelayanan**: Wali santri memberikan ulasan kepuasan (1-5 Bintang) atas penyelesaian keluhan oleh petugas.
6. **Laporan Performance SLA**: Pimpinan melihat statistik jumlah pengaduan, kategori terbanyak, dan durasi rata-rata penyelesaian masalah oleh staf.

---

## 2. Detail Tahapan

### Tahap 1: Pengajuan & Penanganan Tiket Pengaduan
* **Aktor:** Wali Santri & Petugas Customer Service / Admin Departemen.
* **Proses:** 
  1. Wali santri membuka menu **Pengaduan -> Buat Tiket Baru**.
  2. Memilih Kategori, Judul Keluhan, Isi Deskripsi, serta melampirkan foto/video bukti jika ada.
  3. Memilih Opsi Privasi (`Publik` / `Internal` / `Anonim`).
  4. Petugas memberikan balasan & memperbarui status penyelesaian.
* **Output:** Nomor Tiket Pengaduan (contoh: `TKT-202608-0012`) & Notifikasi WA Update Status.

---

## 3. Rancangan Tabel Database

### A. Tabel `pengaduan_kategori`
* `id` (PK)
* `nama_kategori` (varchar - Contoh: Fasilitas, Keuangan, Keasramaan, Kebersihan)
* `departemen_id` (FK `karyawan.id` - Penanggung Jawab Default)

### B. Tabel `pengaduan_tiket`
* `id` (PK)
* `no_tiket` (varchar Unique)
* `kategori_id` (FK `pengaduan_kategori.id`)
* `pelapor_user_id` (FK `users.id`)
* `santri_id` (FK `santri.id` nullable)
* `is_anonim` (boolean)
* `judul` (varchar)
* `deskripsi` (text)
* `foto_lampiran` (varchar nullable)
* `status` (Enum: `Open`, `In_Progress`, `Resolved`, `Closed`)
* `assigned_to` (FK `users.id` - Petugas yang Mengerjakan)
* `rating_kepuasan` (int nullable - 1 s/d 5)
* `feedback_pelapor` (text nullable)

### C. Tabel `pengaduan_respon` (Diskusi / Balasan Tiket)
* `id` (PK)
* `tiket_id` (FK `pengaduan_tiket.id`)
* `sender_user_id` (FK `users.id`)
* `pesan` (text)
* `lampiran` (varchar nullable)
* `created_at` (datetime)

---

## 4. Logika Utama (Pseudo-code)

```php
// Buat Tiket Pengaduan Baru oleh Wali Santri
public function buatTiketPengaduan($data, $userId) {
    $this->db->transStart();

    $noTiket = 'TKT-' . date('Ym') . '-' . sprintf('%04d', rand(1, 9999));
    
    // 1. Ambil Penanggung Jawab Kategori
    $kat = $this->kategoriModel->find($data['kategori_id']);

    // 2. Simpan Tiket Pengaduan
    $this->tiketModel->insert([
        'no_tiket' => $noTiket,
        'kategori_id' => $data['kategori_id'],
        'pelapor_user_id' => $userId,
        'santri_id' => $data['santri_id'] ?? null,
        'is_anonim' => $data['is_anonim'] ?? false,
        'judul' => $data['judul'],
        'deskripsi' => $data['deskripsi'],
        'foto_lampiran' => $data['foto_lampiran'] ?? null,
        'status' => 'Open',
        'assigned_to' => $kat['departemen_id'] ?? null
    ]);

    // 3. Kirim Notifikasi WA ke Petugas yang Ditunjuk
    $this->notifService->sendNotifTiketBaruPetugas($noTiket, $kat['departemen_id']);

    $this->db->transComplete();
}
```
