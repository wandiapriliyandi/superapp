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
        helper('activity');
        $nama_jabatan = $this->request->getPost('nama_jabatan');
        $this->jabatanModel->save([
            'nama_jabatan' => $nama_jabatan,
            'gaji_pokok' => $this->request->getPost('gaji_pokok'),
            'tunjangan_makan' => $this->request->getPost('tunjangan_makan'),
            'tunjangan_transport' => $this->request->getPost('tunjangan_transport')
        ]);

        log_activity('Menyimpan Data Jabatan', 'Kepegawaian', 'Nama Jabatan: ' . $nama_jabatan);

        return redirect()->to(base_url('kepegawaian/jabatan'))->with('success', 'Jabatan berhasil disimpan.');
    }

    public function delete($id)
    {
        helper('activity');
        $jabatan = $this->jabatanModel->find($id);
        if ($jabatan) {
            $this->jabatanModel->delete($id);
            log_activity('Menghapus Data Jabatan', 'Kepegawaian', 'Nama Jabatan: ' . ($jabatan['nama_jabatan'] ?? ''));
        }
        return redirect()->to(base_url('kepegawaian/jabatan'))->with('success', 'Jabatan berhasil dihapus.');
    }
}
