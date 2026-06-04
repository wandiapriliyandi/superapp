<?php

namespace Poskestren\Models;

use CodeIgniter\Model;

class KunjunganModel extends Model
{
    protected $table            = 'pos_kunjungan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'nisn',
        'tgl_kunjungan', 
        'keluhan', 
        'diagnosa', 
        'tindakan', 
        'petugas_id', 
        'status'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getKunjungan($id = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('pos_kunjungan.*, santri.nama_lengkap as nama_santri, santri.nis, akademik_kelas.nama_kelas');
        $builder->join('santri', 'santri.nisn = pos_kunjungan.nisn');
        $builder->join('akademik_kelas', 'akademik_kelas.id = santri.kelas_id', 'left');
        
        if ($id) {
            return $builder->where('pos_kunjungan.id', $id)->get()->getRowArray();
        }

        return $builder->orderBy('pos_kunjungan.tgl_kunjungan', 'DESC')->get()->getResultArray();
    }
}
