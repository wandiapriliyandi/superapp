<?php

namespace Spp\Controllers\Api;

use App\Controllers\BaseController;
use Spp\Models\SppTagihanModel;
use Spp\Models\SppPembayaranModel;
use Spp\Models\SppTarifModel;
use Spp\Models\SppSantriTarifModel;
use App\Models\SantriModel;

class Spp extends BaseController
{
    protected $tagihanModel;
    protected $pembayaranModel;
    protected $tarifModel;
    protected $mappingModel;
    protected $santriModel;

    public function __construct()
    {
        $this->tagihanModel    = new SppTagihanModel();
        $this->pembayaranModel  = new SppPembayaranModel();
        $this->tarifModel      = new SppTarifModel();
        $this->mappingModel    = new SppSantriTarifModel();
        $this->santriModel     = new SantriModel();
    }

    // ===================== TAGIHAN =====================

    public function indexTagihan()
    {
        $page   = $this->request->getVar('page') ? (int) $this->request->getVar('page') : 1;
        $limit  = $this->request->getVar('limit') ? (int) $this->request->getVar('limit') : 10;
        $nisn   = $this->request->getVar('nisn');
        $status = $this->request->getVar('status');
        $bulan  = $this->request->getVar('bulan');
        $q      = $this->request->getVar('q');

        $db = \Config\Database::connect();
        $countBuilder = $db->table('spp_tagihan')
            ->join('santri', 'santri.id = spp_tagihan.santri_id')
            ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id');

        if ($nisn)   $countBuilder->where('santri.nisn', $nisn);
        if ($status) $countBuilder->where('spp_tagihan.status', $status);
        if ($bulan !== null && $bulan !== '')  $countBuilder->where('spp_tagihan.bulan', $bulan);
        if ($q)      $countBuilder->like('santri.nama_lengkap', $q);
        $total = $countBuilder->countAllResults();

        $totalPages = ceil($total / $limit);
        $offset = ($page - 1) * $limit;

        $mainBuilder = $db->table('spp_tagihan')
            ->select('spp_tagihan.*, santri.nisn, santri.nama_lengkap as nama_santri, spp_tarif.nama_tarif')
            ->join('santri', 'santri.id = spp_tagihan.santri_id')
            ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id');

        if ($nisn)   $mainBuilder->where('santri.nisn', $nisn);
        if ($status) $mainBuilder->where('spp_tagihan.status', $status);
        if ($bulan !== null && $bulan !== '')  $mainBuilder->where('spp_tagihan.bulan', $bulan);
        if ($q)      $mainBuilder->like('santri.nama_lengkap', $q);

        $data = $mainBuilder->orderBy('spp_tagihan.tahun', 'DESC')
                      ->orderBy('spp_tagihan.bulan', 'DESC')
                      ->limit($limit, $offset)
                      ->get()->getResultArray();

        return $this->response->setJSON([
            'status'  => 200,
            'message' => 'Data tagihan berhasil diambil',
            'data'    => $data,
            'pagination' => [
                'total'       => $total,
                'page'        => $page,
                'limit'       => $limit,
                'total_pages' => $totalPages
            ]
        ]);
    }

    public function generateTagihanMassal()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $bulan    = $data['bulan'] ?? date('n');
        $tahun    = $data['tahun'] ?? date('Y');
        $mode     = $data['mode']  ?? 'mapping'; // 'single' atau 'mapping'
        $tarif_id = $data['tarif_id'] ?? null;

        $count = 0;

        if ($mode === 'single') {
            if (empty($tarif_id)) {
                return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Pilih tarif yang valid untuk mode single.']);
            }
            $tarif = $this->tarifModel->find($tarif_id);
            if (!$tarif) {
                return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Tarif tidak ditemukan.']);
            }

            $santri = $this->santriModel->where('status_santri', 'Aktif')->findAll();
            foreach ($santri as $s) {
                if ($s['nisn']) {
                    if ($this->createTagihanIfNotExist($s['nisn'], $tarif_id, $bulan, $tahun, $tarif['nominal'], $tarif['tipe'])) {
                        $count++;
                    }
                }
            }
        } else {
            // Mode Mapping
            $mappings = $this->mappingModel
                ->select('spp_santri_tarif.*, spp_tarif.nominal, spp_tarif.tipe, santri.status_santri')
                ->join('santri', 'santri.nisn = spp_santri_tarif.nisn')
                ->join('spp_tarif', 'spp_tarif.id = spp_santri_tarif.tarif_id')
                ->findAll();

            foreach ($mappings as $m) {
                if ($m['status_santri'] === 'Aktif') {
                    if ($this->createTagihanIfNotExist($m['nisn'], $m['tarif_id'], $bulan, $tahun, $m['nominal'], $m['tipe'], $m['nominal_diskon'], $m['keterangan_diskon'])) {
                        $count++;
                    }
                }
            }
        }

        log_activity('Generate Tagihan SPP Massal (API)', 'Spp', 'Mode: ' . $mode . ', Bulan: ' . $bulan . ', Total: ' . $count);
        return $this->response->setJSON(['status' => 200, 'message' => $count . ' Tagihan berhasil digenerate.']);
    }

    public function generateTagihanTahunan()
    {
        helper('activity');
        $data  = $this->request->getJSON(true) ?? $this->request->getPost();
        $ta_id = $data['id_tahun_akademik'] ?? null;

        if (!$ta_id) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Pilih Tahun Akademik terlebih dahulu.']);
        }

        $db = \Config\Database::connect();
        $ta = $db->table('tahun_akademik')->where('id', $ta_id)->get()->getRowArray();
        if (!$ta) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Tahun Akademik tidak ditemukan.']);
        }

        $years = explode('/', $ta['nama_tahun']);
        if (count($years) != 2) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Format Nama Tahun harus "YYYY/YYYY" (contoh: 2024/2025)']);
        }

        $startYear = trim($years[0]);
        $endYear   = trim($years[1]);

        $allMappings = $this->mappingModel
            ->select('spp_santri_tarif.*, spp_tarif.nominal, spp_tarif.tipe, spp_santri_tarif.nisn')
            ->join('santri', 'santri.nisn = spp_santri_tarif.nisn')
            ->join('spp_tarif', 'spp_tarif.id = spp_santri_tarif.tarif_id')
            ->where('spp_tarif.id_tahun_akademik', $ta_id)
            ->findAll();

        $count = 0;
        foreach ($allMappings as $m) {
            if ($m['tipe'] === 'Bulanan') {
                for ($bln = 7; $bln <= 12; $bln++) {
                    if ($this->createTagihanIfNotExist($m['nisn'], $m['tarif_id'], $bln, $startYear, $m['nominal'], 'Bulanan', $m['nominal_diskon'], $m['keterangan_diskon'])) {
                        $count++;
                    }
                }
                for ($bln = 1; $bln <= 6; $bln++) {
                    if ($this->createTagihanIfNotExist($m['nisn'], $m['tarif_id'], $bln, $endYear, $m['nominal'], 'Bulanan', $m['nominal_diskon'], $m['keterangan_diskon'])) {
                        $count++;
                    }
                }
            } else {
                if ($this->createTagihanIfNotExist($m['nisn'], $m['tarif_id'], 0, $startYear, $m['nominal'], 'Tahunan', $m['nominal_diskon'], $m['keterangan_diskon'])) {
                    $count++;
                }
            }
        }

        log_activity('Generate Tagihan SPP Tahunan (API)', 'Spp', 'Tahun Akademik: ' . $ta['nama_tahun'] . ', Total: ' . $count);
        return $this->response->setJSON(['status' => 200, 'message' => "Berhasil men-generate $count tagihan untuk satu tahun ajaran."]);
    }

    public function generateTagihanSantri($santri_id)
    {
        helper('activity');
        $santri = $this->santriModel->find($santri_id);
        if (!$santri) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Santri tidak ditemukan.']);
        }

        $nisn = $santri['nisn'];
        if (!$nisn) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Santri tidak memiliki NISN.']);
        }

        $mappings = $this->mappingModel
            ->select('spp_santri_tarif.*, spp_tarif.nominal, spp_tarif.tipe')
            ->join('santri', 'santri.nisn = spp_santri_tarif.nisn')
            ->join('spp_tarif', 'spp_tarif.id = spp_santri_tarif.tarif_id')
            ->where('spp_santri_tarif.nisn', $nisn)
            ->findAll();

        if (empty($mappings)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Santri ini belum memiliki pemetaan tarif.']);
        }

        $bulan = date('n');
        $tahun = date('Y');
        $count = 0;

        foreach ($mappings as $m) {
            if ($this->createTagihanIfNotExist($nisn, $m['tarif_id'], $bulan, $tahun, $m['nominal'], $m['tipe'], $m['nominal_diskon'], $m['keterangan_diskon'])) {
                $count++;
            }
        }

        log_activity('Generate Tagihan Per Orang (API)', 'Spp', 'Santri NISN: ' . $nisn . ', Total: ' . $count);
        return $this->response->setJSON(['status' => 200, 'message' => $count . ' Tagihan berhasil digenerate untuk bulan ini.']);
    }

    public function deleteTagihan($id)
    {
        helper('activity');
        $tagihan = $this->tagihanModel
            ->select('spp_tagihan.*, santri.nama_lengkap as nama_santri, spp_tarif.nama_tarif')
            ->join('santri', 'santri.id = spp_tagihan.santri_id')
            ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id')
            ->find($id);

        if (!$tagihan) return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Tagihan tidak ditemukan']);

        $this->tagihanModel->delete($id);
        log_activity('Menghapus Tagihan SPP (API)', 'Spp', 'Santri: ' . $tagihan['nama_santri'] . ', Tagihan: ' . $tagihan['nama_tarif']);
        return $this->response->setJSON(['status' => 200, 'message' => 'Tagihan berhasil dihapus']);
    }

    // ===================== PEMBAYARAN =====================

    public function indexPembayaran()
    {
        $page   = $this->request->getVar('page') ? (int) $this->request->getVar('page') : 1;
        $limit  = $this->request->getVar('limit') ? (int) $this->request->getVar('limit') : 10;
        $tagihan_id = $this->request->getVar('tagihan_id');
        $q          = $this->request->getVar('q');

        $db = \Config\Database::connect();
        $countBuilder = $db->table('spp_pembayaran')
            ->join('spp_tagihan', 'spp_tagihan.id = spp_pembayaran.tagihan_id')
            ->join('santri', 'santri.id = spp_tagihan.santri_id')
            ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id');

        if ($tagihan_id) $countBuilder->where('spp_pembayaran.tagihan_id', $tagihan_id);
        if ($q)          $countBuilder->like('santri.nama_lengkap', $q);
        $total = $countBuilder->countAllResults();

        $totalPages = ceil($total / $limit);
        $offset = ($page - 1) * $limit;

        $mainBuilder = $db->table('spp_pembayaran')
            ->select('spp_pembayaran.*, santri.nama_lengkap as nama_santri, spp_tarif.nama_tarif')
            ->join('spp_tagihan', 'spp_tagihan.id = spp_pembayaran.tagihan_id')
            ->join('santri', 'santri.id = spp_tagihan.santri_id')
            ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id');

        if ($tagihan_id) $mainBuilder->where('spp_pembayaran.tagihan_id', $tagihan_id);
        if ($q)          $mainBuilder->like('santri.nama_lengkap', $q);

        $data = $mainBuilder->orderBy('spp_pembayaran.created_at', 'DESC')
                            ->limit($limit, $offset)
                            ->get()->getResultArray();

        return $this->response->setJSON([
            'status'  => 200,
            'message' => 'Data pembayaran berhasil diambil',
            'data'    => $data,
            'pagination' => [
                'total'       => $total,
                'page'        => $page,
                'limit'       => $limit,
                'total_pages' => $totalPages
            ]
        ]);
    }

    public function savePembayaran()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data['tagihan_id']) || empty($data['nominal_bayar'])) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Tagihan ID dan nominal bayar wajib diisi']);
        }

        $tagihan = $this->tagihanModel->find($data['tagihan_id']);
        if (!$tagihan) return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Tagihan tidak ditemukan']);

        $nomorTrx = 'TRX-S-' . date('YmdHis') . '-' . $data['tagihan_id'];
        $this->pembayaranModel->save([
            'tagihan_id'      => $data['tagihan_id'],
            'nominal_bayar'   => $data['nominal_bayar'],
            'tanggal_bayar'   => $data['tanggal_bayar'] ?? date('Y-m-d'),
            'metode_bayar'    => $data['metode_bayar'] ?? 'Tunai',
            'keterangan'      => $data['keterangan'] ?? null,
            'nomor_transaksi' => $nomorTrx,
        ]);

        $totalBayar = $tagihan['total_terbayar'] + $data['nominal_bayar'];
        $net        = $tagihan['nominal_tagihan'] - ($tagihan['diskon'] ?? 0);
        $status     = $totalBayar >= $net ? 'Lunas' : 'Cicilan';

        $this->tagihanModel->update($data['tagihan_id'], [
            'total_terbayar' => $totalBayar,
            'status'         => $status
        ]);

        log_activity('Mencatat Pembayaran SPP (API)', 'Spp', 'Transaksi: ' . $nomorTrx);
        return $this->response->setJSON(['status' => 200, 'message' => 'Pembayaran berhasil dicatat. No: ' . $nomorTrx]);
    }

    // ===================== TARIF =====================

    public function indexTarif()
    {
        $id_tahun = $this->request->getGet('id_tahun_akademik');
        
        $query = $this->tarifModel
            ->select('spp_tarif.*, tahun_akademik.nama_tahun')
            ->join('tahun_akademik', 'tahun_akademik.id = spp_tarif.id_tahun_akademik', 'left');
        
        if ($id_tahun) {
            $query->where('spp_tarif.id_tahun_akademik', $id_tahun);
        }

        return $this->response->setJSON([
            'status' => 200,
            'data'   => $query->orderBy('spp_tarif.nama_tarif', 'ASC')->findAll()
        ]);
    }

    public function saveTarif()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data['nama_tarif']) || empty($data['nominal'])) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Nama tarif dan nominal wajib diisi']);
        }

        if (!empty($data['id'])) {
            $this->tarifModel->update($data['id'], $data);
            log_activity('Mengubah Tarif SPP (API)', 'Spp', 'Nama Tarif: ' . $data['nama_tarif']);
        } else {
            $this->tarifModel->save($data);
            log_activity('Menambah Tarif SPP (API)', 'Spp', 'Nama Tarif: ' . $data['nama_tarif']);
        }

        return $this->response->setJSON(['status' => 200, 'message' => 'Tarif berhasil disimpan']);
    }

    public function deleteTarif($id)
    {
        helper('activity');
        $tarif = $this->tarifModel->find($id);
        if (!$tarif) return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Tarif tidak ditemukan']);

        // Hapus pemetaan yang menggunakan tarif ini
        $this->mappingModel->where('tarif_id', $id)->delete();
        $this->tarifModel->delete($id);

        log_activity('Menghapus Tarif SPP (API)', 'Spp', 'Nama Tarif: ' . $tarif['nama_tarif']);
        return $this->response->setJSON(['status' => 200, 'message' => 'Tarif berhasil dihapus']);
    }

    // ===================== MAPPING =====================

    public function indexMapping()
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
        
        $data = [];
        foreach ($santri as $s) {
            $currentMapping = $this->mappingModel
                ->select('spp_santri_tarif.*, spp_tarif.nama_tarif, spp_tarif.nominal')
                ->join('spp_tarif', 'spp_tarif.id = spp_santri_tarif.tarif_id')
                ->where('spp_santri_tarif.nisn', $s['nisn'])
                ->findAll();

            $data[] = [
                'id'            => $s['id'],
                'nama_lengkap'  => $s['nama_lengkap'],
                'nisn'          => $s['nisn'],
                'jenis_kelamin' => $s['jenis_kelamin'],
                'mappings'      => $currentMapping
            ];
        }

        return $this->response->setJSON(['status' => 200, 'data' => $data]);
    }

    public function santriMapping($santri_id)
    {
        $santri = $this->santriModel->find($santri_id);
        if (!$santri) return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Santri tidak ditemukan']);

        $tarif = $this->tarifModel
            ->select('spp_tarif.*, tahun_akademik.nama_tahun')
            ->join('tahun_akademik', 'tahun_akademik.id = spp_tarif.id_tahun_akademik', 'left')
            ->orderBy('tahun_akademik.nama_tahun', 'DESC')
            ->orderBy('nominal', 'DESC')
            ->findAll();

        $currentMapping = $this->mappingModel->where('nisn', $santri['nisn'])->findAll();

        return $this->response->setJSON([
            'status' => 200,
            'data'   => [
                'santri'  => $santri,
                'tarif'   => $tarif,
                'current' => $currentMapping
            ]
        ]);
    }

    public function saveMapping()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        
        $nisn = $data['nisn'] ?? null;
        if (!$nisn) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'NISN wajib disertakan']);
        }

        $tarif_ids = $data['tarif_ids'] ?? [];
        $diskon    = $data['nominal_diskon'] ?? [];
        $ket       = $data['keterangan_diskon'] ?? [];

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
        log_activity('Mengubah Pemetaan Tarif Santri (API)', 'Spp', 'Santri: ' . ($santri['nama_lengkap'] ?? '') . ' (NISN: ' . $nisn . '), Total Tarif: ' . count($tarif_ids));
        
        return $this->response->setJSON(['status' => 200, 'message' => 'Pemetaan tarif berhasil disimpan']);
    }

    // ===================== REFERENSI =====================

    public function listTahunAkademik()
    {
        $db   = \Config\Database::connect();
        $data = $db->table('tahun_akademik')->orderBy('nama_tahun', 'DESC')->get()->getResultArray();
        return $this->response->setJSON(['status' => 200, 'data' => $data]);
    }

    // ===================== STATS =====================

    public function stats()
    {
        $db   = \Config\Database::connect();
        $stats = [
            'total_tagihan'     => $db->table('spp_tagihan')->countAll(),
            'belum_lunas'       => $db->table('spp_tagihan')->where('status', 'Belum Lunas')->countAllResults(),
            'cicilan'           => $db->table('spp_tagihan')->where('status', 'Cicilan')->countAllResults(),
            'lunas'             => $db->table('spp_tagihan')->where('status', 'Lunas')->countAllResults(),
            'total_terkumpul'   => (float) $db->table('spp_pembayaran')->selectSum('nominal_bayar')->get()->getRowArray()['nominal_bayar'] ?? 0,
        ];

        return $this->response->setJSON(['status' => 200, 'data' => $stats]);
    }

    // ===================== INTERNAL HELPERS =====================

    private function createTagihanIfNotExist($nisn, $tarif_id, $bulan, $tahun, $nominal, $tipe = 'Bulanan', $diskon = 0, $ket_diskon = null)
    {
        $santri = $this->santriModel->where('nisn', $nisn)->first();
        if (!$santri) return false;

        $query = $this->tagihanModel->where([
            'santri_id' => $santri['id'],
            'tarif_id'  => $tarif_id,
            'tahun'     => $tahun
        ]);

        if ($tipe === 'Bulanan') {
            $query->where('bulan', $bulan);
        }

        $existing = $query->first();

        if (!$existing) {
            $status = (($nominal - $diskon) <= 0) ? 'Lunas' : 'Belum Lunas';
            $this->tagihanModel->save([
                'santri_id'         => $santri['id'],
                'tarif_id'          => $tarif_id,
                'bulan'             => ($tipe === 'Tahunan') ? 0 : $bulan,
                'tahun'             => $tahun,
                'nominal_tagihan'   => $nominal,
                'diskon'            => $diskon,
                'keterangan_diskon' => $ket_diskon,
                'total_terbayar'    => 0,
                'status'            => $status
            ]);
            return true;
        } else {
            if ($existing['status'] === 'Belum Lunas' && $existing['total_terbayar'] == 0) {
                $this->tagihanModel->update($existing['id'], [
                    'nominal_tagihan' => $nominal
                ]);
            }
        }
        return false;
    }
}
