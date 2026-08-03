# Workflow Modul Tabungan & Kartu Pelajar Digital Multiguna

Dokumen ini menjelaskan alur kerja pengelolaan tabungan/simpanan santri, setoran, penarikan, pembatasan jajan harian, **restriksi kategori barang belanja (Smart Spending Restriction)**, serta peranan **Kartu Pelajar / Santri Digital Multiguna (Single Card Identity)** berbasis RFID, QR Code, dan Barcode di dalam sistem SuperApp.

---

## 1. Konsep Kartu Pelajar / Santri Digital Multiguna (Single Card System)

> [!IMPORTANT]
> **Satu Kartu Pelajar untuk Seluruh Layanan SuperApp:**
> Kartu fisik / digital yang dipegang oleh santri berfungsi ganda sebagai **Kartu Pelajar Resmi**, **Kartu Identitas**, dan **Kartu Transaksi Digital (Smart Card)** di seluruh modul:
>
> 1. **Modul Tabungan & E-Money:** Penarikan uang di kasir & belanja cashless di Kantin/Koperasi Pesantren.
> 2. **Modul Akademik & KBM:** Scan tap presensi kehadiran kelas & jam pelajaran sekolah.
> 3. **Modul Perijinan & Keasramaan:** Scan tap di Pos Satpam gerbang saat izin keluar/kembali santri.
> 4. **Modul Perpustakaan:** Scan peminjaman & pengembalian buku pustaka.
> 5. **Modul Poskestren (Kesehatan):** Verifikasi identitas rekam medis saat berobat.
> 6. **Modul CBT / E-Learning:** Autentikasi / scan login peserta ujian sekolah.

---

## 2. Arsitektur Identifikasi Kartu & Restriksi Belanja Smart

* **Tipe Perangkat Pembaca:** 
  - **RFID / NFC Reader:** Tap kartu ke reader pada mesin kasir/gerbang/presensi (`RFID-XXXXXX`).
  - **QR Code Scanner / Kamera HP:** Pemindaian QR Code di kartu fisik atau dari layar HP Android/iOS Wali Santri (`QR-XXXXXX`).
  - **Barcode Scanner (1D Laser):** Pemindaian kode batang pada fisik Kartu Pelajar (`BC-XXXXXX`).
* **Fitur Restriksi Belanja Santri (Smart Item Restriction):**
  - Wali Santri atau Pengasuh dapat memblokir/mengunci kategori barang tertentu (misal: *Mie Instan*, *Minuman Bersoda*, atau *Rokok*) via Portal Wali.
  - Saat kasir kantin men-scan Kartu Pelajar santri, sistem secara otomatis memvalidasi apakah item belanjaan mengandung produk yang diblokir oleh orang tuanya.

---

## 3. Alur Utama (Main Flow)

1. **Penerbitan Kartu Pelajar**: Admin meregistrasikan `uid_kartu`, mencetak fisik Kartu Pelajar, dan menghubungkannya dengan `santri_id`.
2. **Pembukaan Rekening Tabungan**: Rekening tabungan terhubung otomatis dengan `santri_id`.
3. **Pengaturan Restriksi Belanja**: Wali santri mengatur limit jajan harian dan memilih kategori barang yang diblokir di Portal Wali.
4. **Transaksi Belanja Kantin via Tap Kartu**:
   - Kasir meng-input barang belanjaan santri.
   - Santri men-tap Kartu Pelajar.
   - Sistem memvalidasi 4 hal: **Kartu Aktif**, **Saldo Cukup**, **Limit Harian Cukup**, dan **Bebas dari Restriksi Barang**.
   - Jika ada barang terlarang, transaksi item tersebut ditolak sistem.
5. **Penggantian Kartu Pelajar Hilang (Card Replacement)**:
   - Kartu lama di-blokir (status `Hilang`).
   - Kartu baru dipairing ke `santri_id` yang sama. Seluruh saldo tabungan, aturan restriksi, & riwayat akademik tetap utuh.

---

## 4. Rancangan Tabel Database

### A. Tabel `santri_kartu` (Master Kartu Pelajar Digital)
* `id` (PK)
* `santri_id` (FK `santri.id`)
* `uid_kartu` (varchar Unique - String RFID / QR Code / Barcode)
* `tipe_media` (Enum: `RFID`, `QRCODE`, `BARCODE`)
* `tgl_aktivasi` (datetime)
* `tgl_nonaktif` (datetime nullable)
* `status` (Enum: `Aktif`, `Hilang`, `Rusak`, `Nonaktif`)

### B. Tabel `tabungan_rekening`
* `id` (PK)
* `no_rekening` (varchar Unique)
* `santri_id` (FK `santri.id`)
* `saldo` (decimal)
* `limit_harian` (decimal)
* `status` (Enum: `Aktif`, `Diblokir`, `Ditutup`)

### C. Tabel `tabungan_restriksi` (Kategori / Barang yang Diblokir Wali)
* `id` (PK)
* `santri_id` (FK `santri.id`)
* `kategori_id` (FK `kantin_kategori.id` nullable - Memblokir 1 Kategori)
* `barang_id` (FK `kantin_barang.id` nullable - Memblokir 1 Produk Spesifik)
* `created_by` (FK `users.id` - Wali Santri / Pengasuh)

### D. Tabel `tabungan_transaksi`
* `id` (PK)
* `rekening_id` (FK `tabungan_rekening.id`)
* `uid_kartu_used` (varchar - Log UID kartu yang di-tap)
* `no_transaksi` (varchar Unique)
* `tgl_transaksi` (datetime)
* `jenis_transaksi` (Enum: `Setoran`, `Penarikan`, `Transfer`, `Pembayaran_Kantin`)
* `nominal` (decimal)
* `saldo_awal` (decimal)
* `saldo_akhir` (decimal)
* `petugas_id` (FK User)
* `keterangan` (text)

---

## 5. Logika Utama (Pseudo-code)

### Validasi Belanja Kantin (Cek Restriksi Barang & Limit Saldo)
```php
public function payKantinWithRestriction($uidKartu, $itemsBelanja, $petugasId) {
    $this->db->transStart();

    // 1. Identification Kartu Pelajar
    $identity = $this->identifyKartuPelajar($uidKartu);
    $santriId = $identity['santri_id'];
    $rek = $identity['rekening'];

    // 2. Ambil Daftar Kategori & Barang yang Diblokir oleh Wali Santri
    $restriksiList = $this->restriksiModel->where('santri_id', $santriId)->findAll();
    $blockedKategoriIds = array_filter(array_column($restriksiList, 'kategori_id'));
    $blockedBarangIds = array_filter(array_column($restriksiList, 'barang_id'));

    // 3. Cek Setiap Barang Belanjaan terhadap Daftar Restriksi
    $totalNominal = 0;
    foreach ($itemsBelanja as $item) {
        if (in_array($item['kategori_id'], $blockedKategoriIds) || in_array($item['barang_id'], $blockedBarangIds)) {
            throw new \Exception("Transaksi Ditolak: Barang '" . $item['nama_barang'] . "' diblokir oleh Wali Santri.");
        }
        $totalNominal += ($item['harga'] * $item['qty']);
    }

    // 4. Validasi Saldo & Limit Harian
    if ($rek['saldo'] < $totalNominal) {
        throw new \Exception("Saldo tabungan tidak mencukupi.");
    }
    $penarikanHariIni = $this->transaksiModel->getPenarikanHariIni($rek['id']);
    if (($penarikanHariIni + $totalNominal) > $rek['limit_harian']) {
        throw new \Exception("Transaksi melebihi limit jajan harian santri.");
    }

    // 5. Potong Saldo & Simpan Transaksi
    $saldoBaru = $rek['saldo'] - $totalNominal;
    $this->rekeningModel->update($rek['id'], ['saldo' => $saldoBaru]);

    $this->transaksiModel->insert([
        'rekening_id' => $rek['id'],
        'uid_kartu_used' => $uidKartu,
        'no_transaksi' => $this->generateNoTrans('TRX-OUT'),
        'tgl_transaksi' => date('Y-m-d H:i:s'),
        'jenis_transaksi' => 'Pembayaran_Kantin',
        'nominal' => $totalNominal,
        'saldo_awal' => $rek['saldo'],
        'saldo_akhir' => $saldoBaru,
        'petugas_id' => $petugasId,
        'keterangan' => 'Belanja Kantin (' . count($itemsBelanja) . ' items)'
    ]);

    $this->db->transComplete();
}
```
