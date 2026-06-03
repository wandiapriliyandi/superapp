<?php

namespace Perijinan\Controllers;

use App\Controllers\BaseController;
use Perijinan\Models\PerijinanModel;
use App\Models\SantriModel;

class Perijinan extends BaseController
{
    protected $perijinanModel;
    protected $santriModel;

    public function __construct()
    {
        $this->perijinanModel = new PerijinanModel();
        $this->santriModel = new SantriModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Daftar Perijinan Santri',
            'perijinan' => $this->perijinanModel->getIzin()
        ];

        return view('Perijinan\Views\index', $data);
    }

    public function tambah()
    {
        $data = [
            'title' => 'Ajukan Perijinan Baru',
            'santri' => $this->santriModel->orderBy('nama_lengkap', 'ASC')->findAll(),
            'validation' => \Config\Services::validation()
        ];

        return view('Perijinan\Views\form', $data);
    }

    public function simpan()
    {
        if (!$this->validate([
            'nisn' => 'required',
            'jenis_izin' => 'required',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
        ])) {
            return redirect()->back()->withInput();
        }

        $data = [
            'nisn'            => $this->request->getPost('nisn'),
            'jenis_izin'      => $this->request->getPost('jenis_izin'),
            'alasan'          => $this->request->getPost('alasan'),
            'tanggal_mulai'   => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'status'          => 'Pending'
        ];

        $this->perijinanModel->save($data);

        return redirect()->to(base_url('perijinan'))->with('success', 'Pengajuan perijinan berhasil dikirim.');
    }

    public function approve($id)
    {
        $this->perijinanModel->update($id, [
            'status'         => 'Disetujui',
            'disetujui_oleh' => session()->get('user_id'),
            'catatan_petugas' => 'Disetujui oleh admin pada ' . date('Y-m-d H:i:s')
        ]);

        return redirect()->back()->with('success', 'Perijinan telah disetujui.');
    }

    public function aktifkan($id)
    {
        $this->perijinanModel->update($id, [
            'status' => 'Aktif'
        ]);

        return redirect()->back()->with('success', 'Santri telah keluar/mulai izin.');
    }

    public function kembali($id)
    {
        $this->perijinanModel->update($id, [
            'status'        => 'Kembali',
            'waktu_kembali' => date('Y-m-d H:i:s')
        ]);

        return redirect()->back()->with('success', 'Santri telah kembali ke pesantren.');
    }

    public function reject($id)
    {
        $catatan = $this->request->getPost('catatan');
        $this->perijinanModel->update($id, [
            'status'         => 'Ditolak',
            'catatan_petugas' => $catatan
        ]);

        return redirect()->back()->with('error', 'Perijinan telah ditolak.');
    }

    public function rekap()
    {
        $data = [
            'title' => 'Rekapitulasi Perijinan',
            'perijinan' => $this->perijinanModel->getIzin()
        ];
        return view('Perijinan\Views\rekap', $data);
    }

    public function pengaturan()
    {
        $data = [
            'title' => 'Pengaturan Modul Perijinan'
        ];
        return view('Perijinan\Views\pengaturan', $data);
    }
}
