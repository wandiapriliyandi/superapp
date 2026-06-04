<?php

namespace Poskestren\Controllers;

use App\Controllers\BaseController;
use Poskestren\Models\ObatModel;
use Poskestren\Models\StokMutasiModel;

class Obat extends BaseController
{
    protected $obatModel;
    protected $stokMutasiModel;

    public function __construct()
    {
        $this->obatModel       = new ObatModel();
        $this->stokMutasiModel = new StokMutasiModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen Obat',
            'obat'  => $this->obatModel->findAll(),
        ];
        return view('Poskestren\Views\obat\index', $data);
    }

    public function tambah()
    {
        $data = [
            'title' => 'Daftarkan Obat Baru',
        ];
        return view('Poskestren\Views\obat\form', $data);
    }

    public function simpan()
    {
        $stokAwal = (int) $this->request->getPost('stok_awal');

        $id = $this->obatModel->insert([
            'nama_obat' => $this->request->getPost('nama_obat'),
            'satuan'    => $this->request->getPost('satuan'),
            'stok'      => 0,
            'deskripsi' => $this->request->getPost('deskripsi'),
        ]);

        if ($stokAwal > 0) {
            $this->stokMutasiModel->catat(
                (int) $id,
                $stokAwal,
                'masuk',
                'pengadaan',
                'Stok awal saat pendaftaran obat',
                null,
                null,
                session()->get('user_id')
            );
        }

        return redirect()->to(base_url('poskestren/obat'))->with('success', 'Obat berhasil didaftarkan.');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Data Obat',
            'obat'  => $this->obatModel->find($id),
        ];
        return view('Poskestren\Views\obat\form', $data);
    }

    public function update($id)
    {
        $this->obatModel->update($id, [
            'nama_obat' => $this->request->getPost('nama_obat'),
            'satuan'    => $this->request->getPost('satuan'),
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
