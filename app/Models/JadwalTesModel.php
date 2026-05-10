<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalTesModel extends Model
{
    protected $table            = 'ppdb_jadwal_tes';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nama_tes', 'tanggal', 'jam', 'tempat', 'kuota'];
}
