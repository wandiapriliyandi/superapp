<?php

namespace ELearning\Models;

use CodeIgniter\Model;

class SkillMateriModel extends Model
{
    protected $table            = 'skill_materi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['slug_bab', 'judul', 'kategori', 'icon', 'color', 'ringkasan', 'materi_lengkap', 'created_at'];

    protected $useTimestamps = false;
}
