<?php

namespace ELearning\Controllers;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $data = [
            'title' => 'Dashboard E-Learning',
            'total_materi' => $db->table('ele_materi')->countAllResults(),
            'total_ujian' => $db->table('ele_ujian')->countAllResults(),
            'total_skill' => $db->table('ele_skill_materi')->countAllResults(),
        ];

        return view('ELearning\Views\dashboard', $data);
    }
}
