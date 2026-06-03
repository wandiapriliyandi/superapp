<?php

namespace Spp\Controllers;

use App\Controllers\BaseController;
use Spp\Models\SppPembayaranModel;
use Spp\Models\SppTagihanModel;
use Spp\Models\SppTarifModel;
use App\Models\SantriModel;
use App\Traits\Exportable;

class Tagihan extends BaseController
{
    use Exportable;
    protected $tagihanModel;

    public function __construct()
    {
        $this->tagihanModel = new SppTagihanModel();
    }

    public function index()
    {
        $nisn = $this->request->getGet('nisn');
        $status    = $this->request->getGet('status');
        $bulan     = $this->request->getGet('bulan');
        $q         = $this->request->getGet('q');

        $query = $this->tagihanModel->select('spp_tagihan.*, santri.nisn, santri.nama_lengkap as nama_santri, spp_tarif.nama_tarif')
                                    ->join('santri', 'santri.id = spp_tagihan.santri_id')
                                    ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id');

        if ($nisn)   $query->where('santri.nisn', $nisn);
        if ($status) $query->where('spp_tagihan.status', $status);
        if ($bulan)  $query->where('spp_tagihan.bulan', $bulan);
        if ($q)      $query->like('spp_tagihan.keterangan_diskon', $q);

        $tagihan = $query->orderBy('spp_tagihan.tahun', 'DESC')
                         ->orderBy('spp_tagihan.bulan', 'DESC')
                         ->findAll();

        $santriModel = new \App\Models\SantriModel();

        $data = [
            'title'   => 'Tagihan SPP',
            'tagihan' => $tagihan,
            'santri'  => $santriModel->where('status_santri', 'Aktif')->findAll(),
            'filter'  => [
                'nisn'   => $nisn,
                'status' => $status,
                'bulan'  => $bulan,
                'q'      => $q
            ]
        ];
        return view('Spp\Views\tagihan\index', $data);
    }

    public function generate()
    {
        $tarifModel = new SppTarifModel();
        $data = [
            'title' => 'Generate Tagihan Massal',
            'tarif' => $tarifModel->findAll()
        ];
        return view('Spp\Views\tagihan\generate', $data);
    }

    public function generateSantri($santri_id)
    {
        helper('activity');
        $santriModel = new \App\Models\SantriModel();
        $santri = $santriModel->find($santri_id);
        if (!$santri) return redirect()->back()->with('error', 'Santri tidak ditemukan.');

        $nisn = $santri['nisn'];
        if (!$nisn) return redirect()->back()->with('error', 'Santri tidak memiliki NISN.');

        $mappingModel = new \Spp\Models\SppSantriTarifModel();
        $mappings = $mappingModel->select('spp_santri_tarif.*, spp_tarif.nominal, spp_tarif.tipe')
                                 ->join('spp_tarif', 'spp_tarif.id = spp_santri_tarif.tarif_id')
                                 ->where('spp_santri_tarif.nisn', $nisn)
                                 ->findAll();

        if (empty($mappings)) return redirect()->back()->with('error', 'Santri ini belum memiliki pemetaan tarif.');

        $bulan = date('n');
        $tahun = date('Y');
        $count = 0;

        foreach ($mappings as $m) {
            if ($this->createTagihanIfNotExist($nisn, $m['tarif_id'], $bulan, $tahun, $m['nominal'], $m['tipe'], $m['nominal_diskon'], $m['keterangan_diskon'])) {
                $count++;
            }
        }

        log_activity('Generate Tagihan Per Orang', 'Spp', 'Santri NISN: ' . $nisn . ', Total: ' . $count);
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
                if ($s['nisn']) {
                    if ($this->createTagihanIfNotExist($s['nisn'], $tarif_id, $bulan, $tahun, $tarif['nominal'], $tarif['tipe'])) {
                        $count++;
                    }
                }
            }
        } else {
            // Mode Mapping: Ambil semua kesepakatan bayaran
            $mappingModel = new \Spp\Models\SppSantriTarifModel();
            $mappings = $mappingModel->select('spp_santri_tarif.*, spp_tarif.nominal, spp_tarif.tipe, santri.status_santri')
                                     ->join('santri', 'santri.nisn = spp_santri_tarif.nisn')
                                     ->join('spp_tarif', 'spp_tarif.id = spp_santri_tarif.tarif_id')
                                     ->findAll();
            
            foreach ($mappings as $m) {
                // Pastikan santri masih aktif
                if ($m['status_santri'] == 'Aktif') {
                    if ($this->createTagihanIfNotExist($m['nisn'], $m['tarif_id'], $bulan, $tahun, $m['nominal'], $m['tipe'], $m['nominal_diskon'], $m['keterangan_diskon'])) {
                        $count++;
                    }
                }
            }
        }

        log_activity('Generate Tagihan SPP Massal', 'Spp', 'Mode: ' . $mode . ', Bulan: ' . $bulan . ', Total: ' . $count);
        return redirect()->to(base_url('spp/tagihan'))->with('success', $count . ' Tagihan berhasil digenerate.');
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

        $mappingModel = new \Spp\Models\SppSantriTarifModel();
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
                    if ($this->createTagihanIfNotExist($m['nisn'], $m['tarif_id'], $bln, $startYear, $m['nominal'], 'Bulanan', $m['nominal_diskon'], $m['keterangan_diskon'])) {
                        $count++;
                    }
                }
                // Januari (1) s/d Juni (6)
                for ($bln = 1; $bln <= 6; $bln++) {
                    if ($this->createTagihanIfNotExist($m['nisn'], $m['tarif_id'], $bln, $endYear, $m['nominal'], 'Bulanan', $m['nominal_diskon'], $m['keterangan_diskon'])) {
                        $count++;
                    }
                }
            } else {
                // Jika tahunan, cukup 1 kali (bulan 0)
                if ($this->createTagihanIfNotExist($m['nisn'], $m['tarif_id'], 0, $startYear, $m['nominal'], 'Tahunan', $m['nominal_diskon'], $m['keterangan_diskon'])) {
                    $count++;
                }
            }
        }

        return redirect()->to(base_url('spp/tagihan'))->with('success', "Berhasil men-generate $count tagihan untuk satu tahun ajaran.");
    }

    private function createTagihanIfNotExist($nisn, $tarif_id, $bulan, $tahun, $nominal, $tipe = 'Bulanan', $diskon = 0, $ket_diskon = null)
    {
        $santriModel = new SantriModel();
        $santri = $santriModel->where('nisn', $nisn)->first();

        if (!$santri) {
            return false;
        }

        $query = $this->tagihanModel->where([
            'santri_id' => $santri['id'],
            'tarif_id'  => $tarif_id,
            'tahun'     => $tahun
        ]);

        // Jika bulanan, cek bulannya juga. Jika tahunan, cukup cek tahunnya saja.
        if ($tipe == 'Bulanan') {
            $query->where('bulan', $bulan);
        }

        $existing = $query->first();

        if (!$existing) {
            // Tentukan status awal: jika diskon mengcover seluruh nominal, langsung Lunas
            $status = (($nominal - $diskon) <= 0) ? 'Lunas' : 'Belum Lunas';

            $this->tagihanModel->save([
                'santri_id'       => $santri['id'],
                'tarif_id'        => $tarif_id,
                'bulan'           => ($tipe == 'Tahunan') ? 0 : $bulan, // 0 menandakan tagihan tahunan
                'tahun'           => $tahun,
                'nominal_tagihan' => $nominal,
                'diskon'          => $diskon,
                'keterangan_diskon' => $ket_diskon,
                'total_terbayar'  => 0,
                'status'          => $status
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
                        ->select('spp_tagihan.*, santri.nisn, santri.nama_lengkap as nama_santri, spp_tarif.nama_tarif')
                        ->join('santri', 'santri.id = spp_tagihan.santri_id')
                        ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id')
                        ->find($id);

        if (!$tagihan) return redirect()->to(base_url('spp/tagihan'))->with('error', 'Tagihan tidak ditemukan.');

        $data = [
            'title'   => 'Edit Tagihan',
            'tagihan' => $tagihan
        ];
        return view('Spp\Views\tagihan\edit', $data);
    }

    public function update($id)
    {
        $nominal = $this->request->getPost('nominal_tagihan');
        $diskon  = $this->request->getPost('diskon') ?? 0;
        $tagihan = $this->tagihanModel->find($id);

        $tagihan_net = $nominal - $diskon;

        // Update status
        $status = 'Belum Lunas';
        if ($tagihan['total_terbayar'] > 0) {
            $status = ($tagihan['total_terbayar'] >= $tagihan_net) ? 'Lunas' : 'Cicilan';
        } elseif ($tagihan_net <= 0) {
            $status = 'Lunas';
        }

        $this->tagihanModel->update($id, [
            'nominal_tagihan'   => $nominal,
            'diskon'            => $diskon,
            'keterangan_diskon' => $this->request->getPost('keterangan_diskon'),
            'status'            => $status
        ]);

        return redirect()->to(base_url('spp/tagihan'))->with('success', 'Tagihan berhasil diperbarui.');
    }

    public function delete($id)
    {
        helper('activity');

        $tagihan = $this->tagihanModel
                        ->select('spp_tagihan.*, santri.nisn, santri.nama_lengkap as nama_santri, spp_tarif.nama_tarif')
                        ->join('santri', 'santri.id = spp_tagihan.santri_id')
                        ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id')
                        ->find($id);

        if (!$tagihan) {
            return redirect()->to(base_url('spp/tagihan'))->with('error', 'Tagihan tidak ditemukan.');
        }

        $pembayaranModel = new SppPembayaranModel();
        $hasPayment = $pembayaranModel->where('tagihan_id', $id)->countAllResults() > 0;

        if ($hasPayment || (float) $tagihan['total_terbayar'] > 0) {
            return redirect()->to(base_url('spp/tagihan'))->with('error', 'Tagihan yang sudah memiliki pembayaran tidak dapat dihapus.');
        }

        $this->tagihanModel->delete($id);

        log_activity('Menghapus Tagihan SPP', 'Spp', 'Santri: ' . $tagihan['nama_santri'] . ', Tagihan: ' . $tagihan['nama_tarif']);

        return redirect()->to(base_url('spp/tagihan'))->with('success', 'Tagihan berhasil dihapus.');
    }

    public function export($format)
    {
        $nisn   = $this->request->getGet('nisn');
        $status = $this->request->getGet('status');
        $bulan  = $this->request->getGet('bulan');
        $q      = $this->request->getGet('q');

        $query = $this->tagihanModel->select('spp_tagihan.*, santri.nisn, santri.nama_lengkap as nama_santri, spp_tarif.nama_tarif')
                                    ->join('santri', 'santri.id = spp_tagihan.santri_id')
                                    ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id');

        if ($nisn)   $query->where('santri.nisn', $nisn);
        if ($status) $query->where('spp_tagihan.status', $status);
        if ($bulan)  $query->where('spp_tagihan.bulan', $bulan);
        if ($q)      $query->like('spp_tagihan.keterangan_diskon', $q);

        $data = $query->orderBy('spp_tagihan.tahun', 'DESC')
                      ->orderBy('spp_tagihan.bulan', 'DESC')
                      ->findAll();

        $data_export = [];
        $months = [0=>'Tahunan',1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
        
        foreach ($data as $row) {
            $data_export[] = [
                'Santri'   => $row['nama_santri'],
                'Tagihan'  => $row['nama_tarif'],
                'Bulan'    => $months[$row['bulan']],
                'Tahun'    => $row['tahun'],
                'Nominal'  => (float)$row['nominal_tagihan'],
                'Potongan' => (float)$row['diskon'],
                'Bayar'    => (float)$row['total_terbayar'],
                'Status'   => $row['status']
            ];
        }

        if ($format == 'excel') {
            return $this->exportToExcel($data_export, "Laporan_Tagihan_" . date('Ymd'));
        } elseif ($format == 'word') {
            return $this->exportToWord($data_export, "Laporan Tagihan SPP", "Laporan_Tagihan_" . date('Ymd'));
        } elseif ($format == 'pdf') {
            return $this->exportToPdf($data_export, "Laporan Tagihan SPP");
        }
    }
}
