<?php

namespace Kepegawaian\Controllers;

use App\Controllers\BaseController;
use Kepegawaian\Models\PegawaiModel;
use Kepegawaian\Models\AbsensiModel;
use Kepegawaian\Models\CutiModel;

class Dashboard extends BaseController
{
    protected $pegawaiModel;
    protected $absensiModel;
    protected $cutiModel;

    public function __construct()
    {
        $this->pegawaiModel = new PegawaiModel();
        $this->absensiModel = new AbsensiModel();
        $this->cutiModel = new CutiModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Dashboard Kepegawaian',
            'total_pegawai' => $this->pegawaiModel->countAllResults(),
            'hadir_hari_ini' => $this->absensiModel->where('tanggal', date('Y-m-d'))->where('status', 'Hadir')->countAllResults(),
            'cuti_pending' => $this->cutiModel->where('status', 'Pending')->countAllResults(),
            'pegawai_terbaru' => $this->pegawaiModel->orderBy('created_at', 'DESC')->limit(5)->getPegawaiFull()
        ];

        return view('Kepegawaian\Views\dashboard\index', $data);
    }
}
