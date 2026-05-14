<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table            = 'akademik_kelas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_kelas', 'tingkat', 'id_wali_kelas'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getKelasWithWali()
    {
        return $this->select('akademik_kelas.*, hr_pegawai.nama_lengkap as nama_wali')
                    ->join('hr_pegawai', 'hr_pegawai.id = akademik_kelas.id_wali_kelas', 'left')
                    ->findAll();
    }
}
