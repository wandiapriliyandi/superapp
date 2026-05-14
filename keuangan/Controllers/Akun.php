<?php

namespace Keuangan\Controllers;

use App\Controllers\BaseController;
use Keuangan\Models\AkunModel;

class Akun extends BaseController
{
    protected $akunModel;

    public function __construct()
    {
        $this->akunModel = new AkunModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Bagan Akun (Chart of Accounts)',
            'akun'  => $this->akunModel->orderBy('kode_akun', 'ASC')->findAll(),
        ];

        return view('Keuangan\Views\akun\index', $data);
    }

    public function add()
    {
        $data = [
            'title' => 'Tambah Akun Baru',
            'parent_akun' => $this->akunModel->where('parent_id', null)->findAll(),
        ];

        return view('Keuangan\Views\akun\add', $data);
    }

    public function save()
    {
        $rules = [
            'kode_akun' => 'required|is_unique[keu_akun.kode_akun]',
            'nama_akun' => 'required',
            'kategori'  => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->akunModel->save([
            'kode_akun'    => $this->request->getPost('kode_akun'),
            'nama_akun'    => $this->request->getPost('nama_akun'),
            'kategori'     => $this->request->getPost('kategori'),
            'parent_id'    => $this->request->getPost('parent_id') ?: null,
            'saldo_normal' => $this->request->getPost('saldo_normal'),
        ]);

        return redirect()->to(base_url('keuangan/akun'))->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Akun',
            'akun'  => $this->akunModel->find($id),
            'parent_akun' => $this->akunModel->where('parent_id', null)->where('id !=', $id)->findAll(),
        ];

        if (!$data['akun']) {
            return redirect()->to(base_url('keuangan/akun'))->with('error', 'Akun tidak ditemukan.');
        }

        return view('Keuangan\Views\akun\edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'kode_akun' => "required|is_unique[keu_akun.kode_akun,id,{$id}]",
            'nama_akun' => 'required',
            'kategori'  => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->akunModel->update($id, [
            'kode_akun'    => $this->request->getPost('kode_akun'),
            'nama_akun'    => $this->request->getPost('nama_akun'),
            'kategori'     => $this->request->getPost('kategori'),
            'parent_id'    => $this->request->getPost('parent_id') ?: null,
            'saldo_normal' => $this->request->getPost('saldo_normal'),
        ]);

        return redirect()->to(base_url('keuangan/akun'))->with('success', 'Akun berhasil diperbarui.');
    }

    public function delete($id)
    {
        $this->akunModel->delete($id);
        return redirect()->to(base_url('keuangan/akun'))->with('success', 'Akun berhasil dihapus.');
    }
}
