<?php

namespace App\Models;

use CodeIgniter\Model;

class NilaiModel extends Model
{
    protected $table            = 'akademik_nilai';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nisn', 
        'id_mapel', 
        'id_tahun_ajaran', 
        'nilai_tugas', 
        'nilai_uts', 
        'nilai_uas', 
        'nilai_akhir', 
        'predikat', 
        'keterangan'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getNilaiSantri($nisn, $id_tahun_ajaran)
    {
        return $this->select('akademik_nilai.*, akademik_mapel.nama_mapel, akademik_mapel.kode_mapel')
                    ->join('akademik_mapel', 'akademik_mapel.id = akademik_nilai.id_mapel')
                    ->where('nisn', $nisn)
                    ->where('id_tahun_ajaran', $id_tahun_ajaran)
                    ->findAll();
    }
}
