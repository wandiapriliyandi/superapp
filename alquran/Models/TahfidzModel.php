<?php

namespace Alquran\Models;

use CodeIgniter\Model;

class TahfidzModel extends Model
{
    protected $table            = 'alquran_tahfidz';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'santri_id',
        'tipe_setoran',
        'juz',
        'surah_mulai',
        'ayat_mulai',
        'surah_selesai',
        'ayat_selesai',
        'predikat',
        'tanggal_setor',
        'penguji_id',
        'catatan'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getTahfidzBySantri($santriId)
    {
        return $this->select('alquran_tahfidz.*, users.nama_lengkap as nama_penguji')
            ->join('users', 'users.id = alquran_tahfidz.penguji_id', 'left')
            ->where('santri_id', $santriId)
            ->orderBy('tanggal_setor', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();
    }
}
