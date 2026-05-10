<?php

namespace App\Models;

use CodeIgniter\Model;

class SyaratBerkasModel extends Model
{
    protected $table            = 'ppdb_syarat_berkas';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nama_berkas', 'is_wajib'];
}
