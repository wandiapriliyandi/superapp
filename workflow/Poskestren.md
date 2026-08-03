# Workflow Modul Poskestren (Pos Kesehatan Pesantren)

Dokumen ini menjelaskan alur kerja layanan kesehatan santri, rekam medis harian, dan rujukan di dalam sistem SuperApp.

## 1. Alur Utama (Main Flow)

1. **Pendaftaran Berobat**: Santri sakit datang ke Poskestren dan didata oleh petugas kesehatan.
2. **Pemeriksaan & Anamnesis**: Tenaga medis (Perawat/Dokter) memeriksa gejala dan mengukur tanda vital (TDS, Suhu, BB).
3. **Diagnosa & Rekam Medis**: Pencatatan hasil diagnosa dan riwayat penyakit santri.
4. **Pemberian Obat & Istirahat**: Resep obat dikeluarkan dari stok apotek pesantren dan surat izin istirahat di UKS/kamar.
5. **Rujukan Luar (Opsional)**: Jika kondisi berat, sistem menerbitkan Surat Rujukan ke Klinik/Klinik/Rumah Sakit mitra.
6. **Notifikasi Orang Tua**: Notifikasi otomatis dikirimkan ke wali santri mengenai kondisi kesehatan santri.

---

## 2. Detail Tahapan

### Tahap 1: Berobat & Pemberian Obat
* **Aktor:** Petugas Medis Poskestren.
* **Proses:** Cari NIS santri -> Input keluhan & suhu badan -> Pilih obat yang diberikan -> Pengurangan stok obat.
* **Output:** Rekam Medis & Resep Obat.

---

## 3. Rancangan Tabel Database

### A. Tabel `poskestren_rekam_medis`
* `id` (PK)
* `santri_id` (FK)
* `tgl_periksa` (datetime)
* `keluhan` (text)
* `diagnosa` (varchar)
* `tensi` (varchar)
* `suhu` (decimal)
* `resep_obat` (text)
* `status_rujukan` (boolean)

### B. Tabel `poskestren_obat`
* `id` (PK)
* `nama_obat` (varchar)
* `stok` (int)
* `satuan` (varchar)

---

## 4. Logika Utama (Pseudo-code)

```php
// Catat Berobat & Resep Obat
public function simpanRekamMedis($data) {
    $this->db->transStart();

    $this->rekamMedisModel->insert($data);

    foreach ($data['list_obat'] as $o) {
        $this->obatModel->where('id', $o['obat_id'])->decrement('stok', $o['jumlah']);
    }

    $this->db->transComplete();
}
```
