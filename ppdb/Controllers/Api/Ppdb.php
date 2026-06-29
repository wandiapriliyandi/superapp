<?php

namespace Ppdb\Controllers\Api;

use App\Controllers\BaseController;
use Ppdb\Models\PendaftarModel;
use Ppdb\Models\PpdbPembayaranModel;

class Ppdb extends BaseController
{
    protected $pendaftarModel;

    public function __construct()
    {
        $this->pendaftarModel = new PendaftarModel();
    }

    // ===================== PENDAFTAR =====================

    public function indexPendaftar()
    {
        $page   = $this->request->getVar('page') ? (int) $this->request->getVar('page') : 1;
        $limit  = $this->request->getVar('limit') ? (int) $this->request->getVar('limit') : 10;
        $status = $this->request->getVar('status');
        $q      = $this->request->getVar('q');

        $db = \Config\Database::connect();
        $countBuilder = $db->table('ppdb_pendaftar');

        if ($status) $countBuilder->where('status_seleksi', $status);
        if ($q)      $countBuilder->like('nama_lengkap', $q);
        $total = $countBuilder->countAllResults();

        $totalPages = ceil($total / $limit);
        $offset = ($page - 1) * $limit;

        $mainBuilder = $db->table('ppdb_pendaftar');
        $mainBuilder->select('ppdb_pendaftar.*, tahun_ajaran.tahun_ajaran as nama_tahun');
        $mainBuilder->join('tahun_ajaran', 'tahun_ajaran.id = ppdb_pendaftar.id_tahun_ajaran', 'left');

        if ($status) $mainBuilder->where('status_seleksi', $status);
        if ($q)      $mainBuilder->like('nama_lengkap', $q);

        $data = $mainBuilder->orderBy('id', 'DESC')->limit($limit, $offset)->get()->getResultArray();

        return $this->response->setJSON([
            'status'  => 200,
            'message' => 'Data pendaftar berhasil diambil',
            'data'    => $data,
            'pagination' => [
                'total'       => $total,
                'page'        => $page,
                'limit'       => $limit,
                'total_pages' => $totalPages
            ]
        ]);
    }

    public function showPendaftar($id)
    {
        $pendaftar = $this->pendaftarModel->find($id);
        if (!$pendaftar) return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Pendaftar tidak ditemukan']);

        $db = \Config\Database::connect();

        // Riwayat Tes
        $tes = $db->table('ppdb_peserta_tes pt')
                  ->select('pt.*, jt.nama_tes, jt.tanggal, jt.jam, jt.tempat')
                  ->join('ppdb_jadwal_tes jt', 'jt.id = pt.id_jadwal')
                  ->where('pt.id_pendaftar', $id)
                  ->get()->getResultArray();

        // Riwayat Berkas
        $berkas = $db->table('pendaftar_berkas')
                     ->where('id_pendaftar', $id)
                     ->orderBy('updated_at', 'DESC')
                     ->get()->getResultArray();

        // Riwayat Pembayaran
        $pembayaran = $db->table('ppdb_pembayaran')
                         ->where('id_pendaftar', $id)
                         ->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 200,
            'data'   => array_merge($pendaftar, [
                'riwayat_tes'       => $tes,
                'riwayat_berkas'    => $berkas,
                'riwayat_pembayaran'=> $pembayaran
            ])
        ]);
    }

    public function savePendaftar()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data['nama_lengkap'])) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Nama lengkap wajib diisi']);
        }

        $nomorPendaftaran = $this->pendaftarModel->generateNomor();

        $payload = [
            'nomor_pendaftaran' => $nomorPendaftaran,
            'nama_lengkap'      => $data['nama_lengkap'],
            'jenis_kelamin'     => $data['jenis_kelamin'] ?? null,
            'tempat_lahir'      => $data['tempat_lahir'] ?? null,
            'tanggal_lahir'     => $data['tanggal_lahir'] ?? null,
            'alamat'            => $data['alamat'] ?? null,
            'no_hp_ortu'        => $data['no_hp_ortu'] ?? null,
            'status_seleksi'    => $data['status_seleksi'] ?? 'Pending',
            'id_tahun_ajaran'   => $data['id_tahun_ajaran'] ?? null,
        ];

        $this->pendaftarModel->insert($payload);
        log_activity('Menambah Pendaftar Baru (API)', 'PPDB', 'Nama: ' . $data['nama_lengkap']);

        return $this->response->setJSON(['status' => 200, 'message' => 'Pendaftar berhasil ditambahkan. No: ' . $nomorPendaftaran, 'nomor' => $nomorPendaftaran]);
    }

    public function setStatus($id, $status)
    {
        helper('activity');
        $map = [
            'lulus'     => 'Lulus',
            'gagal'     => 'Tidak Lulus',
            'pending'   => 'Pending',
            'terdaftar' => 'Santri Terdaftar'
        ];

        if (!isset($map[$status])) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Status tidak valid']);
        }

        $pendaftar = $this->pendaftarModel->find($id);
        if (!$pendaftar) return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Pendaftar tidak ditemukan']);

        $newStatus = $map[$status];
        $this->pendaftarModel->update($id, ['status_seleksi' => $newStatus]);

        if ($newStatus === 'Santri Terdaftar') {
            $santriModel = new \App\Models\SantriModel();
            $existing    = $santriModel->where([
                'nama_lengkap'  => $pendaftar['nama_lengkap'],
                'tanggal_lahir' => $pendaftar['tanggal_lahir']
            ])->first();

            if (!$existing) {
                $santriModel->insert([
                    'nama_lengkap'    => $pendaftar['nama_lengkap'],
                    'jenis_kelamin'   => $pendaftar['jenis_kelamin'],
                    'tempat_lahir'    => $pendaftar['tempat_lahir'],
                    'tanggal_lahir'   => $pendaftar['tanggal_lahir'],
                    'alamat'          => $pendaftar['alamat'],
                    'no_hp'           => $pendaftar['no_hp_ortu'],
                    'status_santri'   => 'Aktif',
                    'id_tahun_ajaran' => $pendaftar['id_tahun_ajaran'],
                ]);
                log_activity('Migrasi Pendaftar ke Santri (API)', 'Akademik', 'Nama: ' . $pendaftar['nama_lengkap']);
            }
        }

        log_activity('Mengubah Status Seleksi (API)', 'PPDB', 'Status: ' . $newStatus . ' - ' . $pendaftar['nama_lengkap']);
        return $this->response->setJSON(['status' => 200, 'message' => 'Status diubah menjadi ' . $newStatus]);
    }

    public function deletePendaftar($id)
    {
        helper('activity');
        $pendaftar = $this->pendaftarModel->find($id);
        if (!$pendaftar) return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Pendaftar tidak ditemukan']);

        $this->pendaftarModel->delete($id);
        log_activity('Menghapus Data Pendaftar (API)', 'PPDB', 'Nama: ' . $pendaftar['nama_lengkap']);
        return $this->response->setJSON(['status' => 200, 'message' => 'Pendaftar berhasil dihapus']);
    }

    // ===================== JADWAL TES =====================

    public function indexJadwal()
    {
        $db = \Config\Database::connect();
        $jadwal = $db->table('ppdb_jadwal_tes jt')
                     ->select('jt.*, COUNT(pt.id) as jumlah_peserta')
                     ->join('ppdb_peserta_tes pt', 'pt.id_jadwal = jt.id', 'left')
                     ->groupBy('jt.id')
                     ->orderBy('jt.tanggal', 'DESC')
                     ->get()->getResultArray();

        return $this->response->setJSON(['status' => 200, 'data' => $jadwal]);
    }

    public function saveJadwal()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data['nama_tes'])) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Nama tes wajib diisi']);
        }

        $db = \Config\Database::connect();
        $payload = [
            'nama_tes' => $data['nama_tes'],
            'tanggal'  => $data['tanggal']  ?? null,
            'jam'      => $data['jam']      ?? null,
            'tempat'   => $data['tempat']   ?? null,
            'kuota'    => $data['kuota']    ?? 0,
        ];

        if (!empty($data['id'])) {
            $db->table('ppdb_jadwal_tes')->where('id', $data['id'])->update($payload);
            log_activity('Update Jadwal Tes (API)', 'PPDB', 'ID: ' . $data['id']);
        } else {
            $db->table('ppdb_jadwal_tes')->insert($payload);
            log_activity('Tambah Jadwal Tes (API)', 'PPDB', 'Nama: ' . $data['nama_tes']);
        }

        return $this->response->setJSON(['status' => 200, 'message' => 'Jadwal tes berhasil disimpan']);
    }

    public function deleteJadwal($id)
    {
        helper('activity');
        $db = \Config\Database::connect();
        $db->table('ppdb_peserta_tes')->where('id_jadwal', $id)->delete();
        $db->table('ppdb_jadwal_tes')->where('id', $id)->delete();
        log_activity('Hapus Jadwal Tes (API)', 'PPDB', 'ID: ' . $id);
        return $this->response->setJSON(['status' => 200, 'message' => 'Jadwal tes berhasil dihapus']);
    }

    public function pesertaJadwal($id_jadwal)
    {
        $db = \Config\Database::connect();
        $peserta = $db->table('ppdb_peserta_tes pt')
                      ->select('pt.*, p.nama_lengkap, p.nomor_pendaftaran')
                      ->join('ppdb_pendaftar p', 'p.id = pt.id_pendaftar')
                      ->where('pt.id_jadwal', $id_jadwal)
                      ->get()->getResultArray();

        return $this->response->setJSON(['status' => 200, 'data' => $peserta]);
    }

    public function addPeserta()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $db   = \Config\Database::connect();

        // Cek duplikat
        $exists = $db->table('ppdb_peserta_tes')
                     ->where('id_jadwal', $data['id_jadwal'])
                     ->where('id_pendaftar', $data['id_pendaftar'])
                     ->get()->getRowArray();

        if ($exists) {
            return $this->response->setStatusCode(409)->setJSON(['status' => 409, 'message' => 'Pendaftar sudah terdaftar di jadwal ini']);
        }

        $db->table('ppdb_peserta_tes')->insert([
            'id_jadwal'    => $data['id_jadwal'],
            'id_pendaftar' => $data['id_pendaftar'],
        ]);

        log_activity('Tambah Peserta Tes (API)', 'PPDB', 'Jadwal ID: ' . $data['id_jadwal']);
        return $this->response->setJSON(['status' => 200, 'message' => 'Peserta berhasil ditambahkan ke jadwal']);
    }

    public function removePeserta($id)
    {
        helper('activity');
        $db = \Config\Database::connect();
        $db->table('ppdb_peserta_tes')->where('id', $id)->delete();
        log_activity('Hapus Peserta Tes (API)', 'PPDB');
        return $this->response->setJSON(['status' => 200, 'message' => 'Peserta berhasil dihapus dari jadwal']);
    }

    public function updateKehadiran()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $db   = \Config\Database::connect();

        $db->table('ppdb_peserta_tes')->where('id', $data['id'])->update([
            'kehadiran' => $data['kehadiran'],
            'nilai'     => $data['nilai'] ?? null,
        ]);

        return $this->response->setJSON(['status' => 200, 'message' => 'Kehadiran/nilai berhasil diperbarui']);
    }

    // ===================== BERKAS =====================

    public function indexSyarat()
    {
        $db = \Config\Database::connect();
        $syarat = $db->table('ppdb_syarat_berkas')->orderBy('id', 'ASC')->get()->getResultArray();
        return $this->response->setJSON(['status' => 200, 'data' => $syarat]);
    }

    public function saveSyarat()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $db   = \Config\Database::connect();

        $db->table('ppdb_syarat_berkas')->insert([
            'nama_berkas' => $data['nama_berkas'],
            'is_wajib'    => $data['is_wajib'] ?? 0,
        ]);

        log_activity('Tambah Syarat Berkas (API)', 'PPDB', 'Nama: ' . $data['nama_berkas']);
        return $this->response->setJSON(['status' => 200, 'message' => 'Syarat berkas berhasil ditambahkan']);
    }

    public function deleteSyarat($id)
    {
        helper('activity');
        $db = \Config\Database::connect();
        $db->table('ppdb_syarat_berkas')->where('id', $id)->delete();
        log_activity('Hapus Syarat Berkas (API)', 'PPDB');
        return $this->response->setJSON(['status' => 200, 'message' => 'Syarat berkas berhasil dihapus']);
    }

    public function berkasPendaftar($id_pendaftar)
    {
        $db     = \Config\Database::connect();
        $syarat = $db->table('ppdb_syarat_berkas')->orderBy('id', 'ASC')->get()->getResultArray();
        $uploaded = $db->table('pendaftar_berkas')
                       ->where('id_pendaftar', $id_pendaftar)
                       ->get()->getResultArray();

        // Map status per jenis berkas
        $statusMap = [];
        foreach ($uploaded as $u) {
            $statusMap[$u['jenis_berkas']] = ['id' => $u['id'], 'status' => $u['status']];
        }

        $result = [];
        foreach ($syarat as $s) {
            $result[] = [
                'id_syarat'   => $s['id'],
                'nama_berkas' => $s['nama_berkas'],
                'is_wajib'    => $s['is_wajib'],
                'id_berkas'   => $statusMap[$s['nama_berkas']]['id'] ?? null,
                'status'      => $statusMap[$s['nama_berkas']]['status'] ?? 'Belum Ada',
            ];
        }

        return $this->response->setJSON(['status' => 200, 'data' => $result]);
    }

    public function updateBerkas()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $db   = \Config\Database::connect();

        if (!empty($data['id_berkas'])) {
            $db->table('pendaftar_berkas')->where('id', $data['id_berkas'])->update(['status' => $data['status']]);
        } else {
            $db->table('pendaftar_berkas')->insert([
                'id_pendaftar' => $data['id_pendaftar'],
                'jenis_berkas' => $data['jenis_berkas'],
                'status'       => $data['status'],
            ]);
        }

        log_activity('Update Checklist Berkas (API)', 'PPDB', 'Pendaftar ID: ' . $data['id_pendaftar'] . ' - ' . $data['jenis_berkas'] . ': ' . $data['status']);
        return $this->response->setJSON(['status' => 200, 'message' => 'Status berkas berhasil diperbarui']);
    }

    // ===================== PEMBAYARAN =====================

    public function pembayaranPendaftar($id_pendaftar)
    {
        $db = \Config\Database::connect();
        $pembayaran = $db->table('ppdb_pembayaran')
                         ->where('id_pendaftar', $id_pendaftar)
                         ->orderBy('tanggal_bayar', 'DESC')
                         ->get()->getResultArray();

        return $this->response->setJSON(['status' => 200, 'data' => $pembayaran]);
    }

    public function savePembayaran()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        $db   = \Config\Database::connect();

        $pembayaranModel = new PpdbPembayaranModel();
        $kwitansi = $pembayaranModel->generateKwitansi();

        $db->table('ppdb_pembayaran')->insert([
            'id_pendaftar'   => $data['id_pendaftar'],
            'nomor_kwitansi' => $kwitansi,
            'jumlah'         => $data['jumlah'],
            'tanggal_bayar'  => $data['tanggal_bayar'] ?? date('Y-m-d'),
            'metode_bayar'   => $data['metode_bayar'] ?? 'Tunai',
            'keterangan'     => $data['keterangan'] ?? null,
        ]);

        log_activity('Mencatat Pembayaran PPDB (API)', 'PPDB', 'Kwitansi: ' . $kwitansi);
        return $this->response->setJSON(['status' => 200, 'message' => 'Pembayaran dicatat. No Kwitansi: ' . $kwitansi, 'kwitansi' => $kwitansi]);
    }

    // ===================== STATS =====================

    public function stats()
    {
        $db = \Config\Database::connect();
        $stats = [
            'total'       => $db->table('ppdb_pendaftar')->countAll(),
            'pending'     => $db->table('ppdb_pendaftar')->where('status_seleksi', 'Pending')->countAllResults(),
            'lulus'       => $db->table('ppdb_pendaftar')->where('status_seleksi', 'Lulus')->countAllResults(),
            'tidak_lulus' => $db->table('ppdb_pendaftar')->where('status_seleksi', 'Tidak Lulus')->countAllResults(),
            'terdaftar'   => $db->table('ppdb_pendaftar')->where('status_seleksi', 'Santri Terdaftar')->countAllResults(),
            'total_jadwal'=> $db->table('ppdb_jadwal_tes')->countAll(),
            'total_berkas_pending' => $db->table('pendaftar_berkas')->where('status', 'Belum Ada')->countAllResults(),
        ];

        return $this->response->setJSON(['status' => 200, 'data' => $stats]);
    }

    // ===================== REFERENSI =====================

    public function listTahunAjaran()
    {
        $db  = \Config\Database::connect();
        $data = $db->table('tahun_ajaran')->orderBy('tahun_ajaran', 'DESC')->get()->getResultArray();
        return $this->response->setJSON(['status' => 200, 'data' => $data]);
    }
}
