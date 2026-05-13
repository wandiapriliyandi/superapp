<?php

namespace Keuangan\Controllers;

use App\Controllers\BaseController;
use Keuangan\Models\SppTagihanModel;
use Keuangan\Models\SppTarifModel;
use App\Models\SantriModel;

class Tagihan extends BaseController
{
    protected $tagihanModel;

    public function __construct()
    {
        $this->tagihanModel = new SppTagihanModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Tagihan SPP',
            'tagihan' => $this->tagihanModel->getTagihanWithSantri()
        ];
        return view('Keuangan\Views\tagihan\index', $data);
    }

    public function generate()
    {
        $tarifModel = new SppTarifModel();
        $data = [
            'title' => 'Generate Tagihan Massal',
            'tarif' => $tarifModel->findAll()
        ];
        return view('Keuangan\Views\tagihan\generate', $data);
    }

    public function processGenerate()
    {
        helper('activity');
        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');
        $tarif_id = $this->request->getPost('tarif_id');

        $tarifModel = new SppTarifModel();
        $tarif = $tarifModel->find($tarif_id);
        
        $santriModel = new SantriModel();
        $santri = $santriModel->where('status_santri', 'Aktif')->findAll();

        $count = 0;
        foreach ($santri as $s) {
            // Cek apakah sudah ada tagihan untuk bulan/tahun ini
            $existing = $this->tagihanModel->where([
                'santri_id' => $s['id'],
                'bulan'     => $bulan,
                'tahun'     => $tahun
            ])->first();

            if (!$existing) {
                $this->tagihanModel->save([
                    'santri_id'       => $s['id'],
                    'tarif_id'        => $tarif_id,
                    'bulan'           => $bulan,
                    'tahun'           => $tahun,
                    'nominal_tagihan' => $tarif['nominal'],
                    'total_terbayar'  => 0,
                    'status'          => 'Belum Lunas'
                ]);
                $count++;
            }
        }

        log_activity('Generate Tagihan SPP Massal', 'Keuangan', 'Bulan: ' . $bulan . ', Tahun: ' . $tahun . ', Total: ' . $count);

        return redirect()->to(base_url('keuangan/tagihan'))->with('success', $count . ' Tagihan berhasil digenerate.');
    }

    public function delete($id)
    {
        $this->tagihanModel->delete($id);
        return redirect()->to(base_url('keuangan/tagihan'))->with('success', 'Tagihan berhasil dihapus.');
    }
}
