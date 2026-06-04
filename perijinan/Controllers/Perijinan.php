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

        $tanggalMulai = $this->request->getPost('tanggal_mulai');
        $year = date('Y', strtotime($tanggalMulai));

        $nisnArray = $this->request->getPost('nisn');
        if (!is_array($nisnArray)) {
            $nisnArray = [$nisnArray];
        }

        // Generate SATU token untuk seluruh rombongan di formulir ini
        $token = $this->generateToken($year);

        foreach ($nisnArray as $nisn) {
            $data = [
                'nisn'            => $nisn,
                'token'           => $token,
                'jenis_izin'      => $this->request->getPost('jenis_izin'),
                'alasan'          => $this->request->getPost('alasan'),
                'tanggal_mulai'   => $this->request->getPost('tanggal_mulai'),
                'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
                'status'          => 'Pending'
            ];
            
            $this->perijinanModel->insert($data);
        }

        return redirect()->to(base_url('perijinan'))->with('success', 'Pengajuan perijinan berhasil dikirim.');
    }

    public function approve($ids)
    {
        $idArray = explode('-', $ids);
        foreach ($idArray as $id) {
            $this->perijinanModel->update($id, [
                'status'         => 'Disetujui',
                'disetujui_oleh' => session()->get('user_id'),
                'catatan_petugas' => 'Disetujui oleh admin pada ' . date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->back()->with('success', 'Perijinan telah disetujui.');
    }

    public function aktifkan($ids)
    {
        $idArray = explode('-', $ids);
        foreach ($idArray as $id) {
            $this->perijinanModel->update($id, [
                'status' => 'Aktif'
            ]);
        }

        return redirect()->back()->with('success', 'Status perizinan telah aktif (keluar).');
    }

    public function kembali($ids)
    {
        $idArray = explode('-', $ids);
        foreach ($idArray as $id) {
            $this->perijinanModel->update($id, [
                'status'        => 'Kembali',
                'waktu_kembali' => date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->back()->with('success', 'Konfirmasi santri kembali berhasil.');
    }

    public function reject($ids)
    {
        $idArray = explode('-', $ids);
        $catatan = $this->request->getPost('catatan');
        foreach ($idArray as $id) {
            $this->perijinanModel->update($id, [
                'status'         => 'Ditolak',
                'catatan_petugas' => $catatan
            ]);
        }

        return redirect()->back()->with('error', 'Perijinan telah ditolak.');
    }

    public function hapus($ids)
    {
        $idArray = explode('-', $ids);
        foreach ($idArray as $id) {
            $this->perijinanModel->delete($id);
        }

        return redirect()->back()->with('success', 'Data perijinan berhasil dihapus.');
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

    private function generateToken($year)
    {
        do {
            $random = rand(10000, 99999);
            $token = $year . $random;
            $exists = $this->perijinanModel->where('token', $token)->first();
        } while ($exists);
        
        return $token;
    }
}
