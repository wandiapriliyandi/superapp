<?php

namespace ELearning\Controllers;

use App\Controllers\BaseController;
use ELearning\Models\UjianModel;
use App\Traits\Indexable;
use App\Traits\Exportable;
use CodeIgniter\API\ResponseTrait;

class Ujian extends BaseController
{
    use ResponseTrait, Indexable, Exportable;

    protected $ujianModel;

    public function __construct()
    {
        $this->ujianModel = new UjianModel();
    }

    public function index()
    {
        return $this->renderIndex(
            $this->ujianModel,
            ['mata_pelajaran', 'kelas', 'status'],
            'ELearning\Views\ujian\index',
            '📝 Ujian & Tugas Online',
            'ujian',
            ['module' => 'E-Learning']
        );
    }

    public function add()
    {
        return view('ELearning\Views\ujian\add', [
            'title' => 'Buat Ujian / Tugas Baru'
        ]);
    }

    public function save()
    {
        $data = $this->request->getPost();
        
        // Format tanggal jika kosong
        if (empty($data['tgl_mulai'])) {
            $data['tgl_mulai'] = date('Y-m-d H:i:s');
        }
        if (empty($data['tgl_selesai'])) {
            $data['tgl_selesai'] = date('Y-m-d H:i:s', strtotime('+7 days'));
        }

        $this->ujianModel->save($data);

        log_activity('Membuat Ujian Online Baru', 'E-Learning', 'Judul: ' . $data['judul']);

        return redirect()->to(base_url('e-learning/ujian'))->with('success', 'Jadwal ujian / tugas online berhasil dibuat!');
    }

    public function show($id)
    {
        $data = $this->ujianModel->find($id);
        if (!$data) return redirect()->back()->with('error', 'Ujian tidak ditemukan.');

        log_activity('Melihat Detail Ujian Online', 'E-Learning', 'Judul: ' . $data['judul']);

        return view('ELearning\Views\ujian\show', [
            'title' => 'Detail Ujian Online',
            'ujian' => $data
        ]);
    }

    public function export_excel()
    {
        $data = $this->prepare_export_data();
        log_activity('Export Excel Ujian Online', 'E-Learning');
        return $this->exportToExcel($data, 'Jadwal-Ujian-Online-' . date('Ymd'));
    }

    public function export_word()
    {
        $data = $this->prepare_export_data();
        log_activity('Export Word Ujian Online', 'E-Learning');
        return $this->exportToWord($data, 'Jadwal Ujian & Tugas Online', 'Jadwal-Ujian-Online-' . date('Ymd'));
    }

    public function export_pdf()
    {
        $data = $this->prepare_export_data();
        log_activity('Cetak PDF Ujian Online', 'E-Learning');
        return $this->exportToPdf($data, 'Jadwal Ujian & Tugas Online');
    }

    private function prepare_export_data()
    {
        $query = $this->ujianModel;
        if ($mapel = $this->request->getGet('mata_pelajaran')) $query->where('mata_pelajaran', $mapel);
        if ($kelas = $this->request->getGet('kelas')) $query->where('kelas', $kelas);
        if ($status = $this->request->getGet('status')) $query->where('status', $status);

        $rows = $query->orderBy('id', 'DESC')->findAll();
        $export = [];
        foreach ($rows as $r) {
            $export[] = [
                'Judul Ujian'    => $r['judul'],
                'Mata Pelajaran' => $r['mata_pelajaran'],
                'Kelas'          => $r['kelas'],
                'Durasi'         => $r['durasi_menit'] . ' Menit',
                'Tgl Mulai'      => $r['tgl_mulai'],
                'Tgl Selesai'    => $r['tgl_selesai'],
                'Status'         => $r['status']
            ];
        }
        return $export;
    }

    public function delete($id)
    {
        $data = $this->ujianModel->find($id);
        if ($data) {
            $this->ujianModel->delete($id);
            log_activity('Menghapus Ujian Online', 'E-Learning', 'Judul: ' . $data['judul']);
            return redirect()->back()->with('success', 'Ujian / tugas berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'Data tidak ditemukan.');
    }
}
