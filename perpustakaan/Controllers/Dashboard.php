<?php

namespace Perpustakaan\Controllers;

use App\Controllers\BaseController;
use Perpustakaan\Models\BukuModel;

class Dashboard extends BaseController
{
    protected $bukuModel;

    public function __construct()
    {
        $this->bukuModel = new BukuModel();
    }

    public function index()
    {
        $driveService = new \App\Services\GoogleDriveService();
        $data = [
            'title' => 'Perpustakaan Digital & Fisik',
            'count_putra'   => $this->bukuModel->where('lokasi', 'Putra')->countAllResults(),
            'count_putri'   => $this->bukuModel->where('lokasi', 'Putri')->countAllResults(),
            'count_digital' => $this->bukuModel->where('lokasi', 'Digital')->countAllResults(),
            'drive_ready'   => $driveService->isConfigured()
        ];

        return view('Perpustakaan\Views\dashboard', $data);
    }

    public function list($lokasi)
    {
        $lokasi = ucfirst($lokasi);
        if (!in_array($lokasi, ['Putra', 'Putri', 'Digital'])) {
            return redirect()->to(base_url('perpustakaan'));
        }

        $data = [
            'title'  => 'Koleksi Buku ' . $lokasi,
            'lokasi' => $lokasi,
            'buku'   => $this->bukuModel->where('lokasi', $lokasi)->findAll()
        ];

        return view('Perpustakaan\Views\buku_list', $data);
    }

    public function tambah($lokasi)
    {
        $data = [
            'title'  => 'Tambah Buku ' . ucfirst($lokasi),
            'lokasi' => ucfirst($lokasi),
            'validation' => \Config\Services::validation()
        ];

        return view('Perpustakaan\Views\buku_form', $data);
    }

    public function simpan()
    {
        helper('activity');
        // Validasi
        if (!$this->validate([
            'judul' => 'required',
            'kode_buku' => 'required|is_unique[perpus_buku.kode_buku,id,{id}]',
        ])) {
            return redirect()->back()->withInput();
        }

        $lokasi = $this->request->getPost('lokasi');
        
        $data = [
            'kode_buku'    => $this->request->getPost('kode_buku'),
            'judul'        => $this->request->getPost('judul'),
            'pengarang'    => $this->request->getPost('pengarang'),
            'penerbit'     => $this->request->getPost('penerbit'),
            'tahun_terbit' => $this->request->getPost('tahun_terbit'),
            'kategori'     => $this->request->getPost('kategori'),
            'stok'         => $this->request->getPost('stok') ?? 0,
            'lokasi'       => $lokasi,
            'deskripsi'    => $this->request->getPost('deskripsi'),
            'link_eksternal' => $this->request->getPost('link_eksternal'),
        ];

        // Tandai jika link adalah Google Drive
        if ($data['link_eksternal'] && strpos($data['link_eksternal'], 'drive.google.com') !== false) {
            $data['is_drive'] = 1;
        }

        // Handle Cover Image
        $fileCover = $this->request->getFile('cover');
        if ($fileCover && $fileCover->isValid() && !$fileCover->hasMoved()) {
            $newName = $fileCover->getRandomName();
            $fileCover->move('uploads/perpus/cover', $newName);
            $data['cover'] = $newName;
        }

        // Handle Digital File
        $fileDigital = $this->request->getFile('file_digital');
        if ($fileDigital && $fileDigital->isValid() && !$fileDigital->hasMoved()) {
            $driveService = new \App\Services\GoogleDriveService();
            
            if ($driveService->isConfigured()) {
                // Upload ke Google Drive
                $tempPath = $fileDigital->getTempName();
                $driveFileId = $driveService->upload($tempPath, $fileDigital->getName());
                
                if ($driveFileId) {
                    $data['link_eksternal'] = "https://drive.google.com/file/d/{$driveFileId}/view?usp=sharing";
                    $data['is_drive'] = 1;
                    $data['file_digital'] = null; // Tidak perlu simpan lokal
                } else {
                    // Fallback ke lokal jika gagal upload ke Drive
                    $newName = $fileDigital->getRandomName();
                    $fileDigital->move('uploads/perpus/digital', $newName);
                    $data['file_digital'] = $newName;
                }
            } else {
                // Simpan lokal jika Drive belum dikonfigurasi
                $newName = $fileDigital->getRandomName();
                $fileDigital->move('uploads/perpus/digital', $newName);
                $data['file_digital'] = $newName;
            }
        }

        $this->bukuModel->save($data);

        log_activity('Menambah Koleksi Buku', 'Perpustakaan', 'Judul: ' . $data['judul'] . ', Lokasi: ' . $lokasi);

        return redirect()->to(base_url('perpustakaan/list/' . strtolower($lokasi)))->with('success', 'Buku berhasil ditambahkan');
    }

    public function hapus($id)
    {
        helper('activity');
        $buku = $this->bukuModel->find($id);
        if ($buku) {
            // Hapus file jika ada
            if ($buku['cover']) @unlink('uploads/perpus/cover/' . $buku['cover']);
            if ($buku['file_digital']) @unlink('uploads/perpus/digital/' . $buku['file_digital']);
            
            $this->bukuModel->delete($id);
            log_activity('Menghapus Koleksi Buku', 'Perpustakaan', 'Judul: ' . ($buku['judul'] ?? ''));
        }
        return redirect()->back()->with('success', 'Buku berhasil dihapus');
    }

    public function pengaturan()
    {
        $driveService = new \App\Services\GoogleDriveService();
        $data = [
            'title' => 'Pengaturan Google Drive',
            'is_configured' => $driveService->isConfigured()
        ];
        return view('Perpustakaan\Views\pengaturan', $data);
    }

    public function simpan_konfigurasi()
    {
        helper('activity');
        $file = $this->request->getFile('google_json');
        if ($file && $file->isValid()) {
            $file->move(WRITEPATH, 'google-credentials.json', true);
            log_activity('Mengubah Konfigurasi Google Drive', 'Perpustakaan');
            return redirect()->back()->with('success', 'Konfigurasi Google Drive berhasil disimpan');
        }
        return redirect()->back()->with('error', 'Gagal menyimpan konfigurasi');
    }
}
