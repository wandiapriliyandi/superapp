<?php

namespace App\Models;

use CodeIgniter\Model;

class KaryawanModel extends Model
{
    protected $table            = 'karyawan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'nip', 'nik', 'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 
        'alamat', 'no_hp', 'email', 'jabatan', 'status_pegawai', 'pendidikan_terakhir', 
        'tanggal_masuk', 'foto'
    ];
    protected $useTimestamps    = true;

    protected $beforeInsert = ['normalizeOptionalIdentifiers'];
    protected $beforeUpdate = ['normalizeOptionalIdentifiers'];

    protected function normalizeOptionalIdentifiers(array $data): array
    {
        if (!isset($data['data'])) {
            return $data;
        }

        foreach (['nip', 'nik'] as $field) {
            if (array_key_exists($field, $data['data']) && trim((string) $data['data'][$field]) === '') {
                $data['data'][$field] = null;
            }
        }

        return $data;
    }
}
