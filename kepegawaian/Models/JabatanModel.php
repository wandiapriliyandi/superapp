<?php

namespace Kepegawaian\Models;

use CodeIgniter\Model;

class JabatanModel extends Model
{
    protected $table            = 'hr_jabatan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_jabatan', 'gaji_pokok', 'tunjangan_makan', 'tunjangan_transport'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
