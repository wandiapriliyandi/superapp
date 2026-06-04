<?php

namespace Spp\Controllers;

use App\Controllers\BaseController;
use Spp\Models\SppTarifModel;
use App\Traits\Exportable;

class Tarif extends BaseController
{
    use Exportable;
    protected $tarifModel;

    public function __construct()
    {
        $this->tarifModel = new SppTarifModel();
    }

    public function index()
    {
        $id_tahun = $this->request->getGet('id_tahun_akademik');
        
        $query = $this->tarifModel
                      ->select('spp_tarif.*, tahun_akademik.nama_tahun')
                      ->join('tahun_akademik', 'tahun_akademik.id = spp_tarif.id_tahun_akademik', 'left');
        
        if ($id_tahun) {
            $query->where('spp_tarif.id_tahun_akademik', $id_tahun);
        }

        $taModel = new \App\Models\TahunAkademikModel();

        $data = [
            'title' => 'Tarif SPP',
            'tarif' => $query->orderBy('tahun_akademik.nama_tahun', 'DESC')->findAll(),
            'ta'    => $taModel->orderBy('nama_tahun', 'DESC')->findAll(),
            'filter'=> ['id_tahun_akademik' => $id_tahun]
        ];
        return view('Spp\Views\tarif\index', $data);
    }

    public function add()
    {
        $taModel = new \App\Models\TahunAkademikModel();
        $data = [
            'title' => 'Tambah Tarif SPP',
            'ta'    => $taModel->orderBy('nama_tahun', 'DESC')->findAll()
        ];
        return view('Spp\Views\tarif\add', $data);
    }

    public function edit($id)
    {
        $taModel = new \App\Models\TahunAkademikModel();
        $data = [
            'title' => 'Edit Tarif SPP',
            'tarif' => $this->tarifModel->find($id),
            'ta'    => $taModel->orderBy('nama_tahun', 'DESC')->findAll()
        ];
        return view('Spp\Views\tarif\edit', $data);
    }

    public function save()
    {
        helper('activity');
        $data = $this->request->getPost();
        $this->tarifModel->save($data);
        log_activity('Menambah Tarif SPP', 'Spp', 'Nama Tarif: ' . ($data['nama_tarif'] ?? ''));
        return redirect()->to(base_url('spp/tarif'))->with('success', 'Tarif berhasil disimpan.');
    }

    public function update($id)
    {
        helper('activity');
        $data = $this->request->getPost();
        $this->tarifModel->update($id, $data);
        log_activity('Mengubah Tarif SPP', 'Spp', 'Nama Tarif: ' . ($data['nama_tarif'] ?? ''));
        return redirect()->to(base_url('spp/tarif'))->with('success', 'Tarif berhasil diperbarui.');
    }

    public function export($format)
    {
        $id_tahun = $this->request->getGet('id_tahun_akademik');
        
        $query = $this->tarifModel
                      ->select('spp_tarif.*, tahun_akademik.nama_tahun')
                      ->join('tahun_akademik', 'tahun_akademik.id = spp_tarif.id_tahun_akademik', 'left');
        
        if ($id_tahun) {
            $query->where('spp_tarif.id_tahun_akademik', $id_tahun);
        }

        $data = $query->orderBy('tahun_akademik.nama_tahun', 'DESC')->findAll();

        $data_export = [];
        foreach ($data as $row) {
            $data_export[] = [
                'Tahun Ajaran' => $row['nama_tahun'],
                'Nama Tarif'   => $row['nama_tarif'],
                'Tipe'         => $row['tipe'],
                'Nominal'      => (float)$row['nominal'],
                'Keterangan'   => $row['keterangan']
            ];
        }

        if ($format == 'excel') {
            return $this->exportToExcel($data_export, "Daftar_Tarif_SPP_" . date('Ymd'));
        } elseif ($format == 'word') {
            return $this->exportToWord($data_export, "Daftar Tarif SPP", "Daftar_Tarif_SPP_" . date('Ymd'));
        } elseif ($format == 'pdf') {
            return $this->exportToPdf($data_export, "Daftar Tarif SPP");
        }
    }

    public function delete($id)
    {
        helper('activity');
        $tarif = $this->tarifModel->find($id);
        if ($tarif) {
            $this->tarifModel->delete($id);
            log_activity('Menghapus Tarif SPP', 'Spp', 'Nama Tarif: ' . ($tarif['nama_tarif'] ?? ''));
        }
        return redirect()->to(base_url('spp/tarif'))->with('success', 'Tarif berhasil dihapus.');
    }
}
