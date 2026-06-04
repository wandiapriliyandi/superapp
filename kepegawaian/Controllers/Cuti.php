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
        helper('activity');
        $pegawai_id = $this->request->getPost('pegawai_id');
        $jenis_cuti = $this->request->getPost('jenis_cuti');
        $this->cutiModel->save([
            'pegawai_id' => $pegawai_id,
            'jenis_cuti' => $jenis_cuti,
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'alasan' => $this->request->getPost('alasan'),
            'status' => 'Pending'
        ]);

        $pegawai = $this->pegawaiModel->find($pegawai_id);
        log_activity('Mengajukan Cuti Pegawai', 'Kepegawaian', 'Pegawai: ' . ($pegawai['nama_lengkap'] ?? '') . ', Jenis Cuti: ' . $jenis_cuti);

        return redirect()->to(base_url('kepegawaian/cuti'))->with('success', 'Pengajuan cuti berhasil dikirim.');
    }

    public function approve($id)
    {
        helper('activity');
        $this->cutiModel->update($id, [
            'status' => 'Disetujui',
            'disetujui_oleh' => 1 // ID Admin
        ]);

        $cuti = $this->cutiModel->find($id);
        $pegawai = $this->pegawaiModel->find($cuti['pegawai_id'] ?? 0);
        log_activity('Menyetujui Cuti Pegawai', 'Kepegawaian', 'Pegawai: ' . ($pegawai['nama_lengkap'] ?? '') . ', Jenis Cuti: ' . ($cuti['jenis_cuti'] ?? ''));

        return redirect()->to(base_url('kepegawaian/cuti'))->with('success', 'Pengajuan cuti disetujui.');
    }
}
