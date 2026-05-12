<?php

namespace ELearning\Models;

use CodeIgniter\Model;

class SkillScoreModel extends Model
{
    protected $table            = 'elearning_skill_scores';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'nama_santri', 
        'kelas', 
        'kategori_skill', 
        'jenis_tes', 
        'skor_benar', 
        'total_soal', 
        'skor_akhir', 
        'catatan',
        'created_at'
    ];

    // Gunakan penanggalan manual atau otomatis jika tipe datetime
    protected $useTimestamps = false; 
}
