<?php

namespace Kepegawaian\Controllers;

use App\Controllers\BaseController;
use Kepegawaian\Models\JabatanModel;

class Jabatan extends BaseController
{
    protected $jabatanModel;

    public function __construct()
    {
        $this->jabatanModel = new JabatanModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Jabatan / Amanah',
            'jabatan' => $this->jabatanModel->findAll()
        ];

        return view('Kepegawaian\Views\jabatan\index', $data);
    }

    public function save()
    {
        $this->jabatanModel->save([
            'nama_jabatan' => $this->request->getPost('nama_jabatan'),
            'gaji_pokok' => $this->request->getPost('gaji_pokok'),
            'tunjangan_makan' => $this->request->getPost('tunjangan_makan'),
            'tunjangan_transport' => $this->request->getPost('tunjangan_transport')
        ]);

        return redirect()->to(base_url('kepegawaian/jabatan'))->with('success', 'Jabatan berhasil disimpan.');
    }

    public function delete($id)
    {
        $this->jabatanModel->delete($id);
        return redirect()->to(base_url('kepegawaian/jabatan'))->with('success', 'Jabatan berhasil dihapus.');
    }
}
