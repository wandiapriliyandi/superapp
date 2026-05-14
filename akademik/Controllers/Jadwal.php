<?php

namespace Akademik\Controllers;

use App\Controllers\BaseController;
use App\Models\JadwalModel;
use App\Models\KelasModel;
use App\Models\MapelModel;
use App\Models\TahunAjaranModel;

class Jadwal extends BaseController
{
    protected $jadwalModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalModel();
    }

    public function index()
    {
        $kelasModel = new KelasModel();
        $id_kelas = $this->request->getGet('kelas');
        
        $data = [
            'title' => 'Jadwal Pelajaran',
            'kelas' => $kelasModel->findAll(),
            'jadwal' => $this->jadwalModel->getJadwalLengkap($id_kelas),
            'selected_kelas' => $id_kelas
        ];

        return view('Akademik\Views\jadwal\index', $data);
    }

    public function create()
    {
        $db = \Config\Database::connect();
        $data = [
            'title' => 'Tambah Jadwal',
            'kelas' => (new KelasModel())->findAll(),
            'mapel' => (new MapelModel())->findAll(),
            'tahun_ajaran' => (new TahunAjaranModel())->where('status', 'Aktif')->findAll(),
            'teachers' => $db->table('hr_pegawai')->select('id, nama_lengkap')->get()->getResultArray()
        ];

        return view('Akademik\Views\jadwal\form', $data);
    }

    public function store()
    {
        $this->jadwalModel->save([
            'id_tahun_ajaran' => $this->request->getPost('id_tahun_ajaran'),
            'id_kelas' => $this->request->getPost('id_kelas'),
            'id_mapel' => $this->request->getPost('id_mapel'),
            'id_guru' => $this->request->getPost('id_guru'),
            'hari' => $this->request->getPost('hari'),
            'jam_mulai' => $this->request->getPost('jam_mulai'),
            'jam_selesai' => $this->request->getPost('jam_selesai'),
            'ruangan' => $this->request->getPost('ruangan'),
        ]);

        return redirect()->to(base_url('akademik/jadwal'))->with('success', 'Jadwal berhasil disimpan');
    }

    public function delete($id)
    {
        $this->jadwalModel->delete($id);
        return redirect()->to(base_url('akademik/jadwal'))->with('success', 'Jadwal berhasil dihapus');
    }
}
