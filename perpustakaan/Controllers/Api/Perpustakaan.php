<?php

namespace Perpustakaan\Controllers\Api;

use App\Controllers\BaseController;
use Perpustakaan\Models\BukuModel;

class Perpustakaan extends BaseController
{
    protected $bukuModel;

    public function __construct()
    {
        $this->bukuModel = new BukuModel();
    }

    public function indexBuku()
    {
        $page     = $this->request->getVar('page') ? (int) $this->request->getVar('page') : 1;
        $limit    = $this->request->getVar('limit') ? (int) $this->request->getVar('limit') : 10;
        $lokasi   = $this->request->getVar('lokasi');
        $kategori = $this->request->getVar('kategori');
        $q        = $this->request->getVar('q');

        $db = \Config\Database::connect();
        $countBuilder = $db->table('perpus_buku');

        if ($lokasi)   $countBuilder->where('lokasi', $lokasi);
        if ($kategori) $countBuilder->where('kategori', $kategori);
        if ($q) {
            $countBuilder->groupStart()
                ->like('judul', $q)
                ->orLike('pengarang', $q)
                ->orLike('kode_buku', $q)
                ->groupEnd();
        }
        $total = $countBuilder->countAllResults();

        $totalPages = ceil($total / $limit);
        $offset = ($page - 1) * $limit;

        $mainBuilder = $db->table('perpus_buku');
        if ($lokasi)   $mainBuilder->where('lokasi', $lokasi);
        if ($kategori) $mainBuilder->where('kategori', $kategori);
        if ($q) {
            $mainBuilder->groupStart()
                ->like('judul', $q)
                ->orLike('pengarang', $q)
                ->orLike('kode_buku', $q)
                ->groupEnd();
        }
        $data = $mainBuilder->orderBy('judul', 'ASC')->limit($limit, $offset)->get()->getResultArray();

        return $this->response->setJSON([
            'status'  => 200,
            'message' => 'Data buku berhasil diambil',
            'data'    => $data,
            'pagination' => [
                'total'       => $total,
                'page'        => $page,
                'limit'       => $limit,
                'total_pages' => $totalPages
            ]
        ]);
    }

    public function saveBuku()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data['judul']) || empty($data['kode_buku'])) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Judul dan kode buku wajib diisi']);
        }

        // Cek unique code
        $existing = $this->bukuModel->where('kode_buku', $data['kode_buku'])->first();
        if ($existing && (empty($data['id']) || $existing['id'] != $data['id'])) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Kode buku sudah digunakan']);
        }

        $payload = [
            'id'             => !empty($data['id']) ? $data['id'] : null,
            'kode_buku'      => $data['kode_buku'],
            'judul'          => $data['judul'],
            'pengarang'      => $data['pengarang'] ?? null,
            'penerbit'       => $data['penerbit'] ?? null,
            'tahun_terbit'   => !empty($data['tahun_terbit']) ? $data['tahun_terbit'] : null,
            'kategori'       => $data['kategori'] ?? 'Umum',
            'stok'           => !empty($data['stok']) ? (int) $data['stok'] : 0,
            'lokasi'         => $data['lokasi'] ?? 'Putra',
            'deskripsi'      => $data['deskripsi'] ?? null,
            'link_eksternal' => $data['link_eksternal'] ?? null,
            'is_drive'       => !empty($data['link_eksternal']) && strpos($data['link_eksternal'], 'drive.google.com') !== false ? 1 : 0,
            'cover'          => $data['cover'] ?? 'default.png',
        ];

        $isUpdate = !empty($data['id']);
        $this->bukuModel->save($payload);

        $aksi = $isUpdate ? 'Mengubah' : 'Menambah';
        log_activity($aksi . ' Buku Perpustakaan (API)', 'Perpustakaan', 'Judul: ' . $data['judul']);

        return $this->response->setJSON(['status' => 200, 'message' => 'Buku berhasil disimpan']);
    }

    public function deleteBuku($id)
    {
        helper('activity');
        $buku = $this->bukuModel->find($id);
        if (!$buku) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Buku tidak ditemukan']);
        }

        $this->bukuModel->delete($id);
        log_activity('Menghapus Buku Perpustakaan (API)', 'Perpustakaan', 'Judul: ' . $buku['judul']);

        return $this->response->setJSON(['status' => 200, 'message' => 'Buku berhasil dihapus']);
    }

    public function stats()
    {
        $stats = [
            'total_putra'   => $this->bukuModel->where('lokasi', 'Putra')->countAllResults(),
            'total_putri'   => $this->bukuModel->where('lokasi', 'Putri')->countAllResults(),
            'total_digital' => $this->bukuModel->where('lokasi', 'Digital')->countAllResults(),
            'total_buku'    => $this->bukuModel->countAllResults()
        ];

        return $this->response->setJSON(['status' => 200, 'data' => $stats]);
    }
}
