<?php

namespace ELearning\Models;

use CodeIgniter\Model;

class MateriModel extends Model
{
    protected $table            = 'materi_pembelajaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'judul', 
        'deskripsi', 
        'mata_pelajaran', 
        'kelas', 
        'link_video', 
        'file_materi', 
        'guru_pengampu'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
