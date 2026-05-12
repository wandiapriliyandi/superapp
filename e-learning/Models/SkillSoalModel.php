<?php

namespace ELearning\Models;

use CodeIgniter\Model;

class SkillSoalModel extends Model
{
    protected $table            = 'skill_soal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['slug_bab', 'pertanyaan', 'pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d', 'kunci_jawaban', 'pembahasan'];

    protected $useTimestamps = false;
}
