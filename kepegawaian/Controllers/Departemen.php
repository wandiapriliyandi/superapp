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
        helper('activity');
        $nama_departemen = $this->request->getPost('nama_departemen');
        $this->deptModel->save([
            'nama_departemen' => $nama_departemen,
            'keterangan' => $this->request->getPost('keterangan')
        ]);

        log_activity('Menyimpan Data Departemen', 'Kepegawaian', 'Nama Departemen: ' . $nama_departemen);

        return redirect()->to(base_url('kepegawaian/departemen'))->with('success', 'Departemen berhasil disimpan.');
    }

    public function delete($id)
    {
        helper('activity');
        $dept = $this->deptModel->find($id);
        if ($dept) {
            $this->deptModel->delete($id);
            log_activity('Menghapus Data Departemen', 'Kepegawaian', 'Nama Departemen: ' . ($dept['nama_departemen'] ?? ''));
        }
        return redirect()->to(base_url('kepegawaian/departemen'))->with('success', 'Departemen berhasil dihapus.');
    }
}
