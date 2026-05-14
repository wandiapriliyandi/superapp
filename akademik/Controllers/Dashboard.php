<?php

namespace Akademik\Controllers;

use App\Controllers\BaseController;
use Akademik\Models\SantriModel; // Assuming this model exists

class Dashboard extends BaseController
{
    public function index()
    {
        $santriModel = new \App\Models\SantriModel();
        $data = [
            'title' => 'Dashboard Akademik',
            'total_santri' => $santriModel->countAllResults(),
            'total_putra' => $santriModel->where('jenis_kelamin', 'L')->countAllResults(),
            'total_putri' => $santriModel->where('jenis_kelamin', 'P')->countAllResults(),
            'santri_aktif' => $santriModel->where('status_santri', 'Aktif')->countAllResults(),
        ];

        return view('Akademik\Views\dashboard', $data);
    }
}
