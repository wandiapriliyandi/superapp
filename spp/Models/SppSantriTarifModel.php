<?php

namespace Spp\Models;

use CodeIgniter\Model;

class SppSantriTarifModel extends Model
{
    protected $table            = 'spp_santri_tarif';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nisn', 'tarif_id', 'nominal_diskon', 'keterangan_diskon', 'created_at'];

    protected $useTimestamps = false; // We use created_at manually if needed or via DB default

    public function getMappingWithDetails($nisn = null)
    {
        $builder = $this->select('spp_santri_tarif.*, santri.nama_lengkap as nama_santri, spp_tarif.nama_tarif, spp_tarif.nominal, spp_tarif.tipe')
                        ->join('santri', 'santri.nisn = spp_santri_tarif.nisn')
                        ->join('spp_tarif', 'spp_tarif.id = spp_santri_tarif.tarif_id');
        
        if ($nisn) {
            $builder->where('spp_santri_tarif.nisn', $nisn);
        }

        return $builder->findAll();
    }
}
