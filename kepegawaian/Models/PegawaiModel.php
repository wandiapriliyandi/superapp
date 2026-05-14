<?php

namespace Kepegawaian\Models;

use CodeIgniter\Model;

class PegawaiModel extends Model
{
    protected $table            = 'hr_pegawai';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nik', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 
        'jenis_kelamin', 'alamat', 'no_hp', 'email', 
        'departemen_id', 'jabatan_id', 'status_pegawai', 
        'tanggal_masuk', 'foto'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getPegawaiFull()
    {
        return $this->select('hr_pegawai.*, hr_departemen.nama_departemen, hr_jabatan.nama_jabatan')
                    ->join('hr_departemen', 'hr_departemen.id = hr_pegawai.departemen_id', 'left')
                    ->join('hr_jabatan', 'hr_jabatan.id = hr_pegawai.jabatan_id', 'left')
                    ->findAll();
    }
}
