<?php

namespace Kepegawaian\Controllers;

use App\Controllers\BaseController;
use Kepegawaian\Models\DepartemenModel;

class Departemen extends BaseController
{
    protected $deptModel;

    public function __construct()
    {
        $this->deptModel = new DepartemenModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Departemen / Unit Kerja',
            'departemen' => $this->deptModel->findAll()
        ];

        return view('Kepegawaian\Views\departemen\index', $data);
    }

    public function save()
    {
        $this->deptModel->save([
            'nama_departemen' => $this->request->getPost('nama_departemen'),
            'keterangan' => $this->request->getPost('keterangan')
        ]);

        return redirect()->to(base_url('kepegawaian/departemen'))->with('success', 'Departemen berhasil disimpan.');
    }

    public function delete($id)
    {
        $this->deptModel->delete($id);
        return redirect()->to(base_url('kepegawaian/departemen'))->with('success', 'Departemen berhasil dihapus.');
    }
}
