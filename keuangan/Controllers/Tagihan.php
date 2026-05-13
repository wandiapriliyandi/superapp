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

    public function generateSantri($santri_id)
    {
        helper('activity');
        $mappingModel = new \Keuangan\Models\SppSantriTarifModel();
        $mappings = $mappingModel->select('spp_santri_tarif.*, spp_tarif.nominal, spp_tarif.tipe')
                                 ->join('spp_tarif', 'spp_tarif.id = spp_santri_tarif.tarif_id')
                                 ->where('santri_id', $santri_id)
                                 ->findAll();

        if (empty($mappings)) return redirect()->back()->with('error', 'Santri ini belum memiliki pemetaan tarif.');

        $bulan = date('n');
        $tahun = date('Y');
        $count = 0;

        foreach ($mappings as $m) {
            if ($this->createTagihanIfNotExist($santri_id, $m['tarif_id'], $bulan, $tahun, $m['nominal'], $m['tipe'])) {
                $count++;
            }
        }

        log_activity('Generate Tagihan Per Orang', 'Keuangan', 'Santri ID: ' . $santri_id . ', Total: ' . $count);
        return redirect()->back()->with('success', $count . ' Tagihan berhasil digenerate untuk bulan ini.');
    }

    public function processGenerate()
    {
        $mode = $this->request->getPost('mode');
        
        if ($mode == 'yearly') {
            return $this->generateTahunan();
        }

        helper('activity');
        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');
        $mode  = $this->request->getPost('mode') ?? 'single';
        $tarif_id = $this->request->getPost('tarif_id');

        $count = 0;
        $santriModel = new SantriModel();
        
        if ($mode == 'single') {
            $tarifModel = new SppTarifModel();
            $tarif = $tarifModel->find($tarif_id);
            if (!$tarif) return redirect()->back()->with('error', 'Pilih tarif yang valid.');

            $santri = $santriModel->where('status_santri', 'Aktif')->findAll();
            foreach ($santri as $s) {
                if ($this->createTagihanIfNotExist($s['id'], $tarif_id, $bulan, $tahun, $tarif['nominal'], $tarif['tipe'])) {
                    $count++;
                }
            }
        } else {
            // Mode Mapping: Ambil semua kesepakatan bayaran
            $mappingModel = new \Keuangan\Models\SppSantriTarifModel();
            $mappings = $mappingModel->select('spp_santri_tarif.*, spp_tarif.nominal, spp_tarif.tipe')
                                     ->join('spp_tarif', 'spp_tarif.id = spp_santri_tarif.tarif_id')
                                     ->findAll();
            
            foreach ($mappings as $m) {
                // Pastikan santri masih aktif
                $s = $santriModel->find($m['santri_id']);
                if ($s && $s['status_santri'] == 'Aktif') {
                    if ($this->createTagihanIfNotExist($m['santri_id'], $m['tarif_id'], $bulan, $tahun, $m['nominal'], $m['tipe'])) {
                        $count++;
                    }
                }
            }
        }

        log_activity('Generate Tagihan SPP Massal', 'Keuangan', 'Mode: ' . $mode . ', Bulan: ' . $bulan . ', Total: ' . $count);
        return redirect()->to(base_url('keuangan/tagihan'))->with('success', $count . ' Tagihan berhasil digenerate.');
    }

    public function generateTahunan()
    {
        $ta_id = $this->request->getPost('id_tahun_akademik');
        if (!$ta_id) return redirect()->back()->with('error', 'Pilih Tahun Akademik terlebih dahulu.');

        $taModel = new \App\Models\TahunAkademikModel();
        $ta = $taModel->find($ta_id);
        if (!$ta) return redirect()->back()->with('error', 'Tahun Akademik tidak ditemukan.');

        // Pecah "2024/2025" menjadi 2024 dan 2025
        $years = explode('/', $ta['nama_tahun']);
        if (count($years) != 2) return redirect()->back()->with('error', 'Format Nama Tahun harus "YYYY/YYYY" (contoh: 2024/2025)');

        $startYear = trim($years[0]);
        $endYear   = trim($years[1]);

        $mappingModel = new \Keuangan\Models\SppSantriTarifModel();
        // Ambil semua pemetaan yang masuk dalam tahun akademik ini
        $allMappings = $mappingModel->select('spp_santri_tarif.*, spp_tarif.nominal, spp_tarif.tipe')
                                    ->join('spp_tarif', 'spp_tarif.id = spp_santri_tarif.tarif_id')
                                    ->where('spp_tarif.id_tahun_akademik', $ta_id)
                                    ->findAll();

        $count = 0;
        foreach ($allMappings as $m) {
            if ($m['tipe'] == 'Bulanan') {
                // Generate 12 bulan (Juli thn_awal s/d Juni thn_akhir)
                // Juli (7) s/d Desember (12)
                for ($bln = 7; $bln <= 12; $bln++) {
                    if ($this->createTagihanIfNotExist($m['santri_id'], $m['tarif_id'], $bln, $startYear, $m['nominal'], 'Bulanan')) {
                        $count++;
                    }
                }
                // Januari (1) s/d Juni (6)
                for ($bln = 1; $bln <= 6; $bln++) {
                    if ($this->createTagihanIfNotExist($m['santri_id'], $m['tarif_id'], $bln, $endYear, $m['nominal'], 'Bulanan')) {
                        $count++;
                    }
                }
            } else {
                // Jika tahunan, cukup 1 kali (bulan 0)
                if ($this->createTagihanIfNotExist($m['santri_id'], $m['tarif_id'], 0, $startYear, $m['nominal'], 'Tahunan')) {
                    $count++;
                }
            }
        }

        return redirect()->to(base_url('keuangan/tagihan'))->with('success', "Berhasil men-generate $count tagihan untuk satu tahun ajaran.");
    }

    private function createTagihanIfNotExist($santri_id, $tarif_id, $bulan, $tahun, $nominal, $tipe = 'Bulanan')
    {
        $query = $this->tagihanModel->where([
            'santri_id' => $santri_id,
            'tarif_id'  => $tarif_id,
            'tahun'     => $tahun
        ]);

        // Jika bulanan, cek bulannya juga. Jika tahunan, cukup cek tahunnya saja.
        if ($tipe == 'Bulanan') {
            $query->where('bulan', $bulan);
        }

        $existing = $query->first();

        if (!$existing) {
            $this->tagihanModel->save([
                'santri_id'       => $santri_id,
                'tarif_id'        => $tarif_id,
                'bulan'           => ($tipe == 'Tahunan') ? 0 : $bulan, // 0 menandakan tagihan tahunan
                'tahun'           => $tahun,
                'nominal_tagihan' => $nominal,
                'total_terbayar'  => 0,
                'status'          => 'Belum Lunas'
            ]);
            return true;
        } else {
            // Jika sudah ada tapi BELUM LUNAS (dan belum ada cicilan), 
            // perbarui nominalnya mengikuti tarif terbaru (Sync)
            if ($existing['status'] == 'Belum Lunas' && $existing['total_terbayar'] == 0) {
                $this->tagihanModel->update($existing['id'], [
                    'nominal_tagihan' => $nominal
                ]);
                return true; // Anggap sebagai "berhasil diproses/update"
            }
        }
        return false;
    }

    public function edit($id)
    {
        $tagihan = $this->tagihanModel
                        ->select('spp_tagihan.*, santri.nama_lengkap as nama_santri, spp_tarif.nama_tarif')
                        ->join('santri', 'santri.id = spp_tagihan.santri_id')
                        ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id')
                        ->find($id);

        if (!$tagihan) return redirect()->to(base_url('keuangan/tagihan'))->with('error', 'Tagihan tidak ditemukan.');

        $data = [
            'title'   => 'Edit Tagihan',
            'tagihan' => $tagihan
        ];
        return view('Keuangan\Views\tagihan\edit', $data);
    }

    public function update($id)
    {
        $nominal = $this->request->getPost('nominal_tagihan');
        $tagihan = $this->tagihanModel->find($id);

        // Update status jika nominal berubah dan sudah ada pembayaran
        $status = 'Belum Lunas';
        if ($tagihan['total_terbayar'] > 0) {
            $status = ($tagihan['total_terbayar'] >= $nominal) ? 'Lunas' : 'Cicilan';
        }

        $this->tagihanModel->update($id, [
            'nominal_tagihan' => $nominal,
            'status'          => $status
        ]);

        return redirect()->to(base_url('keuangan/tagihan'))->with('success', 'Tagihan berhasil diperbarui.');
    }

    public function delete($id)
    {
        $this->tagihanModel->delete($id);
        return redirect()->to(base_url('keuangan/tagihan'))->with('success', 'Tagihan berhasil dihapus.');
    }
}
