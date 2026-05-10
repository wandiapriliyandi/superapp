<?php

namespace Ppdb\Controllers;

use App\Controllers\BaseController;
use Ppdb\Models\PendaftarModel;
use Ppdb\Models\PengaturanModel;
use App\Models\TahunAjaranModel;

class PublicPendaftaran extends BaseController
{
    protected $pendaftarModel;
    protected $pengaturanModel;

    public function __construct()
    {
        $this->pendaftarModel = new PendaftarModel();
        $this->pengaturanModel = new PengaturanModel();
    }

    public function index()
    {
        $activeWave = $this->pengaturanModel->getActive();
        
        if (!$activeWave) {
            return view('Ppdb\Views\public\closed', ['title' => 'Pendaftaran Ditutup']);
        }

        return view('Ppdb\Views\public\form', [
            'title' => 'Form Pendaftaran Santri Baru',
            'wave'  => $activeWave
        ]);
    }

    public function submit()
    {
        $taModel = new TahunAjaranModel();
        $activeTA = $taModel->where('status', 'Aktif')->first();

        $data = [
            'nomor_pendaftaran' => $this->pendaftarModel->generateNomor(),
            'nama_lengkap'      => $this->request->getPost('nama_lengkap'),
            'jenis_kelamin'     => $this->request->getPost('jenis_kelamin'),
            'tempat_lahir'      => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir'     => $this->request->getPost('tanggal_lahir'),
            'alamat'            => $this->request->getPost('alamat'),
            'no_hp_ortu'        => $this->request->getPost('no_hp_ortu'),
            'status_seleksi'    => 'Pending',
            'id_tahun_ajaran'   => $activeTA ? $activeTA['id'] : 0,
        ];

        if ($this->pendaftarModel->insert($data)) {
            return redirect()->to('ppdb/daftar/success/' . $data['nomor_pendaftaran']);
        }

        return redirect()->back()->withInput()->with('error', 'Gagal mengirim pendaftaran.');
    }

    public function success($nomor)
    {
        $pendaftar = $this->pendaftarModel->where('nomor_pendaftaran', $nomor)->first();
        if (!$pendaftar) return redirect()->to('ppdb/daftar');

        return view('Ppdb\Views\public\success', [
            'title' => 'Pendaftaran Berhasil',
            'pendaftar' => $pendaftar
        ]);
    }
}
