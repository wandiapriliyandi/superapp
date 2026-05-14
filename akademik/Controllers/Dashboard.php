<?php

namespace Akademik\Controllers;

use App\Controllers\BaseController;
use Akademik\Models\SantriModel; // Assuming this model exists

class Dashboard extends BaseController
{
    public function index()
    {
        $santriModel = new \App\Models\SantriModel();
        $kelasModel = new \App\Models\KelasModel();
        $mapelModel = new \App\Models\MapelModel();
        $jadwalModel = new \App\Models\JadwalModel();

        $data = [
            'title' => 'Dashboard Akademik',
            'total_santri' => $santriModel->countAllResults(),
            'total_putra' => $santriModel->where('jenis_kelamin', 'L')->countAllResults(),
            'total_putri' => $santriModel->where('jenis_kelamin', 'P')->countAllResults(),
            'santri_aktif' => $santriModel->where('status_santri', 'Aktif')->countAllResults(),
            'total_kelas' => $kelasModel->countAllResults(),
            'total_mapel' => $mapelModel->countAllResults(),
            'total_jadwal' => $jadwalModel->countAllResults(),
        ];

        return view('Akademik\Views\dashboard', $data);
    }
}

