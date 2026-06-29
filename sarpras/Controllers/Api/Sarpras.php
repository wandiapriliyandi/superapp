<?php

namespace Sarpras\Controllers\Api;

use CodeIgniter\Controller;
use Sarpras\Models\BarangModel;
use Sarpras\Models\MutasiModel;
use Sarpras\Models\PeminjamanModel;

class Sarpras extends Controller
{
    protected $barangModel;
    protected $mutasiModel;
    protected $peminjamanModel;

    public function __construct()
    {
        $this->barangModel     = new BarangModel();
        $this->mutasiModel     = new MutasiModel();
        $this->peminjamanModel = new PeminjamanModel();
    }

    // ===================== MASTER BARANG =====================

    public function indexBarang()
    {
        $barang = $this->barangModel->orderBy('nama_barang', 'ASC')->findAll();
        return $this->response->setJSON([
            'status' => 200,
            'data'   => $barang
        ]);
    }

    public function saveBarang()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $id         = !empty($data['id']) ? $data['id'] : null;
        $kodeBarang = $data['kode_barang'] ?? '';
        $namaBarang = $data['nama_barang'] ?? '';
        $kategori   = $data['kategori'] ?? '';
        $satuan     = $data['satuan'] ?? 'Pcs';
        $lokasi     = $data['lokasi'] ?? '';
        $kondisi    = $data['kondisi'] ?? 'Baik';
        $stokAwal   = (int) ($data['stok_awal'] ?? 0);

        if (empty($namaBarang)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Nama barang wajib diisi']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $payload = [
            'id'          => $id,
            'kode_barang' => $kodeBarang ?: null,
            'nama_barang' => $namaBarang,
            'kategori'    => $kategori,
            'satuan'      => $satuan,
            'lokasi'      => $lokasi,
            'kondisi'     => $kondisi,
        ];

        if ($id) {
            $this->barangModel->update($id, $payload);
            $aksi = 'Mengubah';
        } else {
            $payload['stok'] = 0; // Mulai dari 0, lalu ditambah stok awal
            $newId = $this->barangModel->insert($payload);
            $id = $newId;
            $aksi = 'Menambah';

            if ($stokAwal > 0) {
                $resMutasi = $this->mutasiModel->catat(
                    $id,
                    $stokAwal,
                    'masuk',
                    'Stok awal pendaftaran barang inventaris baru',
                    1 // Default Admin
                );

                if (!$resMutasi['ok']) {
                    $db->transRollback();
                    return $this->response->setStatusCode(400)->setJSON(['status' => 400, 'message' => $resMutasi['message']]);
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 500, 'message' => 'Gagal menyimpan barang inventaris']);
        }

        log_activity($aksi . ' Barang Inventaris (API)', 'Sarpras', 'Barang: ' . $namaBarang . ', Stok Awal: ' . $stokAwal);
        return $this->response->setJSON(['status' => 200, 'message' => 'Barang inventaris berhasil disimpan']);
    }

    public function deleteBarang($id)
    {
        helper('activity');
        $barang = $this->barangModel->find($id);
        if (!$barang) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Barang tidak ditemukan']);
        }

        $this->barangModel->delete($id);
        log_activity('Menghapus Barang Inventaris (API)', 'Sarpras', 'Barang: ' . $barang['nama_barang']);

        return $this->response->setJSON(['status' => 200, 'message' => 'Barang inventaris berhasil dihapus']);
    }

    // ===================== MUTASI STOK =====================

    public function indexMutasi()
    {
        $db = \Config\Database::connect();
        $mutasi = $db->table('inv_mutasi')
            ->select('inv_mutasi.*, inv_barang.nama_barang, inv_barang.kode_barang, inv_barang.satuan')
            ->join('inv_barang', 'inv_barang.id = inv_mutasi.barang_id')
            ->orderBy('inv_mutasi.created_at', 'DESC')
            ->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 200,
            'data'   => $mutasi
        ]);
    }

    public function saveMutasi()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $barangId   = (int) ($data['barang_id'] ?? 0);
        $jumlah     = (int) ($data['jumlah'] ?? 0);
        $tipe       = $data['tipe'] ?? ''; // masuk, keluar
        $keterangan = $data['keterangan'] ?? '';

        if (!$barangId || !$jumlah || !$tipe) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Barang, jumlah, dan tipe mutasi wajib diisi']);
        }

        $res = $this->mutasiModel->catat($barangId, $jumlah, $tipe, $keterangan, 1);
        if (!$res['ok']) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 400, 'message' => $res['message']]);
        }

        log_activity('Mencatat Mutasi Barang (API)', 'Sarpras', 'Barang ID: ' . $barangId . ', Tipe: ' . $tipe . ', Qty: ' . $jumlah);
        return $this->response->setJSON(['status' => 200, 'message' => 'Mutasi stok barang berhasil dicatat']);
    }

    // ===================== PEMINJAMAN =====================

    public function indexPeminjaman()
    {
        $peminjaman = $this->peminjamanModel->getPeminjaman();
        return $this->response->setJSON([
            'status' => 200,
            'data'   => $peminjaman
        ]);
    }

    public function savePeminjaman()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $barangId          = (int) ($data['barang_id'] ?? 0);
        $peminjamNama      = $data['peminjam_nama'] ?? '';
        $peminjamTipe      = $data['peminjam_tipe'] ?? 'Santri';
        $jumlah            = (int) ($data['jumlah'] ?? 1);
        $tglPinjam         = $data['tgl_pinjam'] ?? '';
        $tglKembaliRencana = $data['tgl_kembali_rencana'] ?? '';
        $keterangan        = $data['keterangan'] ?? '';

        if (!$barangId || empty($peminjamNama) || empty($tglPinjam)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Barang, nama peminjam, dan tanggal pinjam wajib diisi']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Kurangi stok barang via mutasi
        $resMutasi = $this->mutasiModel->catat(
            $barangId,
            $jumlah,
            'keluar',
            'Dipinjam oleh ' . $peminjamNama . ' (' . $peminjamTipe . ')',
            1,
            false
        );

        if (!$resMutasi['ok']) {
            $db->transRollback();
            return $this->response->setStatusCode(400)->setJSON(['status' => 400, 'message' => $resMutasi['message']]);
        }

        // 2. Simpan record peminjaman
        $this->peminjamanModel->insert([
            'barang_id'           => $barangId,
            'peminjam_nama'       => $peminjamNama,
            'peminjam_tipe'       => $peminjamTipe,
            'jumlah'              => $jumlah,
            'tgl_pinjam'          => $tglPinjam,
            'tgl_kembali_rencana' => $tglKembaliRencana ?: null,
            'status'              => 'Dipinjam',
            'keterangan'          => $keterangan,
            'petugas_id'          => 1
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 500, 'message' => 'Gagal mencatat peminjaman']);
        }

        log_activity('Peminjaman Barang Inventaris (API)', 'Sarpras', 'Peminjam: ' . $peminjamNama . ', Qty: ' . $jumlah);
        return $this->response->setJSON(['status' => 200, 'message' => 'Peminjaman barang berhasil dicatat!']);
    }

    public function kembalikanPeminjaman($id)
    {
        helper('activity');
        $peminjaman = $this->peminjamanModel->find($id);
        if (!$peminjaman) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Data peminjaman tidak ditemukan']);
        }

        if ($peminjaman['status'] === 'Kembali') {
            return $this->response->setStatusCode(400)->setJSON(['status' => 400, 'message' => 'Barang sudah dikembalikan sebelumnya']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Tambah kembali stok barang via mutasi
        $resMutasi = $this->mutasiModel->catat(
            (int) $peminjaman['barang_id'],
            (int) $peminjaman['jumlah'],
            'masuk',
            'Pengembalian dari ' . $peminjaman['peminjam_nama'],
            1,
            false
        );

        if (!$resMutasi['ok']) {
            $db->transRollback();
            return $this->response->setStatusCode(400)->setJSON(['status' => 400, 'message' => $resMutasi['message']]);
        }

        // 2. Update status peminjaman
        $this->peminjamanModel->update($id, [
            'status'                => 'Kembali',
            'tgl_kembali_realisasi' => date('Y-m-d H:i:s'),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 500, 'message' => 'Gagal mencatat pengembalian barang']);
        }

        log_activity('Pengembalian Barang Inventaris (API)', 'Sarpras', 'Peminjam: ' . $peminjaman['peminjam_nama'] . ', Qty: ' . $peminjaman['jumlah']);
        return $this->response->setJSON(['status' => 200, 'message' => 'Barang berhasil dikembalikan!']);
    }

    public function deletePeminjaman($id)
    {
        helper('activity');
        $peminjaman = $this->peminjamanModel->find($id);
        if (!$peminjaman) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Data peminjaman tidak ditemukan']);
        }

        // Jika didelete tapi belum kembali, kembalikan stoknya secara paksa/otomatis
        $db = \Config\Database::connect();
        $db->transStart();

        if ($peminjaman['status'] === 'Dipinjam') {
            $this->mutasiModel->catat(
                (int) $peminjaman['barang_id'],
                (int) $peminjaman['jumlah'],
                'masuk',
                'Pembatalan/Hapus rekam peminjaman ' . $peminjaman['peminjam_nama'],
                1,
                false
            );
        }

        $this->peminjamanModel->delete($id);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 500, 'message' => 'Gagal menghapus data peminjaman']);
        }

        log_activity('Menghapus Rekam Peminjaman (API)', 'Sarpras', 'Peminjam: ' . $peminjaman['peminjam_nama']);
        return $this->response->setJSON(['status' => 200, 'message' => 'Rekam peminjaman berhasil dihapus']);
    }
}
