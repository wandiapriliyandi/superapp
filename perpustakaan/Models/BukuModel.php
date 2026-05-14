<?php

namespace Perpustakaan\Models;

use CodeIgniter\Model;

class BukuModel extends Model
{
    protected $table            = 'perpus_buku';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_buku', 'judul', 'pengarang', 'penerbit', 
        'tahun_terbit', 'kategori', 'stok', 'lokasi', 
        'file_digital', 'link_eksternal', 'is_drive', 'cover', 'deskripsi'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getByLokasi($lokasi)
    {
        return $this->where('lokasi', $lokasi)->findAll();
    }
}
