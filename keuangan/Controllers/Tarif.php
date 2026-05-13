<?php

namespace Keuangan\Controllers;

use App\Controllers\BaseController;
use Keuangan\Models\SppTarifModel;

class Tarif extends BaseController
{
    protected $tarifModel;

    public function __construct()
    {
        $this->tarifModel = new SppTarifModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Tarif SPP',
            'tarif' => $this->tarifModel
                            ->select('spp_tarif.*, tahun_akademik.nama_tahun')
                            ->join('tahun_akademik', 'tahun_akademik.id = spp_tarif.id_tahun_akademik', 'left')
                            ->orderBy('tahun_akademik.nama_tahun', 'DESC')
                            ->findAll()
        ];
        return view('Keuangan\Views\tarif\index', $data);
    }

    public function add()
    {
        $taModel = new \App\Models\TahunAkademikModel();
        $data = [
            'title' => 'Tambah Tarif SPP',
            'ta'    => $taModel->orderBy('nama_tahun', 'DESC')->findAll()
        ];
        return view('Keuangan\Views\tarif\add', $data);
    }

    public function edit($id)
    {
        $taModel = new \App\Models\TahunAkademikModel();
        $data = [
            'title' => 'Edit Tarif SPP',
            'tarif' => $this->tarifModel->find($id),
            'ta'    => $taModel->orderBy('nama_tahun', 'DESC')->findAll()
        ];
        return view('Keuangan\Views\tarif\edit', $data);
    }

    public function save()
    {
        $this->tarifModel->save($this->request->getPost());
        return redirect()->to(base_url('keuangan/tarif'))->with('success', 'Tarif berhasil disimpan.');
    }

    public function update($id)
    {
        $data = $this->request->getPost();
        $this->tarifModel->update($id, $data);
        return redirect()->to(base_url('keuangan/tarif'))->with('success', 'Tarif berhasil diperbarui.');
    }

    public function delete($id)
    {
        $this->tarifModel->delete($id);
        return redirect()->to(base_url('keuangan/tarif'))->with('success', 'Tarif berhasil dihapus.');
    }
}
