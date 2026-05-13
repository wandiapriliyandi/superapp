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
            'tarif' => $this->tarifModel->findAll()
        ];
        return view('Keuangan\Views\tarif\index', $data);
    }

    public function add()
    {
        $data = ['title' => 'Tambah Tarif SPP'];
        return view('Keuangan\Views\tarif\add', $data);
    }

    public function save()
    {
        $this->tarifModel->save($this->request->getPost());
        return redirect()->to(base_url('keuangan/tarif'))->with('success', 'Tarif berhasil disimpan.');
    }

    public function delete($id)
    {
        $this->tarifModel->delete($id);
        return redirect()->to(base_url('keuangan/tarif'))->with('success', 'Tarif berhasil dihapus.');
    }
}
