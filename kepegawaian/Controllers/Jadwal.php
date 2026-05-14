<?php

namespace Kepegawaian\Controllers;

use App\Controllers\BaseController;
use Kepegawaian\Models\JadwalModel;
use Kepegawaian\Models\PegawaiModel;

class Jadwal extends BaseController
{
    protected $jadwalModel;
    protected $pegawaiModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalModel();
        $this->pegawaiModel = new PegawaiModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Penjadwalan Asatidz & Staff',
            'jadwal' => $this->jadwalModel->getJadwalFull(),
            'pegawai' => $this->pegawaiModel->findAll()
        ];

        return view('Kepegawaian\Views\jadwal\index', $data);
    }

    public function save()
    {
        $this->jadwalModel->save([
            'pegawai_id' => $this->request->getPost('pegawai_id'),
            'hari' => $this->request->getPost('hari'),
            'jam_mulai' => $this->request->getPost('jam_mulai'),
            'jam_selesai' => $this->request->getPost('jam_selesai'),
            'kegiatan' => $this->request->getPost('kegiatan'),
            'lokasi' => $this->request->getPost('lokasi'),
        ]);

        return redirect()->to(base_url('kepegawaian/jadwal'))->with('success', 'Jadwal berhasil disimpan.');
    }

    public function delete($id)
    {
        $this->jadwalModel->delete($id);
        return redirect()->to(base_url('kepegawaian/jadwal'))->with('success', 'Jadwal berhasil dihapus.');
    }
}
