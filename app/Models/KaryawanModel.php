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
}
