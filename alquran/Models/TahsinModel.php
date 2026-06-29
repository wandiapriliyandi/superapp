<?php

namespace Alquran\Models;

use CodeIgniter\Model;

class TahsinModel extends Model
{
    protected $table            = 'alquran_tahsin';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'santri_id',
        'makharijul_huruf',
        'sifat_huruf',
        'tajwid',
        'detail_penilaian',
        'catatan',
        'tanggal_penilaian',
        'penguji_id'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getTahsinBySantri($santriId)
    {
        return $this->select('alquran_tahsin.*, users.nama_lengkap as nama_penguji')
            ->join('users', 'users.id = alquran_tahsin.penguji_id', 'left')
            ->where('santri_id', $santriId)
            ->orderBy('tanggal_penilaian', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();
    }
}
