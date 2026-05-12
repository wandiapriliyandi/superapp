<?php

namespace ELearning\Models;

use CodeIgniter\Model;

class UjianModel extends Model
{
    protected $table            = 'ujian_online';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'judul', 
        'deskripsi', 
        'mata_pelajaran', 
        'kelas', 
        'durasi_menit', 
        'tgl_mulai', 
        'tgl_selesai', 
        'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
