<?php

namespace Ppdb\Controllers;

use App\Controllers\BaseController;
use Ppdb\Models\PengaturanModel;

class Pengaturan extends BaseController
{
    protected $pengaturanModel;

    public function __construct()
    {
        $this->pengaturanModel = new PengaturanModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Pengaturan Gelombang PPDB',
            'gelombang' => $this->pengaturanModel->findAll(),
        ];

        return view('Ppdb\Views\pengaturan\index', $data);
    }

    public function save()
    {
        helper('activity');
        $data = [
            'gelombang' => $this->request->getPost('gelombang'),
            'kuota'     => $this->request->getPost('kuota'),
            'tgl_buka'  => $this->request->getPost('tgl_buka'),
            'tgl_tutup' => $this->request->getPost('tgl_tutup'),
            'status'    => $this->request->getPost('status'),
        ];

        if ($id = $this->request->getPost('id')) {
            $this->pengaturanModel->update($id, $data);
            log_activity('Mengubah Pengaturan PPDB', 'PPDB', 'Gelombang: ' . $data['gelombang'] . ', Status: ' . $data['status']);
        } else {
            $this->pengaturanModel->insert($data);
            log_activity('Menambah Pengaturan PPDB', 'PPDB', 'Gelombang: ' . $data['gelombang'] . ', Status: ' . $data['status']);
        }

        return redirect()->to('ppdb/pengaturan')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
