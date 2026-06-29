<?php

namespace Sarpras\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table            = 'inv_barang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'kode_barang', 'nama_barang', 'kategori', 'stok', 'satuan', 'lokasi', 'kondisi'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
