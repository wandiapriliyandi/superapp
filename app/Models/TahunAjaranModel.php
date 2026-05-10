<?php

namespace App\Models;

use CodeIgniter\Model;

class TahunAjaranModel extends Model
{
    protected $table            = 'tahun_ajaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['tahun_ajaran', 'semester', 'status', 'tgl_mulai', 'tgl_selesai'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Fungsi untuk menonaktifkan semua tahun ajaran lain
     * sebelum mengaktifkan satu yang baru.
     */
    public function setActive($id)
    {
        $this->builder()->update(['status' => 'Tidak Aktif']);
        return $this->update($id, ['status' => 'Aktif']);
    }
}
