<?php

namespace Akademik\Controllers;

use App\Controllers\BaseController;
use App\Models\TahunAjaranModel;

use App\Traits\Exportable;
use App\Traits\Indexable;

class TahunAjaran extends BaseController
{
    use Exportable, Indexable;
    protected $taModel;

    public function __construct()
    {
        $this->taModel = new TahunAjaranModel();
    }

    public function index()
    {
        return $this->renderIndex(
            $this->taModel,
            ['semester', 'status'],
            'Akademik\Views\tahun_ajaran\index',
            'Master Tahun Ajaran',
            'ta'
        );
    }

    public function export_excel()
    {
        $data = $this->prepare_export_data();
        log_activity('Export Excel Tahun Ajaran', 'Akademik');
        return $this->exportToExcel($data, 'Master-Tahun-Ajaran-' . date('Ymd'));
    }

    public function export_word()
    {
        $data = $this->prepare_export_data();
        log_activity('Export Word Tahun Ajaran', 'Akademik');
        return $this->exportToWord($data, 'Master Tahun Ajaran', 'Master-Tahun-Ajaran-' . date('Ymd'));
    }

    public function export_pdf()
    {
        $data = $this->prepare_export_data();
        log_activity('Cetak PDF Tahun Ajaran', 'Akademik');
        return $this->exportToPdf($data, 'Master Tahun Ajaran');
    }

    private function prepare_export_data()
    {
        $query = $this->taModel;
        if ($semester = $this->request->getGet('semester')) $query->where('semester', $semester);
        if ($status = $this->request->getGet('status')) $query->where('status', $status);
        
        $ta = $query->orderBy('tahun_ajaran', 'DESC')->findAll();
        $export = [];
        foreach ($ta as $item) {
            $export[] = [
                'Tahun Ajaran' => $item['tahun_ajaran'],
                'Semester'     => $item['semester'],
                'Tgl Mulai'    => $item['tgl_mulai'],
                'Tgl Selesai'  => $item['tgl_selesai'],
                'Status'       => $item['status']
            ];
        }
        return $export;
    }

    public function set_active($id)
    {
        $this->taModel->setActive($id);
        return redirect()->back()->with('success', 'Tahun Ajaran Aktif berhasil diubah!');
    }

    public function add()
    {
        $data = ['title' => 'Tambah Tahun Ajaran'];
        return view('Akademik\Views\tahun_ajaran\add', $data);
    }

    public function save()
    {
        $this->taModel->save([
            'tahun_ajaran' => $this->request->getPost('tahun_ajaran'),
            'semester'     => $this->request->getPost('semester'),
            'tgl_mulai'    => $this->request->getPost('tgl_mulai'),
            'tgl_selesai'  => $this->request->getPost('tgl_selesai'),
            'status'       => 'Tidak Aktif', // Default tidak aktif saat baru dibuat
        ]);

        return redirect()->to(base_url('akademik/tahun-ajaran'))->with('success', 'Tahun Ajaran baru berhasil ditambah!');
    }

    public function delete($id)
    {
        $ta = $this->taModel->find($id);
        if ($ta) {
            $this->taModel->delete($id);
            log_activity('Menghapus Master Tahun Ajaran', 'Akademik', 'Tahun: ' . $ta['tahun_ajaran']);
            return redirect()->to(base_url('akademik/tahun-ajaran'))->with('success', 'Tahun Ajaran berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'Data tidak ditemukan.');
    }
}
