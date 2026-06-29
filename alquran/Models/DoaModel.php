<?php

namespace Alquran\Models;

use CodeIgniter\Model;

class DoaModel extends Model
{
    protected $table            = 'alquran_doa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'santri_id',
        'nama_doa',
        'status',
        'tanggal_setor',
        'penguji_id',
        'catatan'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function getDoaBySantri($santriId)
    {
        return $this->select('alquran_doa.*, users.nama_lengkap as nama_penguji')
                    ->join('users', 'users.id = alquran_doa.penguji_id', 'left')
                    ->where('alquran_doa.santri_id', $santriId)
                    ->where('alquran_doa.deleted_at IS NULL')
                    ->orderBy('alquran_doa.tanggal_setor', 'DESC')
                    ->findAll();
    }
}
