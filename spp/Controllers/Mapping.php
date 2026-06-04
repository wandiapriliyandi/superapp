<?php

namespace Spp\Controllers;

use App\Controllers\BaseController;
use Spp\Models\SppSantriTarifModel;
use Spp\Models\SppTarifModel;
use App\Models\SantriModel;
use App\Traits\Exportable;

class Mapping extends BaseController
{
    use Exportable;
    protected $mappingModel;
    protected $santriModel;
    protected $tarifModel;

    public function __construct()
    {
        $this->mappingModel = new SppSantriTarifModel();
        $this->santriModel  = new SantriModel();
        $this->tarifModel   = new SppTarifModel();
    }

    public function index()
    {
        $q = $this->request->getGet('q');
        
        $query = $this->santriModel->where('status_santri', 'Aktif');
        
        if ($q) {
            $query->groupStart()
                  ->like('nama_lengkap', $q)
                  ->orLike('nisn', $q)
                  ->groupEnd();
        }

        $data = [
            'title'  => 'Pemetaan Tarif (Kesepakatan)',
            'santri' => $query->findAll(),
            'filter' => ['q' => $q]
        ];
        return view('Spp\Views\mapping\index', $data);
    }

    public function export($format)
    {
        $q = $this->request->getGet('q');
        
        $query = $this->santriModel->where('status_santri', 'Aktif');
        
        if ($q) {
            $query->groupStart()
                  ->like('nama_lengkap', $q)
                  ->orLike('nisn', $q)
                  ->groupEnd();
        }

        $santri = $query->findAll();

        $data_export = [];
        foreach ($santri as $s) {
            $data_export[] = [
                'Nama Lengkap'  => $s['nama_lengkap'],
                'NISN'          => $s['nisn'],
                'Jenis Kelamin' => $s['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'
            ];
        }

        if ($format == 'excel') {
            return $this->exportToExcel($data_export, "Pemetaan_Tarif_" . date('Ymd'));
        } elseif ($format == 'word') {
            return $this->exportToWord($data_export, "Daftar Pemetaan Tarif SPP", "Pemetaan_Tarif_" . date('Ymd'));
        } elseif ($format == 'pdf') {
            return $this->exportToPdf($data_export, "Daftar Pemetaan Tarif SPP");
        }
    }

    public function santri($id)
    {
        $santri = $this->santriModel->find($id);
        if (!$santri) return redirect()->to(base_url('spp/mapping'))->with('error', 'Santri tidak ditemukan.');

        $tarif = $this->tarifModel
                      ->select('spp_tarif.*, tahun_akademik.nama_tahun')
                      ->join('tahun_akademik', 'tahun_akademik.id = spp_tarif.id_tahun_akademik', 'left')
                      ->orderBy('tahun_akademik.nama_tahun', 'DESC')
                      ->orderBy('nominal', 'DESC')
                      ->findAll();

        $currentMapping = $this->mappingModel->where('nisn', $santri['nisn'])->findAll();
        $mappedData = [];
        foreach ($currentMapping as $cm) {
            $mappedData[$cm['tarif_id']] = $cm;
        }

        $data = [
            'title'   => 'Atur Tarif: ' . $santri['nama_lengkap'],
            'santri'  => $santri,
            'tarif'   => $tarif,
            'current' => array_keys($mappedData),
            'mappedData' => $mappedData
        ];
        return view('Spp\Views\mapping\santri', $data);
    }

    public function save()
    {
        helper('activity');
        $nisn = $this->request->getPost('nisn');
        $tarif_ids = $this->request->getPost('tarif_ids') ?? [];
        $diskon    = $this->request->getPost('nominal_diskon') ?? [];
        $ket       = $this->request->getPost('keterangan_diskon') ?? [];

        // Hapus mapping lama
        $this->mappingModel->where('nisn', $nisn)->delete();

        // Simpan mapping baru
        foreach ($tarif_ids as $tid) {
            $this->mappingModel->insert([
                'nisn'              => $nisn,
                'tarif_id'          => $tid,
                'nominal_diskon'    => $diskon[$tid] ?? 0,
                'keterangan_diskon' => $ket[$tid] ?? null,
                'created_at'        => date('Y-m-d H:i:s')
            ]);
        }

        $santri = $this->santriModel->where('nisn', $nisn)->first();
        log_activity('Mengubah Pemetaan Tarif Santri', 'Spp', 'Santri: ' . ($santri['nama_lengkap'] ?? '') . ' (NISN: ' . $nisn . '), Total Tarif: ' . count($tarif_ids));
        return redirect()->to(base_url('spp/mapping/santri/' . $santri['id']))->with('success', 'Kesepakatan bayaran berhasil diperbarui.');
    }

    public function print($id)
    {
        $santri = $this->santriModel->find($id);
        if (!$santri) die('Santri tidak ditemukan.');

        $mapping = $this->mappingModel->getMappingWithDetails($santri['nisn']);
        
        $db = \Config\Database::connect();
        $setting = $db->table('app_settings')->get()->getRowArray();

        $data = [
            'santri'  => $santri,
            'mapping' => $mapping,
            'setting' => $setting
        ];
        return view('Spp\Views\mapping\print', $data);
    }

    public function exportWord($id)
    {
        $santri = $this->santriModel->find($id);
        if (!$santri) die('Santri tidak ditemukan.');

        $mapping = $this->mappingModel->getMappingWithDetails($santri['nisn']);
        
        $db = \Config\Database::connect();
        $setting = $db->table('app_settings')->get()->getRowArray();

        $filename = "Kesepakatan_" . str_replace(' ', '_', $santri['nama_lengkap']) . ".doc";

        header("Content-Type: application/msword");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $data = [
            'santri'  => $santri,
            'mapping' => $mapping,
            'setting' => $setting
        ];
        
        return view('Spp\Views\mapping\export_word', $data);
    }
}
