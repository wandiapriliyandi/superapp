<?php

namespace Poskestren\Controllers;

use App\Controllers\BaseController;
use Poskestren\Models\ObatModel;

class Obat extends BaseController
{
    protected $obatModel;

    public function __construct()
    {
        $this->obatModel = new ObatModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen Obat',
            'obat' => $this->obatModel->findAll()
        ];
        return view('Poskestren\Views\obat\index', $data);
    }

    public function tambah()
    {
        $data = [
            'title' => 'Tambah Stok Obat'
        ];
        return view('Poskestren\Views\obat\form', $data);
    }

    public function simpan()
    {
        $this->obatModel->save([
            'nama_obat' => $this->request->getPost('nama_obat'),
            'satuan'    => $this->request->getPost('satuan'),
            'stok'      => $this->request->getPost('stok'),
            'deskripsi' => $this->request->getPost('deskripsi'),
        ]);

        return redirect()->to(base_url('poskestren/obat'))->with('success', 'Data obat berhasil disimpan.');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Data Obat',
            'obat' => $this->obatModel->find($id)
        ];
        return view('Poskestren\Views\obat\form', $data);
    }

    public function update($id)
    {
        $this->obatModel->update($id, [
            'nama_obat' => $this->request->getPost('nama_obat'),
            'satuan'    => $this->request->getPost('satuan'),
            'stok'      => $this->request->getPost('stok'),
            'deskripsi' => $this->request->getPost('deskripsi'),
        ]);

        return redirect()->to(base_url('poskestren/obat'))->with('success', 'Data obat berhasil diupdate.');
    }

    public function hapus($id)
    {
        $this->obatModel->delete($id);
        return redirect()->to(base_url('poskestren/obat'))->with('success', 'Data obat berhasil dihapus.');
    }

    public function get_obat()
    {
        $term = $this->request->getGet('term');
        $obat = $this->obatModel->like('nama_obat', $term)->where('stok >', 0)->limit(10)->find();
        return $this->response->setJSON($obat);
    }
}
