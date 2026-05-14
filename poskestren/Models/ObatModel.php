<?php

namespace Poskestren\Models;

use CodeIgniter\Model;

class ObatModel extends Model
{
    protected $table            = 'pos_obat';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_obat', 'satuan', 'stok', 'deskripsi'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
