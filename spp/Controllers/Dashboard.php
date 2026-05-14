<?php

namespace Spp\Controllers;

use App\Controllers\BaseController;
use Spp\Models\SppTagihanModel;
use Spp\Models\SppPembayaranModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $tagihanModel = new SppTagihanModel();
        $pembayaranModel = new SppPembayaranModel();

        $data = [
            'title' => 'Dashboard SPP',
            'total_tagihan' => $tagihanModel->selectSum('nominal_tagihan')->first()['nominal_tagihan'] ?? 0,
            'total_terbayar' => $tagihanModel->selectSum('total_terbayar')->first()['total_terbayar'] ?? 0,
            'tagihan_lunas' => $tagihanModel->where('status', 'Lunas')->countAllResults(),
            'tagihan_belum' => $tagihanModel->where('status', 'Belum Lunas')->countAllResults(),
        ];

        return view('Spp\Views\dashboard', $data);
    }
}
