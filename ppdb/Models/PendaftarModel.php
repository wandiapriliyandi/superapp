<?php

namespace Ppdb\Models;

use CodeIgniter\Model;

class PendaftarModel extends Model
{
    protected $table            = 'ppdb_pendaftar';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_pendaftaran', 'nama_lengkap', 'jenis_kelamin', 
        'tempat_lahir', 'tanggal_lahir', 'alamat', 
        'no_hp_ortu', 'status_seleksi', 'id_tahun_ajaran', 'sponsor'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Menghasilkan nomor pendaftaran otomatis
     * Format: PPDB-YYYYMM-XXXX
     */
    public function generateNomor()
    {
        $prefix = "PPDB-" . date('Ym') . "-";
        $last = $this->like('nomor_pendaftaran', $prefix, 'after')
                     ->orderBy('id', 'DESC')
                     ->first();

        if (!$last) {
            return $prefix . "0001";
        }

        $lastNumber = (int) substr($last['nomor_pendaftaran'], -4);
        return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}
