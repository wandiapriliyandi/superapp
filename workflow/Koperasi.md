# Workflow Modul Kantin & Koperasi Pesantren (POS Cashless)

Dokumen ini menjelaskan alur kerja Point of Sale (POS), manajemen inventaris produk, pembayaran tap kartu pelajar, integrasi restriksi jajan, dan pembukuan keuangan untuk Modul Kantin & Koperasi Pesantren di dalam sistem SuperApp.

---

## 1. Alur Utama (Main Flow)

1. **Pengaturan Master Produk & Stok**: Admin Kantin mendata kategori barang, harga beli (HPP), harga jual, barcode produk, serta jumlah stok awal.
2. **Buka Shift Kasir**: Kasir membuka kasir harian dan memasukkan nominal uang modal awal.
3. **Proses Penjualan (Checkout POS)**:
   - Kasir meng-input/scan barcode produk yang dibeli santri.
   - Santri men-tap Kartu Pelajar (RFID / QR Code / Barcode).
   - Sistem memvalidasi: **Status Kartu**, **Kecukupan Saldo**, **Batas Limit Harian**, dan **Restriksi Barang Wali Santri**.
   - Saldo tabungan santri terpotong otomatis dan struk transaksi diterbitkan.
4. **Tutup Shift & Rekonsiliasi Kasir**: Di akhir jam kerja, kasir menghitung uang fisik dan mencocokkannya dengan rekap transaksi sistem (Cash vs Cashless).
5. **Stok Opname & Pengadaan (PO)**: Penyesuaian stok barang fisik dan pencatatan barang masuk dari supplier.
6. **Jurnal Keuangan Otomatis**: Omset penjualan dan HPP terposting otomatis ke Modul Keuangan General SuperApp.

---

## 2. Detail Tahapan

### Tahap 1: Transaksi Penjualan Tap Kartu (Cashless Checkout)
* **Aktor:** Kasir Kantin & Santri.
* **Proses:** 
  1. Kasir memindai barcode produk / memilih produk di layar Touchscreen POS.
  2. Santri men-tap Kartu Pelajar pada RFID/QR Scanner.
  3. Sistem memeriksa apakah ada produk belanjaan yang masuk dalam daftar `tabungan_restriksi` santri tersebut.
  4. Jika ada produk dilarang: Muncul peringatan merah di layar kasir & transaksi item tersebut dibatalkan.
  5. Jika lolos: Saldo tabungan santri berkurang, stok barang berkurang, dan notifikasi terkirim ke WhatsApp Wali Santri.
* **Output:** Struk Belanja & Log Transaksi Cashless.

### Tahap 2: Buka & Tutup Shift Kasir
* **Aktor:** Kasir Kantin.
* **Proses:**
  1. Input Uang Modal Awal saat Buka Shift.
  2. Saat Tutup Shift: Sistem menghitung Total Penjualan Tunai + Total Penjualan Cashless (Tap Kartu).
  3. Kasir menyerahkan uang tunai fisik ke Bendahara Keuangan.
* **Output:** Laporan Kasir per Shift.

---

## 3. Rancangan Tabel Database

### A. Tabel `kantin_kategori`
* `id` (PK)
* `nama_kategori` (varchar)
* `is_restricted_capable` (boolean)

### B. Tabel `kantin_barang`
* `id` (PK)
* `kategori_id` (FK `kantin_kategori.id`)
* `barcode` (varchar Unique)
* `nama_barang` (varchar)
* `harga_beli` (decimal - HPP)
* `harga_jual` (decimal)
* `stok` (int)
* `stok_minimal` (int)
* `status` (Enum: `Aktif`, `Nonaktif`)

### C. Tabel `kantin_penjualan`
* `id` (PK)
* `no_faktur` (varchar Unique)
* `tgl_penjualan` (datetime)
* `santri_id` (FK `santri.id` nullable - null jika pembeli umum/cash)
* `metode_pembayaran` (Enum: `Tabungan_Santri`, `Tunai`, `QRIS`)
* `total_hpp` (decimal)
* `total_bayar` (decimal)
* `kasir_id` (FK `users.id`)

### D. Tabel `kantin_penjualan_detail`
* `id` (PK)
* `penjualan_id` (FK `kantin_penjualan.id`)
* `barang_id` (FK `kantin_barang.id`)
* `harga_jual` (decimal)
* `harga_beli` (decimal)
* `qty` (int)
* `subtotal` (decimal)

---

## 4. Logika Utama (Pseudo-code)

```php
// Eksekusi Penjualan POS Kantin Tap Kartu
public function checkoutPosKantin($uidKartu, $cartItems, $kasirId) {
    $this->db->transStart();

    // 1. Identification Kartu Pelajar Santri
    $identity = $this->tabunganService->identifyKartuPelajar($uidKartu);
    $santriId = $identity['santri_id'];
    $rek = $identity['rekening'];

    // 2. Cek Restriksi Barang Wali Santri
    $blocked = $this->tabunganService->checkRestriksiBarang($santriId, $cartItems);
    if (!empty($blocked)) {
        throw new \Exception("Transaksi Ditolak! Barang '" . implode(', ', $blocked) . "' dilarang oleh Wali Santri.");
    }

    // 3. Hitung Total Belanja & HPP
    $totalBayar = 0;
    $totalHpp = 0;
    foreach ($cartItems as $item) {
        $totalBayar += ($item['harga_jual'] * $item['qty']);
        $totalHpp += ($item['harga_beli'] * $item['qty']);
    }

    // 4. Potong Saldo Tabungan (Cek Limit Harian & Saldo Cukup)
    $this->tabunganService->potongSaldoTabungan($rek['id'], $totalBayar, $uidKartu, $kasirId);

    // 5. Simpan Transaksi Penjualan POS & Detail
    $fakturId = $this->penjualanModel->insert([
        'no_faktur' => $this->generateFakturNo(),
        'tgl_penjualan' => date('Y-m-d H:i:s'),
        'santri_id' => $santriId,
        'metode_pembayaran' => 'Tabungan_Santri',
        'total_hpp' => $totalHpp,
        'total_bayar' => $totalBayar,
        'kasir_id' => $kasirId
    ]);

    foreach ($cartItems as $item) {
        $this->penjualanDetailModel->insert([
            'penjualan_id' => $fakturId,
            'barang_id' => $item['barang_id'],
            'harga_jual' => $item['harga_jual'],
            'harga_beli' => $item['harga_beli'],
            'qty' => $item['qty'],
            'subtotal' => $item['harga_jual'] * $item['qty']
        ]);

        // Decement Stok Barang
        $this->barangModel->decrement('stok', $item['qty'], ['id' => $item['barang_id']]);
    }

    // 6. Posting Jurnal Otomatis ke Modul Keuangan (Kas Kantin & Pendapatan)
    $this->keuanganService->postJurnalPenjualanKantin($totalBayar, $totalHpp);

    $this->db->transComplete();
}
```
