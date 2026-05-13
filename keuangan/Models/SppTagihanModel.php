<?php

namespace Keuangan\Models;

use CodeIgniter\Model;

class SppTagihanModel extends Model
{
    protected $table            = 'spp_tagihan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'santri_id', 'tarif_id', 'bulan', 'tahun', 
        'nominal_tagihan', 'total_terbayar', 'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getTagihanWithSantri()
    {
        return $this->select('spp_tagihan.*, santri.nama_lengkap as nama_santri, spp_tarif.nama_tarif')
                    ->join('santri', 'santri.id = spp_tagihan.santri_id')
                    ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id')
                    ->findAll();
    }
}
