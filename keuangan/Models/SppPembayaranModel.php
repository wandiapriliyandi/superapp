<?php

namespace Keuangan\Models;

use CodeIgniter\Model;

class SppPembayaranModel extends Model
{
    protected $table            = 'spp_pembayaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'tagihan_id', 'nomor_transaksi', 'tanggal_bayar', 'nominal_bayar', 
        'metode_pembayaran', 'keterangan', 'recorded_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
}
