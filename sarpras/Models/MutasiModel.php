<?php

namespace Sarpras\Models;

use CodeIgniter\Model;

class MutasiModel extends Model
{
    protected $table            = 'inv_mutasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'barang_id', 'tipe', 'jumlah', 'stok_sebelum', 'stok_sesudah', 'keterangan', 'petugas_id'
    ];

    protected $useTimestamps = false;

    /**
     * Catat mutasi stok barang dan perbarui stok barang di tabel inv_barang.
     */
    public function catat(int $barangId, int $jumlah, string $tipe, ?string $keterangan = null, ?int $petugasId = null): array
    {
        if ($jumlah <= 0) {
            return ['ok' => false, 'message' => 'Jumlah harus lebih dari 0.'];
        }

        $db = \Config\Database::connect();
        $barangModel = new \Sarpras\Models\BarangModel();

        $barang = $barangModel->find($barangId);
        if (!$barang) {
            return ['ok' => false, 'message' => 'Barang tidak ditemukan.'];
        }

        $sebelum = (int) $barang['stok'];
        if ($tipe === 'keluar' && $sebelum < $jumlah) {
            return ['ok' => false, 'message' => 'Stok tidak mencukupi. Tersedia: ' . $sebelum . ' ' . $barang['satuan']];
        }

        $sesudah = $tipe === 'masuk' ? $sebelum + $jumlah : $sebelum - $jumlah;

        // Update stok barang
        $barangModel->update($barangId, ['stok' => $sesudah]);

        // Insert log mutasi
        $this->insert([
            'barang_id'    => $barangId,
            'tipe'         => $tipe,
            'jumlah'       => $jumlah,
            'stok_sebelum' => $sebelum,
            'stok_sesudah' => $sesudah,
            'keterangan'   => $keterangan,
            'petugas_id'   => $petugasId,
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        return ['ok' => true, 'stok_sesudah' => $sesudah];
    }
}
