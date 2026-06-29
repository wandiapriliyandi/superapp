<?php

namespace Kepegawaian\Controllers\Api;

use App\Controllers\BaseController;
use Kepegawaian\Models\PegawaiModel;
use Kepegawaian\Models\DepartemenModel;
use Kepegawaian\Models\JabatanModel;
use Kepegawaian\Models\AbsensiModel;
use Kepegawaian\Models\CutiModel;
use Kepegawaian\Models\PayrollModel;

class Kepegawaian extends BaseController
{
    protected $pegawaiModel;
    protected $deptModel;
    protected $jabatanModel;
    protected $absensiModel;
    protected $cutiModel;
    protected $payrollModel;

    public function __construct()
    {
        $this->pegawaiModel = new PegawaiModel();
        $this->deptModel    = new DepartemenModel();
        $this->jabatanModel = new JabatanModel();
        $this->absensiModel = new AbsensiModel();
        $this->cutiModel    = new CutiModel();
        $this->payrollModel = new PayrollModel();
    }

    // ===================== PEGAWAI =====================

    public function indexPegawai()
    {
        $q = $this->request->getGet('q');
        $dept = $this->request->getGet('dept');

        $query = $this->pegawaiModel
            ->select('hr_pegawai.*, hr_departemen.nama_departemen, hr_jabatan.nama_jabatan, hr_jabatan.gaji_pokok, hr_jabatan.tunjangan_makan, hr_jabatan.tunjangan_transport')
            ->join('hr_departemen', 'hr_departemen.id = hr_pegawai.departemen_id', 'left')
            ->join('hr_jabatan', 'hr_jabatan.id = hr_pegawai.jabatan_id', 'left');

        if ($q)    $query->like('hr_pegawai.nama_lengkap', $q);
        if ($dept) $query->where('hr_pegawai.departemen_id', $dept);

        return $this->response->setJSON([
            'status'  => 200,
            'message' => 'Data pegawai berhasil diambil',
            'data'    => $query->findAll()
        ]);
    }

    public function savePegawai()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data['nama_lengkap'])) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Nama lengkap wajib diisi']);
        }

        $payload = [
            'id'             => !empty($data['id']) ? $data['id'] : null,
            'nik'            => !empty($data['nik']) ? $data['nik'] : null,
            'nama_lengkap'   => $data['nama_lengkap'],
            'tempat_lahir'   => !empty($data['tempat_lahir']) ? $data['tempat_lahir'] : null,
            'tanggal_lahir'  => !empty($data['tanggal_lahir']) ? $data['tanggal_lahir'] : null,
            'jenis_kelamin'  => !empty($data['jenis_kelamin']) ? $data['jenis_kelamin'] : 'L',
            'no_hp'          => !empty($data['no_hp']) ? $data['no_hp'] : null,
            'email'          => !empty($data['email']) ? $data['email'] : null,
            'departemen_id'  => !empty($data['departemen_id']) ? (int) $data['departemen_id'] : null,
            'jabatan_id'     => !empty($data['jabatan_id']) ? (int) $data['jabatan_id'] : null,
            'status_pegawai' => !empty($data['status_pegawai']) ? $data['status_pegawai'] : 'Aktif',
            'tanggal_masuk'  => !empty($data['tanggal_masuk']) ? $data['tanggal_masuk'] : null,
            'foto'           => 'default.png',
        ];

        $isUpdate = !empty($data['id']);
        $this->pegawaiModel->save($payload);

        $aksi = $isUpdate ? 'Mengubah' : 'Menambah';
        log_activity($aksi . ' Data Pegawai (API)', 'Kepegawaian', 'Nama: ' . $data['nama_lengkap']);

        return $this->response->setJSON(['status' => 200, 'message' => 'Pegawai berhasil disimpan']);
    }

    public function deletePegawai($id)
    {
        helper('activity');
        $pegawai = $this->pegawaiModel->find($id);
        if (!$pegawai) return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Pegawai tidak ditemukan']);

        $this->pegawaiModel->delete($id);
        log_activity('Menghapus Data Pegawai (API)', 'Kepegawaian', 'Nama: ' . $pegawai['nama_lengkap']);
        return $this->response->setJSON(['status' => 200, 'message' => 'Pegawai berhasil dihapus']);
    }

    // ===================== JABATAN =====================

    public function indexJabatan()
    {
        return $this->response->setJSON([
            'status'  => 200,
            'message' => 'Data jabatan berhasil diambil',
            'data'    => $this->jabatanModel->findAll()
        ]);
    }

    public function saveJabatan()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        if (empty($data['nama_jabatan'])) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Nama jabatan wajib diisi']);
        }

        $this->jabatanModel->save([
            'id'                   => !empty($data['id']) ? $data['id'] : null,
            'nama_jabatan'         => $data['nama_jabatan'],
            'gaji_pokok'           => !empty($data['gaji_pokok']) ? (float) $data['gaji_pokok'] : 0,
            'tunjangan_makan'      => !empty($data['tunjangan_makan']) ? (float) $data['tunjangan_makan'] : 0,
            'tunjangan_transport'  => !empty($data['tunjangan_transport']) ? (float) $data['tunjangan_transport'] : 0,
        ]);

        log_activity('Menyimpan Data Jabatan (API)', 'Kepegawaian', 'Nama: ' . $data['nama_jabatan']);
        return $this->response->setJSON(['status' => 200, 'message' => 'Jabatan berhasil disimpan']);
    }

    public function deleteJabatan($id)
    {
        helper('activity');
        $jabatan = $this->jabatanModel->find($id);
        if (!$jabatan) return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Jabatan tidak ditemukan']);

        $this->jabatanModel->delete($id);
        log_activity('Menghapus Data Jabatan (API)', 'Kepegawaian', 'Nama: ' . $jabatan['nama_jabatan']);
        return $this->response->setJSON(['status' => 200, 'message' => 'Jabatan berhasil dihapus']);
    }

    // ===================== ABSENSI =====================

    public function indexAbsensi()
    {
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');
        
        $pegawai  = $this->pegawaiModel->where('status_pegawai', 'Aktif')->orderBy('nama_lengkap', 'ASC')->findAll();
        $absensi = $this->absensiModel->where('tanggal', $tanggal)->findAll();

        $absensiMap = [];
        foreach ($absensi as $a) {
            $absensiMap[$a['pegawai_id']] = $a;
        }

        $data = [];
        foreach ($pegawai as $p) {
            $a = $absensiMap[$p['id']] ?? null;
            $data[] = [
                'pegawai_id'   => $p['id'],
                'nama_lengkap' => $p['nama_lengkap'],
                'nik'          => $p['nik'],
                'tanggal'      => $tanggal,
                'jam_masuk'    => $a ? $a['jam_masuk'] : '',
                'jam_pulang'   => $a ? $a['jam_pulang'] : '',
                'status'       => $a ? $a['status'] : 'Hadir',
                'keterangan'   => $a ? $a['keterangan'] : '',
            ];
        }

        return $this->response->setJSON(['status' => 200, 'data' => $data]);
    }

    public function saveAbsensi()
    {
        helper('activity');
        $data    = $this->request->getJSON(true) ?? $this->request->getPost();
        $tanggal = $data['tanggal'] ?? date('Y-m-d');
        $records = $data['records'] ?? [];

        foreach ($records as $r) {
            $existing = $this->absensiModel->where([
                'pegawai_id' => $r['pegawai_id'],
                'tanggal'    => $tanggal
            ])->first();

            $saveData = [
                'pegawai_id' => $r['pegawai_id'],
                'tanggal'    => $tanggal,
                'jam_masuk'  => $r['jam_masuk']  ?: null,
                'jam_pulang' => $r['jam_pulang'] ?: null,
                'status'     => $r['status']     ?? 'Hadir',
                'keterangan' => $r['keterangan'] ?? '',
            ];

            if ($existing) {
                $this->absensiModel->update($existing['id'], $saveData);
            } else {
                $this->absensiModel->save($saveData);
            }
        }

        log_activity('Menginput Absensi Pegawai (API)', 'Kepegawaian', 'Tanggal: ' . $tanggal . ', Total: ' . count($records));
        return $this->response->setJSON(['status' => 200, 'message' => 'Absensi berhasil disimpan']);
    }

    // ===================== CUTI =====================

    public function indexCuti()
    {
        return $this->response->setJSON([
            'status' => 200,
            'data'   => $this->cutiModel->getCutiFull()
        ]);
    }

    public function saveCuti()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data['pegawai_id']) || empty($data['tanggal_mulai']) || empty($data['tanggal_selesai'])) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Pegawai, tgl mulai, dan tgl selesai wajib diisi']);
        }

        $this->cutiModel->save([
            'pegawai_id'      => $data['pegawai_id'],
            'jenis_cuti'      => $data['jenis_cuti'] ?? 'Tahunan',
            'tanggal_mulai'   => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'alasan'          => $data['alasan'] ?? '',
            'status'          => 'Pending'
        ]);

        $pegawai = $this->pegawaiModel->find($data['pegawai_id']);
        log_activity('Mengajukan Cuti Pegawai (API)', 'Kepegawaian', 'Pegawai: ' . ($pegawai['nama_lengkap'] ?? ''));

        return $this->response->setJSON(['status' => 200, 'message' => 'Cuti berhasil diajukan']);
    }

    public function setCutiStatus($id, $status)
    {
        helper('activity');
        $map = ['approve' => 'Disetujui', 'reject' => 'Ditolak'];
        if (!isset($map[$status])) return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Status tidak valid']);

        $cuti = $this->cutiModel->find($id);
        if (!$cuti) return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Data cuti tidak ditemukan']);

        $newStatus = $map[$status];
        $this->cutiModel->update($id, [
            'status' => $newStatus,
            'disetujui_oleh' => 'Administrator'
        ]);

        log_activity('Ubah Status Cuti Pegawai (API)', 'Kepegawaian', 'Status: ' . $newStatus);
        return $this->response->setJSON(['status' => 200, 'message' => 'Status cuti diubah menjadi ' . $newStatus]);
    }

    // ===================== PAYROLL =====================

    public function indexPayroll()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        return $this->response->setJSON([
            'status' => 200,
            'data'   => $this->payrollModel->getPayrollFull($bulan, $tahun)
        ]);
    }

    public function generatePayroll()
    {
        helper('activity');
        $data  = $this->request->getJSON(true) ?? $this->request->getPost();
        $bulan = $data['bulan'] ?? date('m');
        $tahun = $data['tahun'] ?? date('Y');

        // Ambil semua pegawai aktif dengan data jabatannya
        $pegawai = $this->pegawaiModel
            ->select('hr_pegawai.*, jabatan.gaji_pokok, jabatan.tunjangan_makan, jabatan.tunjangan_transport')
            ->join('jabatan', 'jabatan.id = hr_pegawai.jabatan_id', 'left')
            ->where('status_pegawai', 'Aktif')
            ->findAll();

        $count = 0;
        foreach ($pegawai as $p) {
            $exist = $this->payrollModel->where([
                'pegawai_id' => $p['id'],
                'bulan'      => $bulan,
                'tahun'      => $tahun
            ])->first();

            if (!$exist) {
                $gaji_pokok = (float) ($p['gaji_pokok'] ?? 0);
                $tunjangan  = (float) ($p['tunjangan_makan'] ?? 0) + (float) ($p['tunjangan_transport'] ?? 0);
                
                $this->payrollModel->save([
                    'pegawai_id'      => $p['id'],
                    'bulan'           => $bulan,
                    'tahun'           => $tahun,
                    'gaji_pokok'      => $gaji_pokok,
                    'total_tunjangan' => $tunjangan,
                    'potongan'        => 0,
                    'gaji_bersih'     => $gaji_pokok + $tunjangan,
                    'status_bayar'    => 'Belum Dibayar'
                ]);
                $count++;
            }
        }

        log_activity('Generate Payroll (API)', 'Kepegawaian', 'Bulan: ' . $bulan . ', Tahun: ' . $tahun . ', Total: ' . $count);
        return $this->response->setJSON(['status' => 200, 'message' => "Slip gaji berhasil digenerate untuk $count pegawai."]);
    }

    public function bayarPayroll($id)
    {
        helper('activity');
        $payroll = $this->payrollModel->find($id);
        if (!$payroll) return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Slip gaji tidak ditemukan']);

        $this->payrollModel->update($id, [
            'status_bayar'  => 'Dibayar',
            'tanggal_bayar' => date('Y-m-d')
        ]);

        log_activity('Membayar Gaji Pegawai (API)', 'Kepegawaian', 'Slip ID: ' . $id);
        return $this->response->setJSON(['status' => 200, 'message' => 'Gaji berhasil dibayarkan']);
    }
}
