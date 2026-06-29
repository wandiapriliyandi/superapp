<?php

namespace Alquran\Controllers\Api;

use CodeIgniter\Controller;
use Alquran\Models\TahsinModel;
use Alquran\Models\TahfidzModel;
use Alquran\Models\DoaModel;
use Alquran\Models\MasterDoaModel;
use App\Models\SantriModel;

class Alquran extends Controller
{
    protected $tahsinModel;
    protected $tahfidzModel;
    protected $doaModel;
    protected $masterDoaModel;
    protected $santriModel;

    public function __construct()
    {
        $this->tahsinModel = new TahsinModel();
        $this->tahfidzModel = new TahfidzModel();
        $this->doaModel = new DoaModel();
        $this->masterDoaModel = new MasterDoaModel();
        $this->santriModel = new SantriModel();
        helper('activity');
    }

    // ===================== LIST SANTRI DENGAN PROGRES RINGKAS =====================
    public function indexSantri()
    {
        $db = \Config\Database::connect();
        
        // Ambil data filter kelas jika ada
        $kelasId = $this->request->getGet('kelas_id');

        $builder = $db->table('santri');
        $builder->select('santri.id, santri.nama_lengkap, santri.nisn, santri.nis, santri.status_santri, santri.kelas_id, akademik_kelas.nama_kelas');
        $builder->join('akademik_kelas', 'akademik_kelas.id = santri.kelas_id', 'left');
        $builder->where('santri.deleted_at IS NULL');
        
        if (!empty($kelasId)) {
            $builder->where('santri.kelas_id', $kelasId);
        }
        
        $santriList = $builder->orderBy('santri.nama_lengkap', 'ASC')->get()->getResultArray();

        // Cari riwayat progres terakhir untuk setiap santri
        foreach ($santriList as &$s) {
            // Tahsin terakhir
            $latestTahsin = $this->tahsinModel
                ->where('santri_id', $s['id'])
                ->orderBy('tanggal_penilaian', 'DESC')
                ->orderBy('id', 'DESC')
                ->first();

            if ($latestTahsin) {
                $avg = ($latestTahsin['makharijul_huruf'] + $latestTahsin['sifat_huruf'] + $latestTahsin['tajwid']) / 3;
                $s['tahsin_summary'] = [
                    'makharij' => $latestTahsin['makharijul_huruf'],
                    'sifat'    => $latestTahsin['sifat_huruf'],
                    'tajwid'   => $latestTahsin['tajwid'],
                    'rata_rata'=> round($avg, 1),
                    'tanggal'  => $latestTahsin['tanggal_penilaian']
                ];
            } else {
                $s['tahsin_summary'] = null;
            }

            // Tahfidz terakhir
            $latestTahfidz = $this->tahfidzModel
                ->where('santri_id', $s['id'])
                ->orderBy('tanggal_setor', 'DESC')
                ->orderBy('id', 'DESC')
                ->first();

            if ($latestTahfidz) {
                $s['tahfidz_summary'] = [
                    'tipe'    => $latestTahfidz['tipe_setoran'],
                    'juz'     => $latestTahfidz['juz'],
                    'surah'   => $latestTahfidz['surah_selesai'],
                    'ayat'    => $latestTahfidz['ayat_selesai'],
                    'tanggal' => $latestTahfidz['tanggal_setor'],
                    'predikat'=> $latestTahfidz['predikat']
                ];
            } else {
                $s['tahfidz_summary'] = null;
            }
        }

        // Ambil daftar kelas untuk populator filter di frontend
        $kelas = $db->table('akademik_kelas')->select('id, nama_kelas')->orderBy('nama_kelas', 'ASC')->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 200,
            'data'   => [
                'santri' => $santriList,
                'kelas'  => $kelas
            ]
        ]);
    }

    // ===================== MANAJEMEN TAHSIN =====================
    public function indexTahsin()
    {
        $santriId = $this->request->getGet('santri_id');
        if (empty($santriId)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 400, 'message' => 'Santri ID wajib disertakan']);
        }

        $riwayat = $this->tahsinModel->getTahsinBySantri($santriId);
        return $this->response->setJSON([
            'status' => 200,
            'data'   => $riwayat
        ]);
    }

    public function saveTahsin()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $id        = !empty($data['id']) ? $data['id'] : null;
        $santriId  = $data['santri_id'] ?? '';
        $makharij  = $data['makharijul_huruf'] ?? 0;
        $sifat     = $data['sifat_huruf'] ?? 0;
        $tajwid    = $data['tajwid'] ?? 0;
        $detail    = $data['detail_penilaian'] ?? null;
        $catatan   = $data['catatan'] ?? '';
        $tgl       = $data['tanggal_penilaian'] ?? date('Y-m-d');
        $pengujiId = $data['penguji_id'] ?? 1; // Fallback ke admin / penguji id

        if (empty($santriId)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Santri wajib dipilih']);
        }

        $payload = [
            'santri_id'         => (int) $santriId,
            'makharijul_huruf'  => (int) $makharij,
            'sifat_huruf'       => (int) $sifat,
            'tajwid'            => (int) $tajwid,
            'detail_penilaian'  => is_array($detail) ? json_encode($detail) : $detail,
            'catatan'           => $catatan,
            'tanggal_penilaian' => $tgl,
            'penguji_id'        => (int) $pengujiId
        ];

        if ($id) {
            $payload['id'] = $id;
            $this->tahsinModel->save($payload);
            log_activity('Memperbarui Nilai Tahsin Santri', 'Al-Qur\'an', 'ID: ' . $id . ', Santri ID: ' . $santriId);
        } else {
            $newId = $this->tahsinModel->insert($payload);
            log_activity('Menambah Nilai Tahsin Santri', 'Al-Qur\'an', 'ID Baru: ' . $newId . ', Santri ID: ' . $santriId);
        }

        return $this->response->setJSON(['status' => 200, 'message' => 'Data penilaian Tahsin berhasil disimpan']);
    }

    public function deleteTahsin($id)
    {
        $tahsin = $this->tahsinModel->find($id);
        if (!$tahsin) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Data penilaian tidak ditemukan']);
        }

        $this->tahsinModel->delete($id);
        log_activity('Menghapus Nilai Tahsin Santri', 'Al-Qur\'an', 'ID Penilaian: ' . $id);

        return $this->response->setJSON(['status' => 200, 'message' => 'Penilaian Tahsin berhasil dihapus']);
    }

    // ===================== MANAJEMEN TAHFIDZ =====================
    public function indexTahfidz()
    {
        $santriId = $this->request->getGet('santri_id');
        if (empty($santriId)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 400, 'message' => 'Santri ID wajib disertakan']);
        }

        $riwayat = $this->tahfidzModel->getTahfidzBySantri($santriId);
        return $this->response->setJSON([
            'status' => 200,
            'data'   => $riwayat
        ]);
    }

    public function saveTahfidz()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $id           = !empty($data['id']) ? $data['id'] : null;
        $santriId     = $data['santri_id'] ?? '';
        $tipeSetoran  = $data['tipe_setoran'] ?? 'Ziyadah';
        $juz          = $data['juz'] ?? 1;
        $surahMulai   = $data['surah_mulai'] ?? 1;
        $ayatMulai    = $data['ayat_mulai'] ?? 1;
        $surahSelesai = $data['surah_selesai'] ?? 1;
        $ayatSelesai  = $data['ayat_selesai'] ?? 1;
        $predikat     = $data['predikat'] ?? 'Lancar';
        $tglSetor     = $data['tanggal_setor'] ?? date('Y-m-d');
        $pengujiId    = $data['penguji_id'] ?? 1;
        $catatan      = $data['catatan'] ?? '';

        if (empty($santriId)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Santri wajib dipilih']);
        }

        $payload = [
            'santri_id'     => (int) $santriId,
            'tipe_setoran'  => $tipeSetoran,
            'juz'           => (int) $juz,
            'surah_mulai'   => (int) $surahMulai,
            'ayat_mulai'    => (int) $ayatMulai,
            'surah_selesai' => (int) $surahSelesai,
            'ayat_selesai'  => (int) $ayatSelesai,
            'predikat'      => $predikat,
            'tanggal_setor' => $tglSetor,
            'penguji_id'    => (int) $pengujiId,
            'catatan'       => $catatan
        ];

        if ($id) {
            $payload['id'] = $id;
            $this->tahfidzModel->save($payload);
            log_activity('Memperbarui Setoran Tahfidz Santri', 'Al-Qur\'an', 'ID: ' . $id . ', Santri ID: ' . $santriId);
        } else {
            $newId = $this->tahfidzModel->insert($payload);
            log_activity('Menambah Setoran Tahfidz Santri', 'Al-Qur\'an', 'ID Baru: ' . $newId . ', Santri ID: ' . $santriId);
        }

        return $this->response->setJSON(['status' => 200, 'message' => 'Data setoran Tahfidz berhasil disimpan']);
    }

    public function deleteTahfidz($id)
    {
        $tahfidz = $this->tahfidzModel->find($id);
        if (!$tahfidz) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Data setoran tidak ditemukan']);
        }

        $this->tahfidzModel->delete($id);
        log_activity('Menghapus Setoran Tahfidz Santri', 'Al-Qur\'an', 'ID Setoran: ' . $id);

        return $this->response->setJSON(['status' => 200, 'message' => 'Data setoran Tahfidz berhasil dihapus']);
    }

    // ===================== STATISTIK / GRAFIK HAFLAH =====================
    public function stats($santriId)
    {
        $santri = $this->santriModel->find($santriId);
        if (!$santri) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Santri tidak ditemukan']);
        }

        $db = \Config\Database::connect();

        // 1. Hitung total setoran Ziyadah
        $totalZiyadah = $this->tahfidzModel
            ->where('santri_id', $santriId)
            ->where('tipe_setoran', 'Ziyadah')
            ->countAllResults();

        // 2. Hitung total setoran Murajaah
        $totalMurajaah = $this->tahfidzModel
            ->where('santri_id', $santriId)
            ->where('tipe_setoran', 'Murajaah')
            ->countAllResults();

        // 3. Ambil daftar Juz unik yang telah dihafal
        $juzList = $this->tahfidzModel
            ->select('juz')
            ->where('santri_id', $santriId)
            ->where('tipe_setoran', 'Ziyadah')
            ->groupBy('juz')
            ->orderBy('juz', 'ASC')
            ->get()->getResultArray();

        $juzSelesai = array_column($juzList, 'juz');

        // 4. Data setoran harian (10 hari terakhir) untuk Chart
        $historyChart = $this->tahfidzModel
            ->select('tanggal_setor, COUNT(id) as total, tipe_setoran')
            ->where('santri_id', $santriId)
            ->groupBy('tanggal_setor, tipe_setoran')
            ->orderBy('tanggal_setor', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        // Format data chart agar mudah diproses chartjs/frontend
        $chartData = [
            'labels'   => [],
            'ziyadah'  => [],
            'murajaah' => []
        ];

        // Kelompokkan berdasarkan tanggal
        $grouped = [];
        foreach ($historyChart as $row) {
            $tgl = date('d/M', strtotime($row['tanggal_setor']));
            if (!isset($grouped[$tgl])) {
                $grouped[$tgl] = ['Ziyadah' => 0, 'Murajaah' => 0];
            }
            $grouped[$tgl][$row['tipe_setoran']] = (int) $row['total'];
        }

        // Urutkan balik agar kronologis (kiri ke kanan)
        $grouped = array_reverse($grouped);
        foreach ($grouped as $date => $val) {
            $chartData['labels'][] = $date;
            $chartData['ziyadah'][] = $val['Ziyadah'];
            $chartData['murajaah'][] = $val['Murajaah'];
        }

        // 5. Hitung total doa yang lancar/hafal
        $totalDoa = $this->doaModel
            ->where('santri_id', $santriId)
            ->where('status', 'Lancar / Hafal')
            ->countAllResults();

        // 6. Ambil list doa yang pernah disetor beserta statusnya
        $doaList = $this->doaModel
            ->select('nama_doa, status')
            ->where('santri_id', $santriId)
            ->findAll();

        return $this->response->setJSON([
            'status' => 200,
            'data'   => [
                'total_ziyadah'  => $totalZiyadah,
                'total_murajaah' => $totalMurajaah,
                'juz_dihafal'    => $juzSelesai,
                'total_juz'      => count($juzSelesai),
                'total_doa'      => $totalDoa,
                'doa_list'       => $doaList,
                'chart'          => $chartData
            ]
        ]);
    }

    // ===================== MANAJEMEN DOA =====================
    public function indexDoa()
    {
        $santriId = $this->request->getGet('santri_id');
        if (empty($santriId)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 400, 'message' => 'Santri ID wajib disertakan']);
        }

        $riwayat = $this->doaModel->getDoaBySantri($santriId);
        return $this->response->setJSON([
            'status' => 200,
            'data'   => $riwayat
        ]);
    }

    public function saveDoa()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $id        = !empty($data['id']) ? $data['id'] : null;
        $santriId  = $data['santri_id'] ?? '';
        $namaDoa   = $data['nama_doa'] ?? '';
        $status    = $data['status'] ?? 'Lancar / Hafal';
        $tgl       = $data['tanggal_setor'] ?? date('Y-m-d');
        $pengujiId = $data['penguji_id'] ?? 1;
        $catatan   = $data['catatan'] ?? '';

        if (empty($santriId) || empty($namaDoa)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Santri dan Nama Doa wajib diisi']);
        }

        $payload = [
            'santri_id'     => (int) $santriId,
            'nama_doa'      => $namaDoa,
            'status'        => $status,
            'tanggal_setor' => $tgl,
            'penguji_id'    => (int) $pengujiId,
            'catatan'       => $catatan
        ];

        if ($id) {
            $payload['id'] = $id;
            $this->doaModel->save($payload);
            log_activity('Memperbarui Setoran Doa Santri', 'Al-Qur\'an', 'ID: ' . $id . ', Santri ID: ' . $santriId . ', Doa: ' . $namaDoa);
        } else {
            $newId = $this->doaModel->insert($payload);
            log_activity('Menambah Setoran Doa Santri', 'Al-Qur\'an', 'ID Baru: ' . $newId . ', Santri ID: ' . $santriId . ', Doa: ' . $namaDoa);
        }

        return $this->response->setJSON(['status' => 200, 'message' => 'Data setoran doa berhasil disimpan']);
    }

    public function deleteDoa($id)
    {
        $doa = $this->doaModel->find($id);
        if (!$doa) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Data setoran doa tidak ditemukan']);
        }

        $this->doaModel->delete($id);
        log_activity('Menghapus Setoran Doa Santri', 'Al-Qur\'an', 'ID: ' . $id . ', Santri ID: ' . $doa['santri_id']);
        return $this->response->setJSON(['status' => 200, 'message' => 'Data setoran doa berhasil dihapus']);
    }

    // ===================== MASTER DATA LIST DOA =====================
    public function indexMasterDoa()
    {
        $list = $this->masterDoaModel->orderBy('nama_doa', 'ASC')->findAll();
        return $this->response->setJSON([
            'status' => 200,
            'data'   => $list
        ]);
    }

    public function saveMasterDoa()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $id      = !empty($data['id']) ? $data['id'] : null;
        $namaDoa = $data['nama_doa'] ?? '';

        if (empty($namaDoa)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Nama doa wajib diisi']);
        }

        $payload = [
            'nama_doa' => $namaDoa
        ];

        if ($id) {
            $payload['id'] = $id;
            $this->masterDoaModel->save($payload);
            log_activity('Memperbarui Master Doa', 'Al-Qur\'an', 'ID: ' . $id . ', Nama Doa: ' . $namaDoa);
        } else {
            // Cek duplikasi
            $existing = $this->masterDoaModel->where('nama_doa', $namaDoa)->first();
            if ($existing) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 400, 'message' => 'Nama doa tersebut sudah ada di daftar']);
            }
            $this->masterDoaModel->insert($payload);
            log_activity('Menambah Master Doa Baru', 'Al-Qur\'an', 'Nama Doa: ' . $namaDoa);
        }

        return $this->response->setJSON(['status' => 200, 'message' => 'Daftar doa berhasil disimpan']);
    }

    public function deleteMasterDoa($id)
    {
        $master = $this->masterDoaModel->find($id);
        if (!$master) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Data doa tidak ditemukan']);
        }

        $this->masterDoaModel->delete($id);
        log_activity('Menghapus Master Doa', 'Al-Qur\'an', 'ID: ' . $id . ', Nama Doa: ' . $master['nama_doa']);
        return $this->response->setJSON(['status' => 200, 'message' => 'Daftar doa berhasil dihapus']);
    }
}
