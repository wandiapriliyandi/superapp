<?php

namespace Kepegawaian\Controllers;

use App\Controllers\BaseController;
use Kepegawaian\Models\CutiModel;
use Kepegawaian\Models\PegawaiModel;

class Cuti extends BaseController
{
    protected $cutiModel;
    protected $pegawaiModel;

    public function __construct()
    {
        $this->cutiModel = new CutiModel();
        $this->pegawaiModel = new PegawaiModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Pengajuan Cuti & Izin',
            'cuti' => $this->cutiModel->getCutiFull(),
            'pegawai' => $this->pegawaiModel->findAll()
        ];

        return view('Kepegawaian\Views\cuti\index', $data);
    }

    public function save()
    {
        $this->cutiModel->save([
            'pegawai_id' => $this->request->getPost('pegawai_id'),
            'jenis_cuti' => $this->request->getPost('jenis_cuti'),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'alasan' => $this->request->getPost('alasan'),
            'status' => 'Pending'
        ]);

        return redirect()->to(base_url('kepegawaian/cuti'))->with('success', 'Pengajuan cuti berhasil dikirim.');
    }

    public function approve($id)
    {
        $this->cutiModel->update($id, [
            'status' => 'Disetujui',
            'disetujui_oleh' => 1 // ID Admin
        ]);

        return redirect()->to(base_url('kepegawaian/cuti'))->with('success', 'Pengajuan cuti disetujui.');
    }
}
