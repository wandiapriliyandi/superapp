<?php

namespace Kepegawaian\Controllers;

use App\Controllers\BaseController;
use Kepegawaian\Models\PegawaiModel;
use Kepegawaian\Models\DepartemenModel;
use Kepegawaian\Models\JabatanModel;

class Pegawai extends BaseController
{
    protected $pegawaiModel;
    protected $deptModel;
    protected $jabatanModel;

    public function __construct()
    {
        $this->pegawaiModel = new PegawaiModel();
        $this->deptModel = new DepartemenModel();
        $this->jabatanModel = new JabatanModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Asatidz & Staff',
            'pegawai' => $this->pegawaiModel->getPegawaiFull()
        ];

        return view('Kepegawaian\Views\pegawai\index', $data);
    }

    public function add()
    {
        $data = [
            'title' => 'Tambah SDM Baru',
            'departemen' => $this->deptModel->findAll(),
            'jabatan' => $this->jabatanModel->findAll()
        ];

        return view('Kepegawaian\Views\pegawai\form', $data);
    }

    public function save()
    {
        $foto = $this->request->getFile('foto');
        $fileName = 'default.png';

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $fileName = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/pegawai', $fileName);
        }

        $this->pegawaiModel->save([
            'nik' => $this->request->getPost('nik'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'tempat_lahir' => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'no_hp' => $this->request->getPost('no_hp'),
            'email' => $this->request->getPost('email'),
            'departemen_id' => $this->request->getPost('departemen_id'),
            'jabatan_id' => $this->request->getPost('jabatan_id'),
            'status_pegawai' => $this->request->getPost('status_pegawai'),
            'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
            'foto' => $fileName
        ]);

        return redirect()->to(base_url('kepegawaian/pegawai'))->with('success', 'Data SDM berhasil disimpan.');
    }

    public function delete($id)
    {
        $this->pegawaiModel->delete($id);
        return redirect()->to(base_url('kepegawaian/pegawai'))->with('success', 'Data SDM berhasil dihapus.');
    }
}
