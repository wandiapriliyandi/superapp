<?php

namespace Perijinan\Models;

use CodeIgniter\Model;

class PerijinanModel extends Model
{
    protected $table            = 'perijinan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nisn', 
        'token',
        'jenis_izin', 
        'alasan', 
        'tanggal_mulai', 
        'tanggal_selesai', 
        'status', 
        'disetujui_oleh', 
        'catatan_petugas', 
        'waktu_kembali'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getIzin($id = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('perijinan.*, santri.nama_lengkap, santri.nama_lengkap as nama_santri, santri.nis');
        $builder->join('santri', 'santri.nisn = perijinan.nisn');
        
        if ($id) {
            $data = $builder->where('perijinan.id', $id)->get()->getRowArray();
            if ($data) {
                $data['is_terlambat'] = ($data['status'] == 'Aktif' && strtotime(date('Y-m-d H:i:s')) > strtotime($data['tanggal_selesai']));
            }
            return $data;
        }

        $results = $builder->orderBy('perijinan.id', 'DESC')->get()->getResultArray();
        foreach ($results as &$r) {
            $r['is_terlambat'] = ($r['status'] == 'Aktif' && strtotime(date('Y-m-d H:i:s')) > strtotime($r['tanggal_selesai']));
        }
        return $results;
    }
}
