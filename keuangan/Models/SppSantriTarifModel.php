<?php

namespace Keuangan\Models;

use CodeIgniter\Model;

class SppSantriTarifModel extends Model
{
    protected $table            = 'spp_santri_tarif';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['santri_id', 'tarif_id', 'created_at'];

    protected $useTimestamps = false; // We use created_at manually if needed or via DB default

    public function getMappingWithDetails($santri_id = null)
    {
        $builder = $this->select('spp_santri_tarif.*, santri.nama_lengkap as nama_santri, spp_tarif.nama_tarif, spp_tarif.nominal, spp_tarif.tipe')
                        ->join('santri', 'santri.id = spp_santri_tarif.santri_id')
                        ->join('spp_tarif', 'spp_tarif.id = spp_santri_tarif.tarif_id');
        
        if ($santri_id) {
            $builder->where('spp_santri_tarif.santri_id', $santri_id);
        }

        return $builder->findAll();
    }
}
