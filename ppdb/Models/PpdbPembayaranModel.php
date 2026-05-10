<?php

namespace Ppdb\Models;

use CodeIgniter\Model;

class PpdbPembayaranModel extends Model
{
    protected $table            = 'ppdb_pembayaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['id_pendaftar', 'nomor_kwitansi', 'jumlah', 'tanggal_bayar', 'metode_bayar', 'keterangan'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateKwitansi()
    {
        $last = $this->orderBy('id', 'DESC')->first();
        $num = $last ? (int) substr($last['nomor_kwitansi'], -4) + 1 : 1;
        return 'KW-PPDB-' . date('Ymd') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
