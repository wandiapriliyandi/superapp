<?php

namespace Kepegawaian\Models;

use CodeIgniter\Model;

class CutiModel extends Model
{
    protected $table            = 'hr_cuti';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pegawai_id', 'jenis_cuti', 'tanggal_mulai', 
        'tanggal_selesai', 'alasan', 'status', 'disetujui_oleh'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function getCutiFull()
    {
        return $this->select('hr_cuti.*, hr_pegawai.nama_lengkap, hr_pegawai.nik')
                    ->join('hr_pegawai', 'hr_pegawai.id = hr_cuti.pegawai_id')
                    ->findAll();
    }
}
