<?php

namespace Kepegawaian\Models;

use CodeIgniter\Model;

class PayrollModel extends Model
{
    protected $table            = 'hr_payroll';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pegawai_id', 'bulan', 'tahun', 'gaji_pokok', 
        'total_tunjangan', 'potongan', 'gaji_bersih', 
        'status_bayar', 'tanggal_bayar'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function getPayrollFull($bulan = null, $tahun = null)
    {
        $builder = $this->select('hr_payroll.*, hr_pegawai.nama_lengkap, hr_pegawai.nik, hr_jabatan.nama_jabatan')
                        ->join('hr_pegawai', 'hr_pegawai.id = hr_payroll.pegawai_id')
                        ->join('hr_jabatan', 'hr_jabatan.id = hr_pegawai.jabatan_id', 'left');
        
        if ($bulan) $builder->where('hr_payroll.bulan', $bulan);
        if ($tahun) $builder->where('hr_payroll.tahun', $tahun);

        return $builder->findAll();
    }
}
