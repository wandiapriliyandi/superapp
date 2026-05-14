<?php

namespace Spp\Models;

use CodeIgniter\Model;

class SppTarifModel extends Model
{
    protected $table            = 'spp_tarif';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['id_tahun_akademik', 'nama_tarif', 'tipe', 'nominal', 'keterangan'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
