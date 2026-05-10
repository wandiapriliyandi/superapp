<?php

namespace Ppdb\Controllers;

use App\Controllers\BaseController;
use Ppdb\Models\PendaftarModel;
use Ppdb\Models\PengaturanModel;

class Dashboard extends BaseController
{
    protected $pendaftarModel;
    protected $pengaturanModel;

    public function __construct()
    {
        $this->pendaftarModel = new PendaftarModel();
        $this->pengaturanModel = new PengaturanModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Panel Statistik PPDB',
            'stats' => [
                'total'      => $this->pendaftarModel->countAllResults(),
                'pending'    => $this->pendaftarModel->where('status_seleksi', 'Pending')->countAllResults(),
                'lulus'      => $this->pendaftarModel->where('status_seleksi', 'Lulus')->countAllResults(),
                'tidak_lulus'=> $this->pendaftarModel->where('status_seleksi', 'Tidak Lulus')->countAllResults(),
            ],
            'gelombang' => $this->pengaturanModel->findAll()
        ];

        return view('Ppdb\Views\dashboard', $data);
    }
}
