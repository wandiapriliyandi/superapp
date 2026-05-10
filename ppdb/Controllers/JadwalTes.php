<?php

namespace PPDB\Controllers;

use App\Controllers\BaseController;
use App\Models\JadwalTesModel;
use App\Models\PesertaTesModel;
use Ppdb\Models\PendaftarModel;
use App\Traits\Indexable;

class JadwalTes extends BaseController
{
    use Indexable;

    protected $jadwalModel;
    protected $pesertaModel;
    protected $pendaftarModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalTesModel();
        $this->pesertaModel = new PesertaTesModel();
        $this->pendaftarModel = new PendaftarModel();
    }

    public function index()
    {
        return $this->renderIndex(
            $this->jadwalModel,
            ['nama_tes', 'tempat'],
            'Ppdb\Views\jadwal\index',
            'Manajemen Jadwal Tes',
            'jadwal'
        );
    }

    public function save()
    {
        $id = $this->request->getPost('id');
        $data = [
            'nama_tes' => $this->request->getPost('nama_tes'),
            'tanggal'  => $this->request->getPost('tanggal'),
            'jam'      => $this->request->getPost('jam'),
            'tempat'   => $this->request->getPost('tempat'),
            'kuota'    => $this->request->getPost('kuota')
        ];

        if ($id) {
            $this->jadwalModel->update($id, $data);
            log_activity('Update Jadwal Tes', 'PPDB', 'ID: ' . $id);
        } else {
            $this->jadwalModel->insert($data);
            log_activity('Tambah Jadwal Tes', 'PPDB');
        }

        return redirect()->to(base_url('ppdb/jadwal'))->with('success', 'Jadwal tes berhasil disimpan.');
    }

    public function delete($id)
    {
        $this->jadwalModel->delete($id);
        log_activity('Hapus Jadwal Tes', 'PPDB', 'ID: ' . $id);
        return redirect()->back()->with('success', 'Jadwal tes berhasil dihapus.');
    }

    // --- MANAJEMEN PESERTA ---
    public function detail($id_jadwal)
    {
        $jadwal = $this->jadwalModel->find($id_jadwal);
        
        // Ambil semua pendaftar yang statusnya masih Pending
        $pendaftar = $this->pendaftarModel->where('status_seleksi', 'Pending')->findAll();
        
        // Ambil mapping peserta yang sudah masuk jadwal ini
        $peserta = $this->pesertaModel->where('id_jadwal', $id_jadwal)->findAll();
        $mapping = [];
        foreach ($peserta as $p) {
            $mapping[$p['id_pendaftar']] = $p['id'];
        }

        return view('Ppdb\Views\jadwal\detail', [
            'title'     => 'Plotting Peserta Tes',
            'j'         => $jadwal,
            'pendaftar' => $pendaftar,
            'mapping'   => $mapping
        ]);
    }

    public function add_peserta()
    {
        $id_jadwal = $this->request->getPost('id_jadwal');
        $id_pendaftar = $this->request->getPost('id_pendaftar');

        // Cek apakah sudah terdaftar di jadwal ini
        $exists = $this->pesertaModel->where('id_jadwal', $id_jadwal)
                                    ->where('id_pendaftar', $id_pendaftar)
                                    ->first();

        if ($exists) {
            return redirect()->back()->with('error', 'Santri sudah terdaftar di jadwal ini.');
        }

        $this->pesertaModel->insert([
            'id_jadwal'    => $id_jadwal,
            'id_pendaftar' => $id_pendaftar
        ]);

        log_activity('Tambah Peserta Tes', 'PPDB', 'Jadwal ID: ' . $id_jadwal);
        return redirect()->back()->with('success', 'Peserta berhasil ditambahkan.');
    }

    public function remove_peserta($id)
    {
        $this->pesertaModel->delete($id);
        log_activity('Hapus Peserta Tes', 'PPDB');
        return redirect()->back()->with('success', 'Peserta berhasil dihapus dari jadwal.');
    }

    public function update_kehadiran()
    {
        $id = $this->request->getPost('id');
        $kehadiran = $this->request->getPost('kehadiran');
        $nilai = $this->request->getPost('nilai');

        $this->pesertaModel->update($id, [
            'kehadiran' => $kehadiran,
            'nilai'     => $nilai
        ]);

        return redirect()->back()->with('success', 'Data kehadiran/nilai berhasil diperbarui.');
    }
}
