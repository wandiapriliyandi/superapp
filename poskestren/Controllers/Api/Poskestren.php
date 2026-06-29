<?php

namespace Poskestren\Controllers\Api;

use CodeIgniter\Controller;
use Poskestren\Models\KunjunganModel;
use Poskestren\Models\ObatModel;
use Poskestren\Models\PemberianObatModel;
use Poskestren\Models\StokMutasiModel;
use App\Models\SantriModel;

class Poskestren extends Controller
{
    protected $kunjunganModel;
    protected $obatModel;
    protected $pemberianObatModel;
    protected $stokMutasiModel;
    protected $santriModel;

    public function __construct()
    {
        $this->kunjunganModel     = new KunjunganModel();
        $this->obatModel           = new ObatModel();
        $this->pemberianObatModel = new PemberianObatModel();
        $this->stokMutasiModel     = new StokMutasiModel();
        $this->santriModel         = new SantriModel();
    }

    // ===================== DASHBOARD STATS =====================

    public function getDashboard()
    {
        $db = \Config\Database::connect();
        
        // Ambil data santri sedang sakit (status = Sakit atau Observasi)
        $santriSakit = $this->kunjunganModel->select('pos_kunjungan.*, santri.nama_lengkap as nama_santri, santri.nis, akademik_kelas.nama_kelas')
            ->join('santri', 'santri.nisn = pos_kunjungan.nisn')
            ->join('akademik_kelas', 'akademik_kelas.id = santri.kelas_id', 'left')
            ->groupStart()
                ->whereIn('pos_kunjungan.status', ['Sakit', 'Observasi'])
                ->orWhere('pos_kunjungan.status IS NULL')
            ->groupEnd()
            ->orderBy('pos_kunjungan.tgl_kunjungan', 'ASC')
            ->get()->getResultArray();

        $stats = [
            'total_kunjungan'    => $this->kunjunganModel->countAllResults(),
            'kunjungan_hari_ini' => $this->kunjunganModel->where('DATE(tgl_kunjungan)', date('Y-m-d'))->countAllResults(),
            'stok_obat_low'      => $this->obatModel->where('stok <', 10)->countAllResults(),
            'santri_sakit'       => count($santriSakit),
        ];

        $recentKunjungan = $this->kunjunganModel->getKunjungan();
        $recentKunjungan = array_slice($recentKunjungan, 0, 5);

        return $this->response->setJSON([
            'status' => 200,
            'data'   => [
                'stats'             => $stats,
                'recent_kunjungan'  => $recentKunjungan,
                'santri_sakit_list' => $santriSakit
            ]
        ]);
    }

    // ===================== KUNJUNGAN / REKAM MEDIS =====================

    public function getKunjungan()
    {
        $page  = $this->request->getVar('page') ? (int) $this->request->getVar('page') : 1;
        $limit = $this->request->getVar('limit') ? (int) $this->request->getVar('limit') : 10;
        $q     = $this->request->getVar('q');

        $db = \Config\Database::connect();
        $countBuilder = $db->table('pos_kunjungan')
            ->join('santri', 'santri.nisn = pos_kunjungan.nisn');

        if ($q) {
            $countBuilder->like('santri.nama_lengkap', $q);
        }
        $total = $countBuilder->countAllResults();

        $totalPages = ceil($total / $limit);
        $offset = ($page - 1) * $limit;

        $mainBuilder = $db->table('pos_kunjungan')
            ->select('pos_kunjungan.*, santri.nama_lengkap as nama_santri, santri.nis, akademik_kelas.nama_kelas')
            ->join('santri', 'santri.nisn = pos_kunjungan.nisn')
            ->join('akademik_kelas', 'akademik_kelas.id = santri.kelas_id', 'left');

        if ($q) {
            $mainBuilder->like('santri.nama_lengkap', $q);
        }

        $data = $mainBuilder->orderBy('pos_kunjungan.tgl_kunjungan', 'DESC')->limit($limit, $offset)->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 200,
            'data'   => $data,
            'pagination' => [
                'total'       => $total,
                'page'        => $page,
                'limit'       => $limit,
                'total_pages' => $totalPages
            ]
        ]);
    }

    public function detailKunjungan($id)
    {
        $kunjungan = $this->kunjunganModel->getKunjungan($id);
        if (!$kunjungan) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Rekam medis tidak ditemukan']);
        }

        $obatDiberikan = $this->pemberianObatModel->getByKunjungan($id);

        return $this->response->setJSON([
            'status' => 200,
            'data'   => [
                'kunjungan' => $kunjungan,
                'obat'      => $obatDiberikan
            ]
        ]);
    }

    public function saveKunjungan()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $nisn          = $data['nisn'] ?? '';
        $tglKunjungan  = $data['tgl_kunjungan'] ?? '';
        $keluhan       = $data['keluhan'] ?? '';
        $diagnosa      = $data['diagnosa'] ?? '';
        $tindakan      = $data['tindakan'] ?? '';
        $status        = $data['status'] ?? 'Sakit';
        $obatItems     = $data['obat_items'] ?? []; // Array of ['obat_id', 'jumlah', 'dosis']

        if (empty($nisn) || empty($tglKunjungan) || empty($keluhan)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'NISN, tanggal kunjungan, dan keluhan wajib diisi']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $kunjunganId = $this->kunjunganModel->insert([
            'nisn'          => $nisn,
            'tgl_kunjungan' => $tglKunjungan,
            'keluhan'       => $keluhan,
            'diagnosa'      => $diagnosa,
            'tindakan'      => $tindakan,
            'status'        => $status,
            'petugas_id'    => 1 // Default Admin
        ]);

        if (!empty($obatItems)) {
            foreach ($obatItems as $item) {
                $obatId = (int) ($item['obat_id'] ?? 0);
                $qty    = (int) ($item['jumlah'] ?? 0);
                $dosis  = $item['dosis'] ?? '';

                if ($obatId > 0 && $qty > 0) {
                    $resMutasi = $this->stokMutasiModel->catat(
                        $obatId,
                        $qty,
                        'keluar',
                        'konsumsi',
                        'Pemberian ke pasien (kunjungan #' . $kunjunganId . ')',
                        $kunjunganId,
                        'kunjungan',
                        1, // Default Admin
                        false
                    );

                    if (!$resMutasi['ok']) {
                        $db->transRollback();
                        return $this->response->setStatusCode(400)->setJSON(['status' => 400, 'message' => $resMutasi['message']]);
                    }

                    $this->pemberianObatModel->insert([
                        'kunjungan_id' => $kunjunganId,
                        'obat_id'      => $obatId,
                        'jumlah'       => $qty,
                        'dosis'        => $dosis
                    ]);
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 500, 'message' => 'Gagal menyimpan rekam medis']);
        }

        log_activity('Mencatat Kunjungan Pasien (API)', 'Poskestren', 'Kunjungan ID: ' . $kunjunganId . ', NISN: ' . $nisn);
        return $this->response->setJSON(['status' => 200, 'message' => 'Rekam medis kunjungan berhasil dicatat!']);
    }

    public function updatePerkembangan($id)
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $status       = $data['status'] ?? '';
        $diagnosa     = $data['diagnosa'] ?? '';
        $tindakanBaru = $data['tindakan'] ?? '';
        $obatItems    = $data['obat_items'] ?? [];

        $kunjungan = $this->kunjunganModel->find($id);
        if (!$kunjungan) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Rekam medis tidak ditemukan']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $tindakanFinal = $kunjungan['tindakan'];
        if (!empty($tindakanBaru)) {
            $logTindakan   = "\n[" . date('d/m/Y H:i') . "] " . $tindakanBaru;
            $tindakanFinal = !empty($tindakanFinal) ? $tindakanFinal . $logTindakan : $tindakanBaru;
        }

        $updateData = [
            'status' => $status ?: $kunjungan['status']
        ];
        if (!empty($diagnosa)) {
            $updateData['diagnosa'] = $diagnosa;
        }
        if (!empty($tindakanBaru)) {
            $updateData['tindakan'] = $tindakanFinal;
        }

        $this->kunjunganModel->update($id, $updateData);

        if (!empty($obatItems)) {
            foreach ($obatItems as $item) {
                $obatId = (int) ($item['obat_id'] ?? 0);
                $qty    = (int) ($item['jumlah'] ?? 0);
                $dosis  = $item['dosis'] ?? '';

                if ($obatId > 0 && $qty > 0) {
                    $resMutasi = $this->stokMutasiModel->catat(
                        $obatId,
                        $qty,
                        'keluar',
                        'konsumsi',
                        'Pemberian obat tambahan (kunjungan #' . $id . ')',
                        $id,
                        'kunjungan',
                        1,
                        false
                    );

                    if (!$resMutasi['ok']) {
                        $db->transRollback();
                        return $this->response->setStatusCode(400)->setJSON(['status' => 400, 'message' => $resMutasi['message']]);
                    }

                    $this->pemberianObatModel->insert([
                        'kunjungan_id' => $id,
                        'obat_id'      => $obatId,
                        'jumlah'       => $qty,
                        'dosis'        => $dosis
                    ]);
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 500, 'message' => 'Gagal memperbarui rekam medis']);
        }

        log_activity('Memperbarui Rekam Medis (API)', 'Poskestren', 'Kunjungan ID: ' . $id);
        return $this->response->setJSON(['status' => 200, 'message' => 'Rekam medis perkembangan berhasil diperbarui!']);
    }

    public function deleteKunjungan($id)
    {
        helper('activity');
        $kunjungan = $this->kunjunganModel->find($id);
        if (!$kunjungan) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Rekam medis tidak ditemukan']);
        }

        $this->kunjunganModel->delete($id);
        log_activity('Menghapus Rekam Medis (API)', 'Poskestren', 'Kunjungan ID: ' . $id);

        return $this->response->setJSON(['status' => 200, 'message' => 'Rekam medis berhasil dihapus']);
    }

    // ===================== OBAT MASTER =====================

    public function getObat()
    {
        $page  = $this->request->getVar('page') ? (int) $this->request->getVar('page') : 1;
        $limit = $this->request->getVar('limit') ? (int) $this->request->getVar('limit') : 10;
        $q     = $this->request->getVar('q');

        $db = \Config\Database::connect();
        $countBuilder = $db->table('pos_obat');
        if ($q) {
            $countBuilder->like('nama_obat', $q);
        }
        $total = $countBuilder->countAllResults();

        $totalPages = ceil($total / $limit);
        $offset = ($page - 1) * $limit;

        $mainBuilder = $db->table('pos_obat');
        if ($q) {
            $mainBuilder->like('nama_obat', $q);
        }
        $data = $mainBuilder->orderBy('nama_obat', 'ASC')->limit($limit, $offset)->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 200,
            'data'   => $data,
            'pagination' => [
                'total'       => $total,
                'page'        => $page,
                'limit'       => $limit,
                'total_pages' => $totalPages
            ]
        ]);
    }

    public function saveObat()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $id       = !empty($data['id']) ? $data['id'] : null;
        $namaObat = $data['nama_obat'] ?? '';
        $satuan   = $data['satuan'] ?? '';
        $desk     = $data['deskripsi'] ?? '';
        $stokAwal = (int) ($data['stok_awal'] ?? 0);

        if (empty($namaObat) || empty($satuan)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Nama obat dan satuan wajib diisi']);
        }

        if ($id) {
            $this->obatModel->update($id, [
                'nama_obat' => $namaObat,
                'satuan'    => $satuan,
                'deskripsi' => $desk,
            ]);
            log_activity('Memperbarui Data Obat (API)', 'Poskestren', 'Obat: ' . $namaObat);
        } else {
            $newId = $this->obatModel->insert([
                'nama_obat' => $namaObat,
                'satuan'    => $satuan,
                'stok'      => 0,
                'deskripsi' => $desk,
            ]);

            if ($stokAwal > 0) {
                $this->stokMutasiModel->catat(
                    (int) $newId,
                    $stokAwal,
                    'masuk',
                    'pengadaan',
                    'Stok awal pendaftaran obat via API',
                    null,
                    null,
                    1
                );
            }
            log_activity('Mendaftarkan Obat Baru (API)', 'Poskestren', 'Obat: ' . $namaObat . ', Stok Awal: ' . $stokAwal);
        }

        return $this->response->setJSON(['status' => 200, 'message' => 'Data obat berhasil disimpan']);
    }

    public function deleteObat($id)
    {
        helper('activity');
        $obat = $this->obatModel->find($id);
        if (!$obat) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Obat tidak ditemukan']);
        }

        $this->obatModel->delete($id);
        log_activity('Menghapus Obat (API)', 'Poskestren', 'Obat: ' . $obat['nama_obat']);

        return $this->response->setJSON(['status' => 200, 'message' => 'Obat berhasil dihapus']);
    }

    // ===================== STOK MUTASI & PENGADAAN =====================

    public function getRiwayatStok()
    {
        $page  = $this->request->getVar('page') ? (int) $this->request->getVar('page') : 1;
        $limit = $this->request->getVar('limit') ? (int) $this->request->getVar('limit') : 10;
        $obatId = $this->request->getVar('obat_id');

        $db = \Config\Database::connect();
        $countBuilder = $db->table('pos_stok_mutasi');
        if ($obatId) {
            $countBuilder->where('obat_id', (int) $obatId);
        }
        $total = $countBuilder->countAllResults();

        $totalPages = ceil($total / $limit);
        $offset = ($page - 1) * $limit;

        $mainBuilder = $db->table('pos_stok_mutasi')
            ->select('pos_stok_mutasi.*, pos_obat.nama_obat, pos_obat.satuan')
            ->join('pos_obat', 'pos_obat.id = pos_stok_mutasi.obat_id');
        if ($obatId) {
            $mainBuilder->where('pos_stok_mutasi.obat_id', (int) $obatId);
        }
        $data = $mainBuilder->orderBy('pos_stok_mutasi.created_at', 'DESC')->limit($limit, $offset)->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 200,
            'data'   => $data,
            'pagination' => [
                'total'       => $total,
                'page'        => $page,
                'limit'       => $limit,
                'total_pages' => $totalPages
            ]
        ]);
    }

    public function savePengadaan()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $items   = $data['items'] ?? [];
        $ket     = $data['keterangan'] ?? 'Pengadaan obat';
        $tanggal = $data['tanggal'] ?? '';

        if (empty($items)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Belum ada obat yang diinput untuk pengadaan']);
        }

        $createdAt = null;
        if (!empty($tanggal)) {
            $createdAt = date('Y-m-d H:i:s', strtotime($tanggal));
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $successCount = 0;
        foreach ($items as $item) {
            $obatId = (int) ($item['obat_id'] ?? 0);
            $jumlah = (int) ($item['jumlah'] ?? 0);

            if ($obatId > 0 && $jumlah > 0) {
                $res = $this->stokMutasiModel->catat(
                    $obatId,
                    $jumlah,
                    'masuk',
                    'pengadaan',
                    $ket,
                    null,
                    null,
                    1,
                    false,
                    $createdAt
                );

                if (!$res['ok']) {
                    $db->transRollback();
                    return $this->response->setStatusCode(400)->setJSON(['status' => 400, 'message' => $res['message']]);
                }
                $successCount++;
            }
        }

        if ($successCount === 0) {
            $db->transRollback();
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Silakan masukkan jumlah obat yang valid']);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 500, 'message' => 'Gagal mencatat pengadaan obat']);
        }

        log_activity('Pencatatan Pengadaan Obat (API)', 'Poskestren', 'Keterangan: ' . $ket);
        return $this->response->setJSON(['status' => 200, 'message' => 'Pengadaan obat berhasil dicatat!']);
    }

    public function saveMutasiKeluar()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $items   = $data['items'] ?? [];
        $ket     = $data['keterangan'] ?? 'Obat keluar manual';
        $jenis   = $data['jenis'] ?? 'musnah'; // musnah, konsumsi
        $tanggal = $data['tanggal'] ?? '';

        if (empty($items)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Belum ada obat yang diinput untuk mutasi keluar']);
        }

        $createdAt = null;
        if (!empty($tanggal)) {
            $createdAt = date('Y-m-d H:i:s', strtotime($tanggal));
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $successCount = 0;
        foreach ($items as $item) {
            $obatId = (int) ($item['obat_id'] ?? 0);
            $jumlah = (int) ($item['jumlah'] ?? 0);

            if ($obatId > 0 && $jumlah > 0) {
                $res = $this->stokMutasiModel->catat(
                    $obatId,
                    $jumlah,
                    'keluar',
                    $jenis,
                    $ket,
                    null,
                    null,
                    1,
                    false,
                    $createdAt
                );

                if (!$res['ok']) {
                    $db->transRollback();
                    return $this->response->setStatusCode(400)->setJSON(['status' => 400, 'message' => $res['message']]);
                }
                $successCount++;
            }
        }

        if ($successCount === 0) {
            $db->transRollback();
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Silakan masukkan jumlah obat yang valid']);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 500, 'message' => 'Gagal mencatat obat keluar']);
        }

        log_activity('Pencatatan Obat Keluar Manual (API)', 'Poskestren', 'Keterangan: ' . $ket);
        return $this->response->setJSON(['status' => 200, 'message' => 'Pengeluaran obat berhasil dicatat!']);
    }

    // ===================== FORM POPULATORS =====================

    public function getSantriList()
    {
        $santri = $this->santriModel->select('nisn, nama_lengkap, nis')->orderBy('nama_lengkap', 'ASC')->findAll();
        return $this->response->setJSON([
            'status' => 200,
            'data'   => $santri
        ]);
    }

    public function getObatAktif()
    {
        $obat = $this->obatModel->where('stok >', 0)->orderBy('nama_obat', 'ASC')->findAll();
        return $this->response->setJSON([
            'status' => 200,
            'data'   => $obat
        ]);
    }
}
