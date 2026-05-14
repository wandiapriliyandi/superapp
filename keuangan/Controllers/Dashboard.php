<?php

namespace Keuangan\Controllers;

use App\Controllers\BaseController;
use Keuangan\Models\AkunModel;
use Keuangan\Models\JurnalModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $akunModel = new AkunModel();
        $jurnalModel = new JurnalModel();

        $data = [
            'title' => 'Dashboard Keuangan Profesional',
            'total_akun' => $akunModel->countAllResults(),
            'total_jurnal' => $jurnalModel->countAllResults(),
            'kas_masuk_bulan_ini' => 0, // Placeholder
            'kas_keluar_bulan_ini' => 0, // Placeholder
        ];

        return view('Keuangan\Views\dashboard', $data);
    }
}
