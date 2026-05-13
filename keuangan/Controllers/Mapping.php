<?php

namespace Keuangan\Controllers;

use App\Controllers\BaseController;
use Keuangan\Models\SppSantriTarifModel;
use Keuangan\Models\SppTarifModel;
use App\Models\SantriModel;

class Mapping extends BaseController
{
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
        $data = [
            'title' => 'Pemetaan Tarif (Kesepakatan)',
            'santri' => $this->santriModel->where('status_santri', 'Aktif')->findAll()
        ];
        return view('Keuangan\Views\mapping\index', $data);
    }

    public function santri($id)
    {
        $santri = $this->santriModel->find($id);
        if (!$santri) return redirect()->to(base_url('keuangan/mapping'))->with('error', 'Santri tidak ditemukan.');

        $tarif = $this->tarifModel
                      ->select('spp_tarif.*, tahun_akademik.nama_tahun')
                      ->join('tahun_akademik', 'tahun_akademik.id = spp_tarif.id_tahun_akademik', 'left')
                      ->orderBy('tahun_akademik.nama_tahun', 'DESC')
                      ->orderBy('nominal', 'DESC')
                      ->findAll();

        $data = [
            'title'   => 'Atur Tarif: ' . $santri['nama_lengkap'],
            'santri'  => $santri,
            'tarif'   => $tarif,
            'current' => $this->mappingModel->where('santri_id', $id)->findColumn('tarif_id') ?? []
        ];
        return view('Keuangan\Views\mapping\santri', $data);
    }

    public function save()
    {
        $santri_id = $this->request->getPost('santri_id');
        $tarif_ids = $this->request->getPost('tarif_ids') ?? [];

        // Hapus mapping lama
        $this->mappingModel->where('santri_id', $santri_id)->delete();

        // Simpan mapping baru
        foreach ($tarif_ids as $tid) {
            $this->mappingModel->insert([
                'santri_id' => $santri_id,
                'tarif_id'  => $tid,
                'created_at'=> date('Y-m-d H:i:s')
            ]);
        }

        return redirect()->to(base_url('keuangan/mapping/santri/' . $santri_id))->with('success', 'Kesepakatan bayaran berhasil diperbarui.');
    }

    public function print($id)
    {
        $santri = $this->santriModel->find($id);
        if (!$santri) die('Santri tidak ditemukan.');

        $mapping = $this->mappingModel->getMappingWithDetails($id);
        
        $db = \Config\Database::connect();
        $setting = $db->table('app_settings')->get()->getRowArray();

        $data = [
            'santri'  => $santri,
            'mapping' => $mapping,
            'setting' => $setting
        ];
        return view('Keuangan\Views\mapping\print', $data);
    }

    public function exportWord($id)
    {
        $santri = $this->santriModel->find($id);
        if (!$santri) die('Santri tidak ditemukan.');

        $mapping = $this->mappingModel->getMappingWithDetails($id);
        
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
        
        return view('Keuangan\Views\mapping\export_word', $data);
    }
}
