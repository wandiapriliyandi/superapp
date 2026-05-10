<?php

namespace Akademik\Controllers;

use App\Controllers\BaseController;
use App\Models\SantriModel;
use CodeIgniter\API\ResponseTrait;

use App\Traits\Exportable;

use App\Traits\Indexable;
use App\Traits\Selectable;

class Santri extends BaseController
{
    use ResponseTrait, Exportable, Indexable, Selectable;

    protected $santriModel;

    public function __construct()
    {
        $this->santriModel = new SantriModel();
    }

    /**
     * Endpoint untuk Select2 Autocomplete
     */
    public function selection()
    {
        return $this->renderSelection($this->santriModel, 'nama_lengkap', [], 'id');
    }

    public function show($id)
    {
        $santri = $this->santriModel->find($id);
        if (!$santri) return redirect()->back()->with('error', 'Data santri tidak ditemukan.');

        log_activity('Melihat Profil Santri', 'Akademik', 'Nama: ' . $santri['nama_lengkap']);

        return view('Akademik\Views\santri\show', [
            'title'  => 'Profil Santri',
            'santri' => $santri
        ]);
    }

    public function index()
    {
        return $this->renderIndex(
            $this->santriModel, 
            ['jenis_kelamin', 'status_santri'], 
            'Akademik\Views\santri\index', 
            'Daftar Santri',
            'santri'
        );
    }

    public function export_excel()
    {
        $data = $this->prepare_export_data();
        log_activity('Export Excel Santri', 'Akademik');
        return $this->exportToExcel($data, 'Laporan-Data-Santri-' . date('Ymd'));
    }

    public function export_word()
    {
        $data = $this->prepare_export_data();
        log_activity('Export Word Santri', 'Akademik');
        return $this->exportToWord($data, 'Laporan Data Santri', 'Laporan-Data-Santri-' . date('Ymd'));
    }

    public function export_pdf()
    {
        $data = $this->prepare_export_data();
        log_activity('Cetak PDF Santri', 'Akademik');
        return $this->exportToPdf($data, 'Laporan Data Santri');
    }

    public function add()
    {
        return view('Akademik\Views\santri\add', [
            'title' => 'Tambah Santri Baru'
        ]);
    }

    public function save()
    {
        $data = $this->request->getPost();
        
        // Handle Upload Foto
        $file = $this->request->getFile('foto');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/santri', $newName);
            $data['foto'] = $newName;
        }

        $this->santriModel->save($data);
        log_activity('Menambah Santri Baru', 'Akademik', 'Nama: ' . $data['nama_lengkap']);

        return redirect()->to(base_url('akademik/santri'))->with('success', 'Data santri berhasil disimpan.');
    }

    private function prepare_export_data()
    {
        $query = $this->santriModel;
        if ($jk = $this->request->getGet('jk')) $query->where('jenis_kelamin', $jk);
        if ($status = $this->request->getGet('status')) $query->where('status_santri', $status);
        
        $santri = $query->findAll();
        $export = [];
        foreach ($santri as $s) {
            $export[] = [
                'NISN'          => $s['nisn'],
                'Nama Lengkap'  => $s['nama_lengkap'],
                'JK'            => $s['jenis_kelamin'],
                'Tempat Lahir'  => $s['tempat_lahir'],
                'Status'        => $s['status_santri']
            ];
        }
        return $export;
    }

    public function delete($id)
    {
        $santri = $this->santriModel->find($id);
        if ($santri) {
            $this->santriModel->delete($id);
            log_activity('Menghapus Data Santri', 'Akademik', 'Nama: ' . $santri['nama_lengkap']);
            return redirect()->to(base_url('akademik/santri'))->with('success', 'Data santri berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'Data tidak ditemukan.');
    }
}
