<?php

namespace ELearning\Controllers;

use App\Controllers\BaseController;
use ELearning\Models\MateriModel;
use App\Traits\Indexable;
use App\Traits\Exportable;
use CodeIgniter\API\ResponseTrait;

class Materi extends BaseController
{
    use ResponseTrait, Indexable, Exportable;

    protected $materiModel;

    public function __construct()
    {
        $this->materiModel = new MateriModel();
    }

    public function index()
    {
        return $this->renderIndex(
            $this->materiModel,
            ['mata_pelajaran', 'kelas'],
            'ELearning\Views\materi\index',
            '📚 Materi Pembelajaran',
            'materi',
            ['module' => 'E-Learning']
        );
    }

    public function add()
    {
        return view('ELearning\Views\materi\add', [
            'title' => 'Tambah Materi Baru'
        ]);
    }

    public function save()
    {
        $data = $this->request->getPost();

        // Handle Upload File Materi (PDF/Doc/PPT)
        $file = $this->request->getFile('file_materi');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/materi', $newName);
            $data['file_materi'] = $newName;
        }

        $this->materiModel->save($data);

        log_activity('Menambah Materi Pembelajaran', 'E-Learning', 'Judul: ' . $data['judul']);

        return redirect()->to(base_url('e-learning/materi'))->with('success', 'Materi pembelajaran berhasil diunggah dan disimpan!');
    }

    public function show($id)
    {
        $data = $this->materiModel->find($id);
        if (!$data) return redirect()->back()->with('error', 'Materi tidak ditemukan.');

        log_activity('Melihat Detail Materi Pembelajaran', 'E-Learning', 'Judul: ' . $data['judul']);

        return view('ELearning\Views\materi\show', [
            'title'  => 'Detail Materi Belajar',
            'materi' => $data
        ]);
    }

    public function export_excel()
    {
        $data = $this->prepare_export_data();
        log_activity('Export Excel Materi Belajar', 'E-Learning');
        return $this->exportToExcel($data, 'Materi-Pembelajaran-' . date('Ymd'));
    }

    public function export_word()
    {
        $data = $this->prepare_export_data();
        log_activity('Export Word Materi Belajar', 'E-Learning');
        return $this->exportToWord($data, 'Daftar Materi Pembelajaran', 'Materi-Pembelajaran-' . date('Ymd'));
    }

    public function export_pdf()
    {
        $data = $this->prepare_export_data();
        log_activity('Cetak PDF Materi Belajar', 'E-Learning');
        return $this->exportToPdf($data, 'Daftar Materi Pembelajaran');
    }

    private function prepare_export_data()
    {
        $query = $this->materiModel;
        if ($mapel = $this->request->getGet('mata_pelajaran')) $query->where('mata_pelajaran', $mapel);
        if ($kelas = $this->request->getGet('kelas')) $query->where('kelas', $kelas);

        $rows = $query->orderBy('id', 'DESC')->findAll();
        $export = [];
        foreach ($rows as $r) {
            $export[] = [
                'Judul Materi'   => $r['judul'],
                'Mata Pelajaran' => $r['mata_pelajaran'],
                'Kelas'          => $r['kelas'],
                'Guru Pengampu'  => $r['guru_pengampu'],
                'Link Video'     => $r['link_video'] ? $r['link_video'] : '-',
                'File Materi'    => $r['file_materi'] ? $r['file_materi'] : '-',
                'Tanggal Upload' => $r['created_at']
            ];
        }
        return $export;
    }

    public function delete($id)
    {
        $data = $this->materiModel->find($id);
        if ($data) {
            $this->materiModel->delete($id);
            log_activity('Menghapus Materi Pembelajaran', 'E-Learning', 'Judul: ' . $data['judul']);
            return redirect()->back()->with('success', 'Materi berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'Data tidak ditemukan.');
    }
}
