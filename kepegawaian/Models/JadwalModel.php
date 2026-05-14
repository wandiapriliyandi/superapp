<?php

namespace Kepegawaian\Models;

use CodeIgniter\Model;

class JadwalModel extends Model
{
    protected $table            = 'hr_jadwal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['pegawai_id', 'hari', 'jam_mulai', 'jam_selesai', 'kegiatan', 'lokasi'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getJadwalFull($hari = null)
    {
        $builder = $this->select('hr_jadwal.*, hr_pegawai.nama_lengkap, hr_pegawai.nik')
                        ->join('hr_pegawai', 'hr_pegawai.id = hr_jadwal.pegawai_id');
        
        if ($hari) $builder->where('hr_jadwal.hari', $hari);
        
        return $builder->orderBy('FIELD(hari, "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu")')
                        ->orderBy('jam_mulai', 'ASC')
                        ->findAll();
    }
}
