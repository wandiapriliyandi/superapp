<?php

namespace Spp\Models;

use CodeIgniter\Model;

class SppTagihanModel extends Model
{
    protected $table            = 'spp_tagihan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'nisn', 'tarif_id', 'bulan', 'tahun', 
        'nominal_tagihan', 'diskon', 'keterangan_diskon', 'total_terbayar', 'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getTagihanWithSantri()
    {
        return $this->select('spp_tagihan.*, santri.nama_lengkap as nama_santri, spp_tarif.nama_tarif')
                    ->join('santri', 'santri.nisn = spp_tagihan.nisn')
                    ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id')
                    ->findAll();
    }

    public function getUnpaidBySantri($nisn)
    {
        return $this->select('spp_tagihan.*, spp_tarif.nama_tarif, spp_tarif.tipe')
                    ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id')
                    ->where('spp_tagihan.nisn', $nisn)
                    ->where('spp_tagihan.status !=', 'Lunas')
                    ->orderBy('spp_tagihan.tahun', 'DESC')
                    ->orderBy('spp_tagihan.bulan', 'DESC')
                    ->findAll();
    }
}
