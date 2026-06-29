<?php

namespace Perijinan\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Perijinan\Models\PerijinanModel;
use App\Models\SantriModel;
use Exception;

class Perijinan extends BaseController
{
    use ResponseTrait;

    protected $perijinanModel;
    protected $santriModel;

    public function __construct()
    {
        $this->perijinanModel = new PerijinanModel();
        $this->santriModel = new SantriModel();
    }

    /**
     * Get all permission logs
     */
    public function index()
    {
        $perijinan = $this->perijinanModel->getIzin();
        return $this->respond([
            'status' => 200,
            'message' => 'Data perizinan berhasil diambil',
            'data' => $perijinan
        ]);
    }

    /**
     * Get all students for selection dropdown
     */
    public function santri()
    {
        $santri = $this->santriModel->orderBy('nama_lengkap', 'ASC')->findAll();
        return $this->respond([
            'status' => 200,
            'message' => 'Data santri berhasil diambil',
            'data' => $santri
        ]);
    }

    /**
     * Save new permission request
     */
    public function save()
    {
        helper('activity');
        
        $json = null;
        try {
            $json = $this->request->getJSON();
        } catch (Exception $e) {
            // Abaikan parsing error
        }

        $nisnArray = null;
        $jenisIzin = null;
        $alasan = null;
        $tanggalMulai = null;
        $tanggalSelesai = null;

        if ($json) {
            $nisnArray = $json->nisn ?? null;
            $jenisIzin = $json->jenis_izin ?? null;
            $alasan = $json->alasan ?? null;
            $tanggalMulai = $json->tanggal_mulai ?? null;
            $tanggalSelesai = $json->tanggal_selesai ?? null;
        } else {
            $nisnArray = $this->request->getPost('nisn');
            $jenisIzin = $this->request->getPost('jenis_izin');
            $alasan = $this->request->getPost('alasan');
            $tanggalMulai = $this->request->getPost('tanggal_mulai');
            $tanggalSelesai = $this->request->getPost('tanggal_selesai');
        }

        if (empty($nisnArray) || empty($jenisIzin) || empty($tanggalMulai) || empty($tanggalSelesai)) {
            return $this->fail('Parameter wajib diisi (nisn, jenis_izin, tanggal_mulai, tanggal_selesai).', 400);
        }

        if (!is_array($nisnArray)) {
            $nisnArray = [$nisnArray];
        }

        $year = date('Y', strtotime($tanggalMulai));
        $token = $this->generateToken($year);

        foreach ($nisnArray as $nisn) {
            $data = [
                'nisn'            => $nisn,
                'token'           => $token,
                'jenis_izin'      => $jenisIzin,
                'alasan'          => $alasan,
                'tanggal_mulai'   => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'status'          => 'Pending'
            ];
            $this->perijinanModel->insert($data);
        }

        // Catat aktivitas
        $user = $this->request->user ?? null;
        $namaLengkap = $user ? ($user->nama_lengkap ?? 'API User') : 'API User';
        
        session()->set(['nama_lengkap' => $namaLengkap]);
        log_activity('Mengajukan Perizinan Baru via API', 'Perizinan API', 'Token: ' . $token . ', Jumlah: ' . count($nisnArray));
        session()->remove('nama_lengkap');

        return $this->respond([
            'status' => 200,
            'message' => 'Pengajuan perizinan berhasil disimpan.',
            'data' => [
                'token' => $token,
                'jumlah_santri' => count($nisnArray)
            ]
        ]);
    }

    /**
     * Approve permission request
     */
    public function approve($ids)
    {
        helper('activity');
        $idArray = explode('-', $ids);
        
        $user = $this->request->user ?? null;
        $userId = $user ? ($user->user_id ?? 1) : 1;
        $namaLengkap = $user ? ($user->nama_lengkap ?? 'API User') : 'API User';

        foreach ($idArray as $id) {
            $this->perijinanModel->update($id, [
                'status'         => 'Disetujui',
                'disetujui_oleh' => $userId,
                'catatan_petugas' => 'Disetujui oleh admin via API pada ' . date('Y-m-d H:i:s')
            ]);
        }

        session()->set(['nama_lengkap' => $namaLengkap]);
        log_activity('Menyetujui Izin Santri via API', 'Perizinan API', 'ID Perizinan: ' . $ids);
        session()->remove('nama_lengkap');

        return $this->respond([
            'status' => 200,
            'message' => 'Perizinan berhasil disetujui.'
        ]);
    }

    /**
     * Activate permission (departed)
     */
    public function aktifkan($ids)
    {
        helper('activity');
        $idArray = explode('-', $ids);
        
        $user = $this->request->user ?? null;
        $namaLengkap = $user ? ($user->nama_lengkap ?? 'API User') : 'API User';

        foreach ($idArray as $id) {
            $this->perijinanModel->update($id, [
                'status' => 'Aktif'
            ]);
        }

        session()->set(['nama_lengkap' => $namaLengkap]);
        log_activity('Mengaktifkan Izin Santri via API', 'Perizinan API', 'ID Perizinan: ' . $ids);
        session()->remove('nama_lengkap');

        return $this->respond([
            'status' => 200,
            'message' => 'Status perizinan telah aktif (keluar).'
        ]);
    }

    /**
     * Student returned
     */
    public function kembali($ids)
    {
        helper('activity');
        $idArray = explode('-', $ids);
        
        $user = $this->request->user ?? null;
        $namaLengkap = $user ? ($user->nama_lengkap ?? 'API User') : 'API User';

        foreach ($idArray as $id) {
            $this->perijinanModel->update($id, [
                'status'        => 'Kembali',
                'waktu_kembali' => date('Y-m-d H:i:s')
            ]);
        }

        session()->set(['nama_lengkap' => $namaLengkap]);
        log_activity('Konfirmasi Santri Kembali via API', 'Perizinan API', 'ID Perizinan: ' . $ids);
        session()->remove('nama_lengkap');

        return $this->respond([
            'status' => 200,
            'message' => 'Konfirmasi santri kembali berhasil.'
        ]);
    }

    /**
     * Reject permission request
     */
    public function reject($ids)
    {
        helper('activity');
        $idArray = explode('-', $ids);
        
        $json = null;
        try {
            $json = $this->request->getJSON();
        } catch (Exception $e) {}

        $catatan = $json ? ($json->catatan ?? '') : $this->request->getPost('catatan');

        $user = $this->request->user ?? null;
        $namaLengkap = $user ? ($user->nama_lengkap ?? 'API User') : 'API User';

        foreach ($idArray as $id) {
            $this->perijinanModel->update($id, [
                'status'         => 'Ditolak',
                'catatan_petugas' => $catatan
            ]);
        }

        session()->set(['nama_lengkap' => $namaLengkap]);
        log_activity('Menolak Izin Santri via API', 'Perizinan API', 'ID Perizinan: ' . $ids . ', Alasan: ' . $catatan);
        session()->remove('nama_lengkap');

        return $this->respond([
            'status' => 200,
            'message' => 'Perizinan berhasil ditolak.'
        ]);
    }

    /**
     * Delete permission log
     */
    public function delete($ids)
    {
        helper('activity');
        $idArray = explode('-', $ids);
        
        $user = $this->request->user ?? null;
        $namaLengkap = $user ? ($user->nama_lengkap ?? 'API User') : 'API User';

        foreach ($idArray as $id) {
            $this->perijinanModel->delete($id);
        }

        session()->set(['nama_lengkap' => $namaLengkap]);
        log_activity('Menghapus Data Perizinan via API', 'Perizinan API', 'ID Perizinan: ' . $ids);
        session()->remove('nama_lengkap');

        return $this->respond([
            'status' => 200,
            'message' => 'Data perizinan berhasil dihapus.'
        ]);
    }

    /**
     * Helper to generate unique token
     */
    private function generateToken($year)
    {
        do {
            $random = rand(10000, 99999);
            $token = $year . $random;
            $exists = $this->perijinanModel->where('token', $token)->first();
        } while ($exists);
        
        return $token;
    }
}
