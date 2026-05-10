<?php

namespace App\Controllers;

use App\Models\KaryawanModel;
use App\Traits\Indexable;
use App\Traits\Exportable;
use App\Traits\Selectable;
use CodeIgniter\API\ResponseTrait;

class Karyawan extends BaseController
{
    use ResponseTrait, Indexable, Exportable, Selectable;

    protected $karyawanModel;

    public function __construct()
    {
        $this->karyawanModel = new KaryawanModel();
    }

    public function add()
    {
        return view('karyawan/add', [
            'title' => 'Tambah Pegawai Baru'
        ]);
    }

    public function save()
    {
        $data = $this->request->getPost();

        // Handle Upload Foto
        $file = $this->request->getFile('foto');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/karyawan', $newName);
            $data['foto'] = $newName;
        }

        $this->karyawanModel->save($data);

        log_activity('Menambah Pegawai Baru', 'Kepegawaian', 'Nama: ' . $data['nama_lengkap']);

        return redirect()->to(base_url('kepegawaian/karyawan'))->with('success', 'Data pegawai berhasil disimpan!');
    }

    public function index()
    {
        return $this->renderIndex(
            $this->karyawanModel,
            ['jabatan', 'status_pegawai', 'jenis_kelamin'],
            'karyawan/index',
            'Daftar Guru & Karyawan',
            'karyawan',
            ['module' => 'Kepegawaian']
        );
    }

    public function show($id)
    {
        $data = $this->karyawanModel->find($id);
        if (!$data) return redirect()->back()->with('error', 'Data tidak ditemukan.');

        log_activity('Melihat Profil Karyawan', 'Kepegawaian', 'Nama: ' . $data['nama_lengkap']);

        return view('karyawan/show', [
            'title' => 'Profil Pegawai',
            'k'     => $data
        ]);
    }

    public function selection()
    {
        return $this->renderSelection($this->karyawanModel, 'nama_lengkap', [], 'id');
    }

    public function export_excel()
    {
        $data = $this->prepare_export_data();
        log_activity('Export Excel Kepegawaian', 'Kepegawaian');
        return $this->exportToExcel($data, 'Data-Kepegawaian-' . date('Ymd'));
    }

    private function prepare_export_data()
    {
        $rows = $this->karyawanModel->findAll();
        $export = [];
        foreach ($rows as $r) {
            $export[] = [
                'NIP'        => $r['nip'],
                'NIK'        => $r['nik'],
                'Nama'       => $r['nama_lengkap'],
                'JK'         => $r['jenis_kelamin'],
                'Jabatan'    => $r['jabatan'],
                'Status'     => $r['status_pegawai'],
                'Pendidikan' => $r['pendidikan_terakhir'],
                'Tgl Masuk'  => $r['tanggal_masuk']
            ];
        }
        return $export;
    }

    public function delete($id)
    {
        $this->karyawanModel->delete($id);
        log_activity('Menghapus Data Karyawan', 'Kepegawaian');
        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }
}
