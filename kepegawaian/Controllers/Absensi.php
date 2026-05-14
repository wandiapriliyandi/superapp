<?php

namespace Kepegawaian\Controllers;

use App\Controllers\BaseController;
use Kepegawaian\Models\AbsensiModel;
use Kepegawaian\Models\PegawaiModel;

class Absensi extends BaseController
{
    protected $absensiModel;
    protected $pegawaiModel;

    public function __construct()
    {
        $this->absensiModel = new AbsensiModel();
        $this->pegawaiModel = new PegawaiModel();
    }

    public function index()
    {
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');
        $data = [
            'title' => 'Presensi Kehadiran',
            'absensi' => $this->absensiModel->getAbsensiFull($tanggal),
            'pegawai' => $this->pegawaiModel->findAll(),
            'tanggal_filter' => $tanggal
        ];

        return view('Kepegawaian\Views\absensi\index', $data);
    }

    public function save()
    {
        $pegawai_ids = $this->request->getPost('pegawai_id');
        $status = $this->request->getPost('status');
        $tanggal = $this->request->getPost('tanggal');

        foreach ($pegawai_ids as $idx => $pid) {
            // Cek apakah sudah ada absen hari ini
            $exist = $this->absensiModel->where('pegawai_id', $pid)
                                        ->where('tanggal', $tanggal)
                                        ->first();
            
            $saveData = [
                'pegawai_id' => $pid,
                'tanggal' => $tanggal,
                'status' => $status[$idx],
                'jam_masuk' => ($status[$idx] == 'Hadir') ? date('H:i:s') : null
            ];

            if ($exist) {
                $this->absensiModel->update($exist['id'], $saveData);
            } else {
                $this->absensiModel->save($saveData);
            }
        }

        return redirect()->to(base_url('kepegawaian/absensi?tanggal='.$tanggal))->with('success', 'Presensi berhasil diperbarui.');
    }
}
