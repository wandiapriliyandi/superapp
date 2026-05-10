<?php

namespace Ppdb\Models;

use CodeIgniter\Model;

class PengaturanModel extends Model
{
    protected $table            = 'ppdb_pengaturan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['gelombang', 'kuota', 'tgl_buka', 'tgl_tutup', 'status'];

    protected $useTimestamps = true;

    /**
     * Ambil gelombang yang sedang aktif saat ini
     */
    public function getActive()
    {
        return $this->where('status', 'Buka')
                    ->where('tgl_buka <=', date('Y-m-d'))
                    ->where('tgl_tutup >=', date('Y-m-d'))
                    ->first();
    }
}
