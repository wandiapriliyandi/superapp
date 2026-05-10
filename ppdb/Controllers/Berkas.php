<?php

namespace PPDB\Controllers;

use App\Controllers\BaseController;
use App\Models\SyaratBerkasModel;
use App\Models\PendaftarBerkasModel;
use Ppdb\Models\PendaftarModel;
use App\Traits\Indexable;

class Berkas extends BaseController
{
    use Indexable;

    protected $syaratModel;
    protected $berkasModel;
    protected $pendaftarModel;

    public function __construct()
    {
        $this->syaratModel = new SyaratBerkasModel();
        $this->berkasModel = new PendaftarBerkasModel();
        $this->pendaftarModel = new PendaftarModel();
    }

    // --- PENGATURAN MASTER BERKAS ---
    public function index()
    {
        return view('Ppdb\Views\berkas\index', [
            'title'  => 'Pengaturan Syarat Berkas',
            'syarat' => $this->syaratModel->findAll()
        ]);
    }

    public function syarat_save()
    {
        $this->syaratModel->save([
            'nama_berkas' => $this->request->getPost('nama_berkas'),
            'is_wajib'    => $this->request->getPost('is_wajib') ?? 0
        ]);
        log_activity('Menambah Syarat Berkas', 'PPDB');
        return redirect()->back()->with('success', 'Syarat berkas berhasil ditambahkan.');
    }

    public function syarat_delete($id)
    {
        $this->syaratModel->delete($id);
        log_activity('Menghapus Syarat Berkas', 'PPDB');
        return redirect()->back()->with('success', 'Syarat berkas berhasil dihapus.');
    }

    // --- VERIFIKASI BERKAS PENDAFTAR ---
    public function verifikasi()
    {
        return $this->renderIndex(
            $this->pendaftarModel,
            [], // Filter dinamis (kosong)
            'Ppdb\Views\berkas\verifikasi',
            'Verifikasi Berkas Pendaftar (Lulus)',
            'pendaftar',
            [], // Extra data
            ['status_seleksi' => 'Lulus'] // Filter permanen (Hardcoded)
        );
    }

    public function detail($id_pendaftar)
    {
        $pendaftar = $this->pendaftarModel->find($id_pendaftar);
        $syarat = $this->syaratModel->findAll();
        
        // Ambil berkas yang sudah diupload
        $uploaded = $this->berkasModel->where('id_pendaftar', $id_pendaftar)->findAll();
        
        return view('Ppdb\Views\berkas\detail', [
            'title'     => 'Detail Verifikasi Berkas',
            'p'         => $pendaftar,
            'syarat'    => $syarat,
            'uploaded'  => $uploaded
        ]);
    }

    public function update_status()
    {
        $id = $this->request->getPost('id_berkas');
        $id_pendaftar = $this->request->getPost('id_pendaftar');
        $jenis_berkas = $this->request->getPost('jenis_berkas');
        $status = $this->request->getPost('status');

        if ($id) {
            $this->berkasModel->update($id, [
                'status'  => $status
            ]);
        } else {
            $this->berkasModel->insert([
                'id_pendaftar' => $id_pendaftar,
                'jenis_berkas' => $jenis_berkas,
                'status'       => $status
            ]);
        }

        log_activity('Update Checklist Berkas', 'PPDB', 'Pendaftar ID: ' . $id_pendaftar . ' - ' . $jenis_berkas . ': ' . $status);
        return redirect()->back()->with('success', 'Checklist berkas berhasil diperbarui.');
    }
}
