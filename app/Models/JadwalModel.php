<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalModel extends Model
{
    protected $table            = 'akademik_jadwal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_tahun_ajaran', 
        'id_kelas', 
        'id_mapel', 
        'id_guru', 
        'hari', 
        'jam_mulai', 
        'jam_selesai', 
        'ruangan'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getJadwalLengkap($id_kelas = null)
    {
        $builder = $this->select('akademik_jadwal.*, akademik_kelas.nama_kelas, akademik_mapel.nama_mapel, hr_pegawai.nama_lengkap as nama_guru')
                        ->join('akademik_kelas', 'akademik_kelas.id = akademik_jadwal.id_kelas')
                        ->join('akademik_mapel', 'akademik_mapel.id = akademik_jadwal.id_mapel')
                        ->join('hr_pegawai', 'hr_pegawai.id = akademik_jadwal.id_guru');
        
        if ($id_kelas) {
            $builder->where('akademik_jadwal.id_kelas', $id_kelas);
        }

        return $builder->orderBy('hari', 'ASC')->orderBy('jam_mulai', 'ASC')->findAll();
    }
}
