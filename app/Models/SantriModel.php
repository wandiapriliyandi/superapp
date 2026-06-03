<?php

namespace App\Models;

use CodeIgniter\Model;

class SantriModel extends Model
{
    protected $table            = 'santri';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true; // Menggunakan soft deletes sesuai migrasi (deleted_at)
    
    // Field yang boleh diisi
    protected $allowedFields    = [
        'nisn', 'nis', 'nik', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 
        'jenis_kelamin', 'alamat', 'no_hp', 'email', 'foto', 'status_santri', 'id_tahun_ajaran'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $beforeInsert = ['normalizeOptionalIdentifiers'];
    protected $beforeUpdate = ['normalizeOptionalIdentifiers'];

    protected function normalizeOptionalIdentifiers(array $data): array
    {
        if (!isset($data['data'])) {
            return $data;
        }

        foreach (['nisn', 'nis', 'nik'] as $field) {
            if (array_key_exists($field, $data['data']) && trim((string) $data['data'][$field]) === '') {
                $data['data'][$field] = null;
            }
        }

        return $data;
    }
}
