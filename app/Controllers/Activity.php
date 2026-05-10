<?php

namespace App\Controllers;

use App\Models\ActivityModel;

class Activity extends BaseController
{
    public function index()
    {
        $model = new ActivityModel();
        
        $data = [
            'title' => 'Log Aktivitas Sistem',
            'logs'  => $model->orderBy('created_at', 'DESC')->findAll(500), // Tampilkan 500 log terbaru
        ];

        return view('activity/index', $data);
    }
}
