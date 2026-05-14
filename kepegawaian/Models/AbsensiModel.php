<?php

namespace Kepegawaian\Models;

use CodeIgniter\Model;

class AbsensiModel extends Model
{
    protected $table            = 'hr_absensi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['pegawai_id', 'tanggal', 'jam_masuk', 'jam_pulang', 'status', 'keterangan'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function getAbsensiFull($tanggal = null)
    {
        $builder = $this->select('hr_absensi.*, hr_pegawai.nama_lengkap, hr_pegawai.nik')
                        ->join('hr_pegawai', 'hr_pegawai.id = hr_absensi.pegawai_id');
        
        if ($tanggal) {
            $builder->where('hr_absensi.tanggal', $tanggal);
        }

        return $builder->findAll();
    }
}
