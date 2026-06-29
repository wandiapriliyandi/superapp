<?php

namespace Akademik\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\SantriModel;
use App\Models\KelasModel;
use App\Models\MapelModel;
use App\Models\JadwalModel;
use App\Models\PresensiModel;
use App\Models\NilaiModel;

class Akademik extends BaseController
{
    protected $santriModel;
    protected $kelasModel;
    protected $mapelModel;
    protected $jadwalModel;
    protected $presensiModel;
    protected $nilaiModel;

    public function __construct()
    {
        $this->santriModel   = new SantriModel();
        $this->kelasModel    = new KelasModel();
        $this->mapelModel    = new MapelModel();
        $this->jadwalModel   = new JadwalModel();
        $this->presensiModel = new PresensiModel();
        $this->nilaiModel    = new NilaiModel();
    }

    // ===================== SANTRI =====================

    public function indexSantri()
    {
        $q      = $this->request->getGet('q');
        $jk     = $this->request->getGet('jk');
        $status = $this->request->getGet('status');

        $query = $this->santriModel
            ->select('santri.*, tahun_ajaran.tahun_ajaran as nama_tahun_ajaran')
            ->join('tahun_ajaran', 'tahun_ajaran.id = santri.id_tahun_ajaran', 'left');

        if ($q)      $query->like('santri.nama_lengkap', $q);
        if ($jk)     $query->where('santri.jenis_kelamin', $jk);
        if ($status) $query->where('santri.status_santri', $status);

        return $this->response->setJSON([
            'status'  => 200,
            'message' => 'Data santri berhasil diambil',
            'data'    => $query->orderBy('santri.nama_lengkap', 'ASC')->findAll()
        ]);
    }

    public function showSantri($id)
    {
        $santri = $this->santriModel
            ->select('santri.*, tahun_ajaran.tahun_ajaran as nama_tahun_ajaran')
            ->join('tahun_ajaran', 'tahun_ajaran.id = santri.id_tahun_ajaran', 'left')
            ->find($id);

        if (!$santri) return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Santri tidak ditemukan']);

        return $this->response->setJSON(['status' => 200, 'data' => $santri]);
    }

    public function saveSantri()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data['nama_lengkap'])) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Nama lengkap wajib diisi']);
        }

        $isUpdate = !empty($data['id']);
        $this->santriModel->save($data);

        $aksi = $isUpdate ? 'Mengubah' : 'Menambah';
        log_activity($aksi . ' Data Santri (API)', 'Akademik', 'Nama: ' . $data['nama_lengkap']);

        return $this->response->setJSON(['status' => 200, 'message' => 'Data santri berhasil disimpan']);
    }

    public function deleteSantri($id)
    {
        helper('activity');
        $santri = $this->santriModel->find($id);
        if (!$santri) return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Santri tidak ditemukan']);

        $this->santriModel->delete($id);
        log_activity('Menghapus Data Santri (API)', 'Akademik', 'Nama: ' . $santri['nama_lengkap']);
        return $this->response->setJSON(['status' => 200, 'message' => 'Data santri berhasil dihapus']);
    }

    // ===================== KELAS =====================

    public function indexKelas()
    {
        $db    = \Config\Database::connect();
        $kelas = $db->table('akademik_kelas k')
                    ->select('k.*, p.nama_lengkap as nama_wali_kelas')
                    ->join('hr_pegawai p', 'p.id = k.id_wali_kelas', 'left')
                    ->orderBy('k.tingkat', 'ASC')
                    ->get()->getResultArray();

        return $this->response->setJSON(['status' => 200, 'message' => 'Data kelas berhasil diambil', 'data' => $kelas]);
    }

    public function saveKelas()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data['nama_kelas'])) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Nama kelas wajib diisi']);
        }

        $this->kelasModel->save([
            'id'            => !empty($data['id']) ? $data['id'] : null,
            'nama_kelas'    => $data['nama_kelas'],
            'tingkat'       => !empty($data['tingkat']) ? $data['tingkat'] : null,
            'id_wali_kelas' => !empty($data['id_wali_kelas']) ? (int) $data['id_wali_kelas'] : null,
        ]);

        log_activity('Menyimpan Data Kelas (API)', 'Akademik', 'Nama Kelas: ' . $data['nama_kelas']);
        return $this->response->setJSON(['status' => 200, 'message' => 'Data kelas berhasil disimpan']);
    }

    public function deleteKelas($id)
    {
        helper('activity');
        $kelas = $this->kelasModel->find($id);
        if (!$kelas) return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Kelas tidak ditemukan']);

        $this->kelasModel->delete($id);
        log_activity('Menghapus Data Kelas (API)', 'Akademik', 'Nama Kelas: ' . $kelas['nama_kelas']);
        return $this->response->setJSON(['status' => 200, 'message' => 'Data kelas berhasil dihapus']);
    }

    // ===================== MAPEL =====================

    public function indexMapel()
    {
        $data = $this->mapelModel->orderBy('nama_mapel', 'ASC')->findAll();
        return $this->response->setJSON(['status' => 200, 'data' => $data]);
    }

    public function saveMapel()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data['nama_mapel']) || empty($data['kode_mapel'])) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Kode & nama mapel wajib diisi']);
        }

        $this->mapelModel->save($data);
        log_activity('Menyimpan Mata Pelajaran (API)', 'Akademik', 'Nama Mapel: ' . $data['nama_mapel']);

        return $this->response->setJSON(['status' => 200, 'message' => 'Mata pelajaran berhasil disimpan']);
    }

    public function deleteMapel($id)
    {
        helper('activity');
        $mapel = $this->mapelModel->find($id);
        if (!$mapel) return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Mapel tidak ditemukan']);

        $this->mapelModel->delete($id);
        log_activity('Menghapus Mata Pelajaran (API)', 'Akademik', 'Nama: ' . $mapel['nama_mapel']);
        return $this->response->setJSON(['status' => 200, 'message' => 'Mata pelajaran berhasil dihapus']);
    }

    // ===================== JADWAL =====================

    public function indexJadwal()
    {
        $id_kelas = $this->request->getGet('kelas');
        $data     = $this->jadwalModel->getJadwalLengkap($id_kelas);
        return $this->response->setJSON(['status' => 200, 'data' => $data]);
    }

    public function saveJadwal()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data['id_kelas']) || empty($data['id_mapel']) || empty($data['hari'])) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Kelas, mapel, dan hari wajib diisi']);
        }

        $this->jadwalModel->save($data);
        log_activity('Menyimpan Jadwal Pelajaran (API)', 'Akademik', 'Hari: ' . $data['hari']);

        return $this->response->setJSON(['status' => 200, 'message' => 'Jadwal pelajaran berhasil disimpan']);
    }

    public function deleteJadwal($id)
    {
        helper('activity');
        $jadwal = $this->jadwalModel->find($id);
        if (!$jadwal) return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Jadwal tidak ditemukan']);

        $this->jadwalModel->delete($id);
        log_activity('Menghapus Jadwal Pelajaran (API)', 'Akademik', 'Hari: ' . $jadwal['hari']);
        return $this->response->setJSON(['status' => 200, 'message' => 'Jadwal berhasil dihapus']);
    }

    // ===================== PRESENSI =====================

    public function indexPresensi()
    {
        $id_jadwal = $this->request->getGet('id_jadwal');
        $tanggal   = $this->request->getGet('tanggal') ?? date('Y-m-d');

        if (!$id_jadwal) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Jadwal ID wajib disertakan']);
        }

        // Ambil semua santri aktif
        $santri = $this->santriModel->where('status_santri', 'Aktif')->orderBy('nama_lengkap', 'ASC')->findAll();
        
        // Ambil presensi yang sudah tercatat
        $presensi = $this->presensiModel->where([
            'id_jadwal' => $id_jadwal,
            'tanggal'   => $tanggal
        ])->findAll();

        $statusMap = [];
        foreach ($presensi as $p) {
            $statusMap[$p['nisn']] = ['status' => $p['status'], 'catatan' => $p['catatan']];
        }

        $data = [];
        foreach ($santri as $s) {
            $data[] = [
                'nisn'         => $s['nisn'],
                'nama_lengkap' => $s['nama_lengkap'],
                'status'       => $statusMap[$s['nisn']]['status'] ?? 'Hadir',
                'catatan'      => $statusMap[$s['nisn']]['catatan'] ?? '',
            ];
        }

        return $this->response->setJSON(['status' => 200, 'data' => $data]);
    }

    public function savePresensi()
    {
        helper('activity');
        $data      = $this->request->getJSON(true) ?? $this->request->getPost();
        $id_jadwal = $data['id_jadwal'] ?? null;
        $tanggal   = $data['tanggal']   ?? date('Y-m-d');
        $records   = $data['records']   ?? []; // Array of {nisn, status, catatan}

        if (!$id_jadwal) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Jadwal ID wajib diisi']);
        }

        foreach ($records as $r) {
            $existing = $this->presensiModel->where([
                'id_jadwal' => $id_jadwal,
                'nisn'      => $r['nisn'],
                'tanggal'   => $tanggal
            ])->first();

            $saveData = [
                'id_jadwal' => $id_jadwal,
                'nisn'      => $r['nisn'],
                'tanggal'   => $tanggal,
                'status'    => $r['status']  ?? 'Hadir',
                'catatan'   => $r['catatan'] ?? '',
            ];

            if ($existing) {
                $this->presensiModel->update($existing['id'], $saveData);
            } else {
                $this->presensiModel->save($saveData);
            }
        }

        log_activity('Menginput Presensi Santri (API)', 'Akademik', 'Jadwal ID: ' . $id_jadwal . ', Total: ' . count($records));
        return $this->response->setJSON(['status' => 200, 'message' => 'Presensi berhasil disimpan']);
    }

    // ===================== NILAI =====================

    public function indexNilai()
    {
        $id_mapel        = $this->request->getGet('id_mapel');
        $id_tahun_ajaran = $this->request->getGet('id_tahun_ajaran');

        if (!$id_mapel || !$id_tahun_ajaran) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Mapel dan tahun ajaran wajib disertakan']);
        }

        $santri = $this->santriModel->where('status_santri', 'Aktif')->orderBy('nama_lengkap', 'ASC')->findAll();
        
        $nilai = $this->nilaiModel->where([
            'id_mapel'        => $id_mapel,
            'id_tahun_ajaran' => $id_tahun_ajaran
        ])->findAll();

        $nilaiMap = [];
        foreach ($nilai as $n) {
            $nilaiMap[$n['nisn']] = $n;
        }

        $data = [];
        foreach ($santri as $s) {
            $n = $nilaiMap[$s['nisn']] ?? null;
            $data[] = [
                'nisn'         => $s['nisn'],
                'nama_lengkap' => $s['nama_lengkap'],
                'nilai_tugas'  => $n ? (float) $n['nilai_tugas'] : '',
                'nilai_uts'    => $n ? (float) $n['nilai_uts'] : '',
                'nilai_uas'    => $n ? (float) $n['nilai_uas'] : '',
                'nilai_akhir'  => $n ? (float) $n['nilai_akhir'] : '—',
                'predikat'     => $n ? $n['predikat'] : '—',
                'keterangan'   => $n ? $n['keterangan'] : '',
            ];
        }

        return $this->response->setJSON(['status' => 200, 'data' => $data]);
    }

    public function saveNilai()
    {
        helper('activity');
        $data            = $this->request->getJSON(true) ?? $this->request->getPost();
        $id_mapel        = $data['id_mapel'] ?? null;
        $id_tahun_ajaran = $data['id_tahun_ajaran'] ?? null;
        $records         = $data['records'] ?? []; // Array of {nisn, tugas, uts, uas, keterangan}

        if (!$id_mapel || !$id_tahun_ajaran) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Mapel dan tahun ajaran wajib diisi']);
        }

        foreach ($records as $r) {
            $tugas = $r['nilai_tugas'] !== '' ? (float) $r['nilai_tugas'] : 0;
            $uts   = $r['nilai_uts']   !== '' ? (float) $r['nilai_uts']   : 0;
            $uas   = $r['nilai_uas']   !== '' ? (float) $r['nilai_uas']   : 0;
            
            $nilai_akhir = ($tugas * 0.4) + ($uts * 0.3) + ($uas * 0.3);
            
            $predikat = 'E';
            if ($nilai_akhir >= 85)     $predikat = 'A';
            elseif ($nilai_akhir >= 75) $predikat = 'B';
            elseif ($nilai_akhir >= 65) $predikat = 'C';
            elseif ($nilai_akhir >= 50) $predikat = 'D';

            $existing = $this->nilaiModel->where([
                'nisn'            => $r['nisn'],
                'id_mapel'        => $id_mapel,
                'id_tahun_ajaran' => $id_tahun_ajaran
            ])->first();

            $saveData = [
                'nisn'            => $r['nisn'],
                'id_mapel'        => $id_mapel,
                'id_tahun_ajaran' => $id_tahun_ajaran,
                'nilai_tugas'     => $tugas,
                'nilai_uts'       => $uts,
                'nilai_uas'       => $uas,
                'nilai_akhir'     => $nilai_akhir,
                'predikat'        => $predikat,
                'keterangan'      => $r['keterangan'] ?? ''
            ];

            if ($existing) {
                $this->nilaiModel->update($existing['id'], $saveData);
            } else {
                $this->nilaiModel->save($saveData);
            }
        }

        log_activity('Menginput Nilai Santri (API)', 'Akademik', 'Mapel ID: ' . $id_mapel . ', Total: ' . count($records));
        return $this->response->setJSON(['status' => 200, 'message' => 'Nilai berhasil disimpan']);
    }

    // ===================== REFERENSI =====================

    public function listTahunAjaran()
    {
        $taModel = new \App\Models\TahunAjaranModel();
        return $this->response->setJSON([
            'status' => 200,
            'data'   => $taModel->orderBy('tahun_ajaran', 'DESC')->findAll()
        ]);
    }

    public function listGuru()
    {
        $db   = \Config\Database::connect();
        $data = $db->table('hr_pegawai')
                   ->select('id, nama_lengkap')
                   ->orderBy('nama_lengkap', 'ASC')
                   ->get()->getResultArray();
        
        return $this->response->setJSON(['status' => 200, 'data' => $data]);
    }
}
