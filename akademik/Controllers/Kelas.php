<?php

namespace Akademik\Controllers;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\KaryawanModel; // Assuming KaryawanModel or HrPegawaiModel

class Kelas extends BaseController
{
    protected $kelasModel;

    public function __construct()
    {
        $this->kelasModel = new KelasModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen Kelas',
            'kelas' => $this->kelasModel->getKelasWithWali()
        ];

        return view('Akademik\Views\kelas\index', $data);
    }

    public function create()
    {
        // Fetch teachers for Wali Kelas dropdown
        $db = \Config\Database::connect();
        $teachers = $db->table('hr_pegawai')->select('id, nama_lengkap')->get()->getResultArray();

        $data = [
            'title' => 'Tambah Kelas',
            'teachers' => $teachers
        ];

        return view('Akademik\Views\kelas\form', $data);
    }

    public function store()
    {
        $this->kelasModel->save([
            'nama_kelas' => $this->request->getPost('nama_kelas'),
            'tingkat' => $this->request->getPost('tingkat'),
            'id_wali_kelas' => $this->request->getPost('id_wali_kelas'),
        ]);

        return redirect()->to(base_url('akademik/kelas'))->with('success', 'Data kelas berhasil disimpan');
    }

    public function edit($id)
    {
        $db = \Config\Database::connect();
        $teachers = $db->table('hr_pegawai')->select('id, nama_lengkap')->get()->getResultArray();

        $data = [
            'title' => 'Edit Kelas',
            'kelas' => $this->kelasModel->find($id),
            'teachers' => $teachers
        ];

        return view('Akademik\Views\kelas\form', $data);
    }

    public function update($id)
    {
        $this->kelasModel->update($id, [
            'nama_kelas' => $this->request->getPost('nama_kelas'),
            'tingkat' => $this->request->getPost('tingkat'),
            'id_wali_kelas' => $this->request->getPost('id_wali_kelas'),
        ]);

        return redirect()->to(base_url('akademik/kelas'))->with('success', 'Data kelas berhasil diperbarui');
    }

    public function delete($id)
    {
        $this->kelasModel->delete($id);
        return redirect()->to(base_url('akademik/kelas'))->with('success', 'Data kelas berhasil dihapus');
    }
}
