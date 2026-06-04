<?php

namespace Akademik\Controllers;

use App\Controllers\BaseController;
use App\Models\MapelModel;

class Mapel extends BaseController
{
    protected $mapelModel;

    public function __construct()
    {
        $this->mapelModel = new MapelModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Mata Pelajaran',
            'mapel' => $this->mapelModel->findAll()
        ];

        return view('Akademik\Views\mapel\index', $data);
    }

    public function create()
    {
        $data = ['title' => 'Tambah Mata Pelajaran'];
        return view('Akademik\Views\mapel\form', $data);
    }

    public function store()
    {
        helper('activity');
        $nama_mapel = $this->request->getPost('nama_mapel');
        $this->mapelModel->save([
            'kode_mapel' => $this->request->getPost('kode_mapel'),
            'nama_mapel' => $nama_mapel,
            'kelompok' => $this->request->getPost('kelompok'),
        ]);

        log_activity('Menambah Mata Pelajaran', 'Akademik', 'Nama Mapel: ' . $nama_mapel);

        return redirect()->to(base_url('akademik/mapel'))->with('success', 'Mata pelajaran berhasil disimpan');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Mata Pelajaran',
            'mapel' => $this->mapelModel->find($id)
        ];

        return view('Akademik\Views\mapel\form', $data);
    }

    public function update($id)
    {
        helper('activity');
        $nama_mapel = $this->request->getPost('nama_mapel');
        $this->mapelModel->update($id, [
            'kode_mapel' => $this->request->getPost('kode_mapel'),
            'nama_mapel' => $nama_mapel,
            'kelompok' => $this->request->getPost('kelompok'),
        ]);

        log_activity('Mengubah Mata Pelajaran', 'Akademik', 'Nama Mapel: ' . $nama_mapel);

        return redirect()->to(base_url('akademik/mapel'))->with('success', 'Mata pelajaran berhasil diperbarui');
    }

    public function delete($id)
    {
        helper('activity');
        $mapel = $this->mapelModel->find($id);
        if ($mapel) {
            $this->mapelModel->delete($id);
            log_activity('Menghapus Mata Pelajaran', 'Akademik', 'Nama Mapel: ' . ($mapel['nama_mapel'] ?? ''));
        }
        return redirect()->to(base_url('akademik/mapel'))->with('success', 'Mata pelajaran berhasil dihapus');
    }
}
