<?php

namespace App\Models;

use CodeIgniter\Model;

class PendaftarBerkasModel extends Model
{
    protected $table            = 'pendaftar_berkas';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['id_pendaftar', 'jenis_berkas', 'file_path', 'status', 'catatan'];
    protected $useTimestamps    = true;
}
